<?php

require '../vendor/autoload.php';
require '../config.php';
use RedBean_Facade as R;

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

R::setup($dsn,$username,$password);
R::freeze(true);

$app = new \Slim\Slim();

$app->config(array(
    //'debug' => true,
    'templates.path' => '../templates/',
    'oauth.cliendId' => 'r-index',
    'oauth.secret' => 'testpass',
    'oauth.url' => 'http://localhost:9000',
    'title' => $config['title']
));

// error reporting 
ini_set('display_errors',1);error_reporting(E_ALL);

//Enable logging
$app->log->setEnabled(true);

class ResourceNotFoundException extends Exception {}

// handle GET requests for /
$app->get('/', function () use ($app) {  

	$title = $app->config('title');

	$app->render('index.html', array(
		'title' => $title,
		'footerText' => '©2013 Autore contenuto. Design by Designer sito'
	));

});

// run
$app->run();

?>