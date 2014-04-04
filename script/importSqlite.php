#!/usr/bin/php
<?php

require '../vendor/autoload.php';
require '../config.php';
use RedBean_Facade as R;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

// create a log channel
$log = new Logger('cron');
if ( DEBUG ) {
	$handler = new TestHandler(Logger::DEBUG);
} else {
	$handler = new TestHandler(Logger::WARNING);
}
$log->pushHandler($handler);

if ( $config['enviroment'] == 'production' && isset($config['log']['hipchat']) ) {
	$hipchat = $config['log']['hipchat'];
	$hipchat_handler = new \Monolog\Handler\HipChatHandler($hipchat['token'], $hipchat['room'], $hipchat['name'], $hipchat['notify'], \Monolog\Logger::ERROR, $hipchat['bubble'], $hipchat['useSSL']);
	$log->pushHandler($hipchat_handler);
}

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];
 
R::addDatabase('DB1','sqlite:/Users/yoghi/Desktop/Censimento Agesci/sqlite/estrazione_capiBologna.mdb.sqlite','','',true);
R::addDatabase('DB2',$dsn,$username,$password,true);

R::selectDatabase('DB1');

$dump = array();

$listOfTables = R::inspect();
foreach ($listOfTables as $tableName) {
	
	echo "import $tableName\n";
	$fields = R::inspect($tableName);
	
	foreach ($fields as $key => $field) {
		echo $key.'->'.$field."\n";
	}

	$righe = R::getAll( 'select * from '.$tableName );

	}

}

R::selectDatabase('DB1');
$x = R::dispense($tableName);
foreach ($righe as $rowid => $value) {

//$soci = R::findAll('soci');
//print_r($soci);









