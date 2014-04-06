#!/usr/bin/php
<?php

require '../vendor/autoload.php';
require '../config.php';
use RedBean_Facade as R;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

// create a log channel
$log = new Logger('importSqlite');
if ( DEBUG ) {
	$handler = new TestHandler(Logger::DEBUG);
	$log->pushHandler($handler);
	$handler = new \Monolog\Handler\StreamHandler($config['log']['filename'],Logger::DEBUG);
	$log->pushHandler($handler);
} else {
	$handler = new TestHandler(Logger::INFO);
	$log->pushHandler($handler);
	$handler = new \Monolog\Handler\StreamHandler($config['log']['filename'],Logger::INFO);
	$log->pushHandler($handler);
}

if ( $config['enviroment'] == 'production' && isset($config['log']['hipchat']) ) {
	$hipchat = $config['log']['hipchat'];
	$hipchat_handler = new \Monolog\Handler\HipChatHandler($hipchat['token'], $hipchat['room'], $hipchat['name'], $hipchat['notify'], \Monolog\Logger::ERROR, $hipchat['bubble'], $hipchat['useSSL']);
	$log->pushHandler($hipchat_handler);
}

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

// setto il database di default
R::setup($dsn,$username,$password);

$index = 0;
$dir = '/Users/yoghi/Desktop/Censimento Agesci/sqlite/';
$filesSqlite = scandir($dir);
foreach ($filesSqlite as $file) {
	if (  $file != "." && $file != ".."  ) {
		$pos = strpos($file, '.sqlite');
		if ( $pos > 0 ){
			R::addDatabase('DB'+$index,'sqlite:'.$dir.$file,'','',true);
			$log->addInfo('Aggiunto DB'+$index+' -> '.$dir.$file);
			$index++;
		}
	}
}

$dump = array();

for ($i=0; $i < $index; $i++) { 
	R::selectDatabase('DB'+$i);
	$listOfTables = R::inspect();
	foreach ($listOfTables as $tableName) {
		$log->addInfo( "import $tableName\n of DB$i" );
		$fields = R::inspect($tableName);
		
		$fieldList = '';
		foreach ($fields as $field => $type) {
			$log->addDebug( $field.'['.$type.']'."\n" );
			$fieldList .= $field.',';
		}
		$fieldList = substr($fieldList, 0, -1);
		$log->addInfo("Fields ".$fieldList);

		$righe = R::getAll( 'select '.$fieldList.' from '.$tableName );
		$log->addInfo('Trovate '.count($righe).' righe');
		
		if ( isset($dump[$tableName]) ) {
			$righe = array_merge($dump[$tableName],$righe);
		}

		$dump[$tableName] = $righe;
	}
}

R::selectDatabase('default');
R::ext( 'prefix', array('RedBean_Prefix', 'prefix') );
$prefix = 'asa_';
R::prefix($prefix);
R::freeze(false);
foreach ($dump as $tableName => $righe) {

	$tableName = str_replace('_', '', $tableName);
	$tableName = strtolower($tableName);

	$log->addInfo('Tabella '.$tableName.' con '.count($righe).' righe');

	$beans = array();
	foreach ($righe as $rowid => $elenco_campi) {

		if ( $rowid > 1 ) {
			$query = '';
			foreach ($elenco_campi as $key => $value) {
				$query .= $prefix.$key .' = \''. addslashes($value) .'\' AND ';
			}
			$query = substr($query, 0, -4);

            //SELECT * FROM `asa_recapiti`  WHERE asa_CSOCIO = "1354314" AND asa_NUMERO = "C/O Seminario Vescovile "Don Benzi" - Via Covignan" AND asa_TIPO = "A"
			$book  = R::findOne( $tableName, $query);
			if ( $book != null ) continue;
		}

		$x = R::dispense($tableName);
		//se metto l'id pensa automaticamente che sia un update... -.-
		//$beans[$rowid]->id = $rowid; 
		foreach ($elenco_campi as $key => $value) {
			$x->$key = $value;
		}
		R::store($x);
		//$log->addInfo("Inserito ".$rowid);

	}

}





