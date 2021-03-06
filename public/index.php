<?php

define('UNIQUE_SALT', '3nR1c?2');

date_default_timezone_set('Europe/Rome');

session_cache_limiter(false);

session_start();

require '../vendor/autoload.php';
require '../config.php';

// error reporting
if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

use \stdClass;
use RedBean_Facade as R;

class ResourceNotFoundException extends Exception
{
}

$dsn = $config['db']['type'].':host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

R::setup($dsn, $username, $password);
R::freeze(true);

$log_level = \Slim\Log::WARN;
$log_enable = false;
if (isset($config['log'])) {
    $handlers = [];
    if ($config['enviroment'] == 'production' && isset($config['log']['hipchat'])) {
        $hipchat = $config['log']['hipchat'];
        $handlers[] = new \Monolog\Handler\HipChatHandler($hipchat['token'], $hipchat['room'], $hipchat['name'], $hipchat['notify'], \Monolog\Logger::INFO, $hipchat['bubble'], $hipchat['useSSL']);
    }
    $handlers[] = new \Monolog\Handler\StreamHandler($config['log']['filename']);
    $logger = new \Flynsarmy\SlimMonolog\Log\MonologWriter([
        'handlers' => $handlers,
    ]);
    switch ($config['log']['level']) {
        case 'EMERGENCY'    :
            $log_level = \Slim\Log::EMERGENCY;
            break;
        case 'ALERT'        :
            $log_level = \Slim\Log::ALERT;
            break;
        case 'CRITICAL'        :
            $log_level = \Slim\Log::CRITICAL;
            break;
        case 'ERROR'        :
            $log_level = \Slim\Log::ERROR;
            break;
        case 'WARN'            :
            $log_level = \Slim\Log::WARN;
            break;
        case 'NOTICE'        :
            $log_level = \Slim\Log::NOTICE;
            break;
        case 'INFO'            :
            $log_level = \Slim\Log::INFO;
            break;
        case 'DEBUG'        :
            $log_level = \Slim\Log::DEBUG;
            break;
        default:
            $log_level = \Slim\Log::WARN;
            break;
    }
    $log_enable = true;
}

function decodeInfoPhp()
{
    ob_start();
    phpinfo(INFO_GENERAL);
    $phpinfo = ['phpinfo' => []];
    if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            if (strlen($match[1])) {
                $phpinfo[$match[1]] = [];
            } elseif (isset($match[3])) {
                $t = array_keys($phpinfo);
                $v = end($t);
                $phpinfo[$v][$match[2]] = isset($match[4]) ? [$match[3], $match[4]] : $match[3];
            } else {
                $t = array_keys($phpinfo);
                $v = end($t);
                $phpinfo[$v][] = $match[2];
            }
        }
    }
    ob_end_clean();

    return $phpinfo;
}

$app = new \Slim\Slim([
    'mode' => $config['enviroment'],
]);

$app->config([
    'log.enabled'      => $log_enable,
    'log.level'        => $log_level,
    'log.writer'       => $logger,
    'templates.path'   => $config['template_dir'].'/'.$config['enviroment'],
    'title'            => $config['title'],
    'import'           => $config['import'],
    'cookies.lifetime' => $config['cookies.lifetime'],
    'security.salt'    => $config['security.salt'],
]);

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config([
        'debug' => false,
    ]);
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config([
        /*'oauth.cliendId' => 'r-index',
        'oauth.secret' => 'testpass',
        'oauth.url' => 'http://localhost:9000', */
        'debug' => true,
    ]);
});

$app->hook('slim.before.router', function () use ($app) {

    $req = $app->request;

    $allGetVars = $req->get();
    $allPostVars = $req->post();
    $allPutVars = $req->put();

    $vars = array_merge($allGetVars, $allPostVars);
    $vars = array_merge($vars, $allPutVars);

    $srcParam = json_encode($vars);

    //$req->getRootUri()
    $srcUrl = $req->getResourceUri();
    //Kint::dump( $srcUrl );

    \Rescue\RescueLogger::addHistory($srcUrl, $srcParam, 'slim.before.router');

});

