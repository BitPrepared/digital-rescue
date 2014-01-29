#!/usr/bin/php
<?php


require '../vendor/autoload.php';
require '../config.php';
use RedBean_Facade as R;

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

R::setup($dsn,$username,$password);
R::freeze(true);

$importer = new \BitPrepared\Asa\Importer();
$soci_trovati = $importer->carica('/Users/yoghi/Documents/workspace/digital-rescue/test/resources/elenco.ods');

$soci = $soci_trovati[0];
foreach ($soci as $cod_socio => $asa_socio) {
	$find = R::findOne('asa',' csocio = ? ',array($cod_socio));
	if ( null == $find ) {
		$asa = R::dispense('asa');
		foreach ($asa_socio as $key => $value) {
			$asa->$key = $value;
		}
		$id = R::store($asa);
	} else {
		// CERCA LE 7 PICCOLE DIFFERENZE e FAI UPDATE E VERSIONING
	}
}



foreach ($soci_trovati[1] as $error ) {
	echo $error."\n";
}
echo "\n";

?>