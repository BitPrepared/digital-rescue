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
use RedBean_Facade as R;

class SqliteDriver extends BaseDriver
{

    private $filename;

    public function __construct($log,$filename)
    {
        parent::__construct($log);
        $this->filename = $filename;
        $this->errori = array();
        R::addDatabase('DBsqlite','sqlite:'.$filename,'','',true);
        R::freeze(true);

    }

    public function carica()
    {
        $dump = array();

        R::selectDatabase('DBsqlite');
        $listOfTables = R::inspect();
        foreach ($listOfTables as $tableName) {
            $this->log->addInfo( "import $tableName\n of DBsqlite" );
            $fields = R::inspect($tableName);

            $fieldList = '';
            foreach ($fields as $field => $type) {
                $this->log->addDebug( $field.'['.$type.']'."\n" );
                $fieldList .= $field.',';
            }
            $fieldList = substr($fieldList, 0, -1);
            $this->log->addInfo("Fields ".$fieldList);

            $righe = R::getAll( 'select '.$fieldList.' from '.$tableName );
            $this->log->addInfo('Trovate '.count($righe).' righe');

            if ( isset($dump[$tableName]) ) {
                $righe = array_merge($dump[$tableName],$righe);
            }

            $dump[$tableName] = $righe;
        }

        R::selectDatabase('default');
        R::ext( 'prefix', array('RedBean_Prefix', 'prefix') );
        $prefix = 'asa_';
        R::prefix($prefix);
        R::freeze(false);
        foreach ($dump as $tableName => $righe) {

            $tableName = str_replace('_', '', $tableName);
            $tableName = strtolower($tableName);

            $this->log->addInfo('Tabella '.$tableName.' con '.count($righe).' righe');

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
                foreach ($elenco_campi as $key => $value) {
                    $x->$key = $value;
                }
                R::store($x);
                //$log->addInfo("Inserito ".$rowid);

            }

        }

        R::selectDatabase('DBsqlite');

        $profili = array();
        $elenco = R::getAll( 'select asa_csocio,asa_cognome,asa_nome,asa_indirizzo,asa_residenza,asa_datan,asa_nascita,asa_cap,asa_prov,asa_ord,asa_foca from asa_soci' );
        foreach ($elenco as $row => $asa_info) {

            //CUN ???? TIPO ????
            $cod_socio = $asa_info['asa_csocio'];

            $p = new ProfiloAsa();
            $p->setCsocio($cod_socio);

            //R::getRow('select asa_nome from asa_regionisys',array($asa_info['asa_ord']));

            $recapiti = R::getAll('select asa_numero from asa_recapiti where asa_csocio = ?',array($asa_info['asa_csocio']));
            $contacts = array();
            foreach($recapiti as $recapito){
                if ( $recapito['asa_tipo'] == 'E' ){
                    $p->setEmail($recapito['asa_numero']);
                }
                if ( $recapito['asa_tipo'] == 'B' ){
                    $contacts[] = $recapito['asa_numero'];
                }
            }

            $p->setContacts($contacts);
            $p->setGruppo($asa_info['asa_ord']);
            $p->setCognome($asa_info['asa_cognome']);
            $p->setNome($asa_info['asa_nome']);
            $p->setIndirizzo($asa_info['asa_indirizzo']);
            $p->setCap($asa_info['asa_cap']);
            $p->setResidenza($asa_info['asa_residenza']);
            $p->setProv($asa_info['asa_prov']);
            $p->setDatanascita($asa_info['asa_datan']);
            $p->setLuogonascita($asa_info['asa_nascita']);
            $p->setFoca($asa_info['asa_foca']);
            $profili[$cod_socio] = $p;
        }

        $this->setSociAsa($profili);
    }
}