// OAUTH autentication
// API group (Es: GET /api/asa/user/:id )
$app->group('/api', function () use ($app) {

    // Library group
    $app->group('/asa', function () use ($app) {

        // Get user with ID
        $app->get('/user/:id', function ($id) {

        });

        // Create user with ID
        $app->post('/user', function () {

        });

        // Update user with ID
        $app->put('/user/:id', function ($id) {

        });

        // Delete user with ID
        $app->delete('/user/:id', function ($id) {

        });

    });

});

// route middleware for simple API authentication
function authenticate(\Slim\Route $route)
{
    $app = \Slim\Slim::getInstance();
    $log = $app->log;

    //$token = $app->request->get('X-ACCESS-TOKEN');
    //$scope = $app->request->get('X-ACCESS-SCOPE');
    //$request->headers['AUTHORIZATION'] = $token;
    //$uid = $app->getEncryptedCookie('uid');
    //$key = $app->getEncryptedCookie('key');

    $secHash = new \BitPrepared\Security\SecureHash();

    $password = $_SERVER['HTTP_USER_AGENT'];
    $identifiedIp = \BitPrepared\Security\IpIdentifier::get_ip_address();
    $password .= $identifiedIp;
    $password .= $app->config('security.salt');

    if (isset($_SESSION['salt'])) {
        $salt = $_SESSION['salt'];
        $cookie_fingerprint = $app->getEncryptedCookie('fingerprint');
        if ('' == $cookie_fingerprint) {
            $_SESSION = [];
            session_destroy(); //logout
            header('Location: '.$_SERVER['REQUEST_URI']);
            flush();
        }
        $app->log->debug('check '.$cookie_fingerprint.' with '.$password);
        if (!$secHash->validate_hash($password, $cookie_fingerprint, $salt)) {
            echo 'Attenzione: svuotare la cache del browser prima di proseguire, anomalia individuata al suo interno.';
            exit;
            // DA GESITRE
        }
    }

    $salt = ''; //reset
    $fingerprint = $secHash->create_hash($password, $salt);
    $_SESSION['salt'] = $salt;
    $app->setEncryptedCookie('fingerprint', $fingerprint);
    $app->log->debug('new fingerprint '.$fingerprint);
}

// menu
$app->get('/menu', 'authenticate', function () use ($app) {

    $app->response->headers->set('Content-Type', 'application/json');

    $menuList = new stdClass();
    $menu = [];

    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Homepage', '/home', 'HomeController', 'partials/home.html');

    // da portare a unico controller in futuro
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Login', '/login', 'LoginController', 'partials/login.html');
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Login', '/logout', 'LogoutController', 'partials/logout.html');
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Recupera password', '/lostpassword', 'ProfileController', 'partials/lostpassword.html');

    // da portare a unico controller in futuro
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Profilo Utente', '/profilo', 'ProfileController', 'partials/profilo.html');
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Trova codice censimento', '/ricercacensimento', 'RicercaController', 'partials/ricercacensimento.html');

    // qui solo se si ha il ruolo giusto
    $menu[] = \Rescue\RescueMenuItem::createMenuItem('Carica ASA', '/upload-asa', 'AdminController', 'partials/uploadasa.html');

    $menuList->menu = $menu;

    //$menu[] = \Rescue\RescueMenuItem::createMenuItem('','/','','');
    /*
    $menu->Path = ;
    $menu->Controller = ;
    $menu->TemplateUrl = ;
    $menu->Title = ;
    */

    $app->response->setBody(json_encode($menuList));

});

// handle GET requests for /
$app->get('/', 'authenticate', function () use ($app) {

    //$log = $app->log;
    //$log->debug('called /');

    $title = $app->config('title');

    $app->render('index.html', [
        'title'      => $title,
        'baseUrl'    => $app->request->getRootUri().'/',
        'footerText' => '&copy;2014 Stefano Tamagnini - Agesci Emilia Romagna',
    ]);

});

$app->get('/location', 'authenticate', function () use ($app) {

    $app->response->headers->set('Content-Type', 'application/json');

    $location = $app->request->params('location');

    $elenco_luoghi = R::getCol('select distinct nascita from asa where nascita like "'.$location.'%" order by nascita ASC');
    foreach ($elenco_luoghi as &$value2) {
        $value2 = ucwords(strtolower($value2));
    }
    unset($value2); # remove the alias for safety reasons.
    /*
     Array
        (
            [0] => Abbiategrasso
            [1] => ..
            [2] => ..
        )
    */
    $t = new stdClass();
    $t->results = $elenco_luoghi;
    $app->response->setBody(json_encode($t));

});

