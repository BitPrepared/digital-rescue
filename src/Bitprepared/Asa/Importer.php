<?php

namespace Bitprepared\Asa;

/**
 * BitPrepared - ASA Import Bundle
 *
 * @author      Stefano Tamagnini <yoghi@sigmalab.net>
 * @copyright   2014 Stefano Tamagnini
 * @link
 * @license
 * @version
 * @package
 *
 */
class Importer
{
    /**
     * Constructor
     * @param array $userSettings Associative array of application settings
     */
    public function __construct() {}

    public function carica($filename)
    {
        $Reader = new \SpreadsheetReader_ODS($filename);
        $Reader->ChangeSheet(1);
        $numero_colonne_utili = 0;
        $Reader->next();
        $Row = $Reader->current();
        for ($i=0; $i < count($Row); $i++) {
            if ( $Row[$i] == '' ) break;
        }
        $numero_colonne_utili = $i;

        $keySet = array();
        $codici_socio_duplicati = array();
        $soci_identificati = array();
        $errori = array();

        $firstRow = true;
        $rowId = 0;
        foreach ($Reader as $Row) {
            $rowId++; //parto dalla riga 1
            if (!$firstRow) {

                $cod_socio = $Row[0];

                if ( !array_key_exists($cod_socio, $soci_identificati) ) {
                    if ( !is_numeric($cod_socio) ) {
                        $errori[] = "codice socio invalido - riga $rowId";
                    } else {
                        $asa = array();
                        for ($i=0; $i < $numero_colonne_utili; $i++) {
                            $asa[$keySet[$i]] = $Row[$i];
                        }
                        $soci_identificati[$cod_socio] = $asa;
                    }
                } else {
                    $codici_socio_duplicati[$cod_socio] = isset($codici_socio_duplicati[$cod_socio]) ? $codici_socio_duplicati[$cod_socio]+1 : 1;
                }

            } else {
                $firstRow = false;
                /* MAPPA CAMPI */
                for ($i=0; $i < $numero_colonne_utili; $i++) {
                    $keySet[$i] = $Row[$i];
                }
            }
        }

        if ( count($codici_socio_duplicati) > 0 ) $errori[] = "codici duplicati : ".json_encode($codici_socio_duplicati);
        return array($soci_identificati,$errori);

    }
}
