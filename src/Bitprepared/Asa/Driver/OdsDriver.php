<?php
/**
 * BitPrepared - ASA Import Bundle
 *
 * @author      Stefano Tamagnini <yoghi@sigmalab.net>
 * @copyright   2014 Stefano Tamagnini
 * @date : 05/04/14 - 21:27
 * @link
 * @license
 * @version
 * @package
 */

namespace Bitprepared\Asa\Driver;

use BitPrepared\Asa\Driver\BaseDriver;
use BitPrepared\Asa\Model\ProfiloAsa;
use Monolog\Logger;

class OdsDriver extends BaseDriver
{
    private $filename;


    /**
     * Costruttore
     * @param Logger $log logger
     * @param string $filename percorso al file da caricare
     */
    public function __construct(Logger $log,string $filename)
    {
        parent::__construct($log);
        $this->filename = $filename;
    }

    /**
     * @return array      { soci_identificati , errori }
     * @throws \Exception
     */
    public function carica()
    {
        if ( !file_exists($this->filename) ) {
            throw new \Exception('Filename invalido '.$this->filename);
        }

        $Reader = new \SpreadsheetReader_ODS($this->filename);
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

        $firstRow = true;
        $rowId = 0;
        foreach ($Reader as $Row) {
            $rowId++; //parto dalla riga 1
            if (!$firstRow) {

                $cod_socio = $Row[0];

                if ( !array_key_exists($cod_socio, $soci_identificati) ) {
                    if ( !is_numeric($cod_socio) ) {
                        $this->addErrore("codice socio invalido - riga $rowId");
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
                $csocio_found = false;
                for ($i=0; $i < $numero_colonne_utili; $i++) {
                    $keySet[$i] = $Row[$i];
                    if ( trim($Row[$i]) == 'CSOCIO' ) $csocio_found = true;
                }
                if (!$csocio_found) throw new \Exception("Prima riga manca la intestazione/colonna del CSOCIO");

            }
        }

        if ( count($codici_socio_duplicati) > 0 ) $this->addErrore("codici duplicati : ".json_encode($codici_socio_duplicati));

        $profili = array();
        foreach ($soci_identificati as $cod_socio => $asa_info) {

            //CUN ???? TIPO ????

            $p = new ProfiloAsa();
            $p->setCsocio($cod_socio);
            $p->setGruppo($asa_info['ORD']);
            $p->setCognome($asa_info['COGNOME']);
            $p->setNome($asa_info['NOME']);
            $p->setEmail($asa_info['NUMERO']);
            $p->setIndirizzo($asa_info['INDIRIZZO']);
            $p->setCap($asa_info['CAP']);
            $p->setResidenza($asa_info['RESIDENZA']);
            $p->setProv($asa_info['PROV']);
            $p->setDatanascita($asa_info['DATAN']);
            $p->setLuogonascita($asa_info['NASCITA']);
            $p->setFoca($asa_info['FOCA']);
            $profili[$cod_socio] = $p;
        }

        $this->setSociAsa($profili);
    }

}