$app->post('/rescue/codicecensimento', function () use ($app) {

    $app->response->headers->set('Content-Type', 'application/json');

    /*$req = $app->request();
    $name_of_the_controller = $req->post('controller');
    $name_of_the_action =  $req->post('action');*/

    $body = $app->request->getBody();

    $app->log->debug('Richiesta ricerca codicecensimento '.$body);

    $obj_request = json_decode($body);

    $nome = $obj_request->nome;
    $cognome = $obj_request->cognome;

    $datanascita = $obj_request->datanascita; //1981-09-18T23:22:51.000Z or 1981-09-18

    $datetime = new DateTime($datanascita);
    $datanascita = $datetime->format('Ymd'); //19810918
    $app->log->debug('cerco per data '.$datanascita);
    $luogonascita = $obj_request->luogonascita;

    $find = R::findOne('asa', ' nome = ? and cognome = ? and datan = ? ', [$nome, $cognome, $datanascita]);
    if ($find != null) {
        $app->log->debug('nome, cognome e datanascita validi procedo con l\'inserimento della richiesta');
        try {
            $t = new stdClass();
            $t->nome = $nome;
            $t->cognome = $cognome;
            $t->datanascita = $datanascita;
            $t->luogonascita = $luogonascita;

            $task = R::dispense('task');
            $task->arguments = json_encode($t);
            $task->created = R::isoDateTime();
            $task->updated = R::isoDateTime();
            $task->status = \Rescue\RequestStatus::QUEUE;
            $task->type = \Rescue\RequestType::SEARCH;
            $task_id = R::store($task);

            \Rescue\RescueLogger::taskLog($task_id, \Monolog\Logger::INFO, 'Created task search from '.$_SERVER['REMOTE_ADDR']);

            $app->response->setBody(json_encode(['id_richiesta' => $task_id]));
        } catch (Exception $e) {
            $app->log->error($e->getMessage());
            $app->halt(412, 'Dati invalidi'); //Precondition Failed
        }
    } else {
        $app->log->debug('nome e cognome non validi');
        $app->halt(412, 'Nome e/o Cognomi invalidi'); //Precondition Failed
    }

});

// BASIC Autentication (two factor?)

$app->post('/fileupload', 'authenticate', function () use ($app) {

    $log = $app->log;

    $import = $app->config('import');
    $upload_dir = $import['upload_dir'];

    foreach ($_FILES['uploadedFile']['error'] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['uploadedFile']['tmp_name'][$key];
            $type = $_FILES['uploadedFile']['type'][$key];

            $name = $_FILES['uploadedFile']['name'][$key];
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            //$objDateTime = new DateTime('NOW');
            //$name = "upload-".$objDateTime->format(DateTime::W3C).".".$ext;
            $name = 'upload-'.microtime(true).'.'.$ext;

            move_uploaded_file($tmp_name, "$upload_dir/$name");

            $info = new SplFileInfo("$upload_dir/$name");

            $t = new stdClass();
            $t->filename = $info->getRealPath();

            $task = R::dispense('task');
            $task->arguments = json_encode($t);
            $task->created = R::isoDateTime();
            $task->updated = R::isoDateTime();
            $task->status = \Rescue\RequestStatus::QUEUE;
            $task->type = \Rescue\RequestType::IMPORT;
            $task_id = R::store($task);

            \Rescue\RescueLogger::taskLog($task_id, \Monolog\Logger::INFO, 'Created task import from '.$_SERVER['REMOTE_ADDR']);
        } else {
            $log->error('Errore upload file : '.$key.' -> '.$error);
        }
    }

    $app->response->headers->set('Content-Type', 'text/html');
    $app->response->setBody('Upload completato con successo');

});

if (DEBUG) {
    $app->get('/version', function () use ($app) {

        $app->response->headers->set('Content-Type', 'text/html');
        $info = decodeInfoPhp();
        $app->response->setBody($info['phpinfo'][0]);

    });
}

// run
$app->run();
