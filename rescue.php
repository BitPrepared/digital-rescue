#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 05/04/14
 * Time: 18:45
 */

require 'vendor/autoload.php';
require 'config.php';

use RedBean_Facade as R;
use Monolog\Logger;
use BitPrepared\Asa\Driver\OdsDriver;
use BitPrepared\Asa\Driver\SqliteDriver;

$strict = in_array('--strict', $_SERVER['argv']);
$arguments = new \cli\Arguments(compact('strict'));

$arguments->addFlag(array('verbose', 'v'), 'Turn on verbose output');
$arguments->addFlag('version', 'Display the version');
$arguments->addFlag(array('quiet', 'q'), 'Disable all output');
$arguments->addFlag(array('help', 'h'), 'Show this help screen');

$arguments->addOption(array('import-asa','i'), array(
    'default' => getcwd().'/resources/uploads/',
    'description' => 'Setta la  directory dove importare i soci da formato ods/sqlite'));
$arguments->addOption(array('export-csv','e'), array(
    'default' => getcwd(),
    'description' => 'Setta la  directory dove esportare i soci come csv'));

$arguments->addFlag(array('update-db', 'u'), 'Abilita la sovra-scrittura su db');


$arguments->parse();
if ($arguments['help']) {
    echo $arguments->getHelpScreen();
    echo "\n\n";
}

$arguments_parsed = $arguments->getArguments();

if ( isset($arguments_parsed['verbose']) ) {
    define("VERBOSE",true);
} else {
    define("VERBOSE",false);
}

if ( isset($arguments_parsed['quiet']) ) {
    define("QUIET",true);
} else {
    define("QUIET",false);
}

// create a log channel
$log = new Logger('rescue-script');
if (VERBOSE) {
    \cli\out($arguments->asJSON() . "\n");

    if (!QUIET) {
        $handler = new \Monolog\Handler\StreamHandler('php://stdout',Logger::DEBUG);
        $log->pushHandler($handler);
    }
    $handler = new \Monolog\Handler\StreamHandler($config['log']['filename'],Logger::DEBUG);
    $log->pushHandler($handler);
} else {
    if (!QUIET) {
        $handler = new \Monolog\Handler\StreamHandler($config['log']['filename'],Logger::INFO);
        $log->pushHandler($handler);
    } else {
        $handler = new \Monolog\Handler\StreamHandler($config['log']['filename'],Logger::WARNING);
        $log->pushHandler($handler);
    }
}

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

// setto il database di default
R::setup($dsn,$username,$password);

if ( isset($arguments_parsed['import-asa']) ) {

    if ('' == $arguments_parsed['import-asa']) {
        $arguments_parsed['import-asa'] = getcwd().'/resources/uploads/';
    }

    $dir = $arguments_parsed['import-asa'];

    if ( !is_dir($dir) ) {
        \cli\out('invalid import directory : '.$dir . "\n");
        exit -1;
    }

    $log->addInfo('Inizio scansione directory '.$dir);

    $drivers = array();
    $filesSqlite = scandir($dir);
    foreach ($filesSqlite as $filename) {
        if ($filename != "." && $filename != "..") {
            $fullpath = $dir.'/'.$filename;
            $pos = strpos($filename, '.sqlite');
            if ($pos > 0) {
                $drivers[] = new SqliteDriver($log,$fullpath);
            } else {
                $pos = strpos($filename, '.ods');
                if ($pos > 0) {
                    $drivers[] = new OdsDriver($log,$fullpath);
                }
            }
        }
    }

    $log->addInfo('Trovati '.count($drivers).' driver da importare');

    $R = new RedBean_Facade();
    foreach ($drivers as $driver) {
        try {
            $importer = new \BitPrepared\Asa\Importer($driver,$log,$R);

            if ( isset($arguments_parsed['update-db']) ) {
                $log->addInfo('Abilitata la sovra-scrittura su database');
                $importer->setUpdate(true);
            }

            $importer->load();
            $importer->writeOnDb();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $log->addError($message);
            $log->addError($e->getTraceAsString());
        }
    }

}

if ( isset($arguments_parsed['export-csv']) ) {

    if ('' == $arguments_parsed['export-csv']) {
        $arguments_parsed['export-csv'] = getcwd();
    }

    echo 'export : '.$arguments_parsed['export-csv'];
}
