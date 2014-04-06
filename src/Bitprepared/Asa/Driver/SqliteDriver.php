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

use Egulias\EmailValidator\EmailValidator;
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
    }

    public function carica()
    {

        $this->log->addInfo('Caricato database DBsqlite '.$this->filename);
        R::addDatabase('DBsqlite','sqlite:'.$this->filename,'','',true);
        R::freeze(true);

        $dump = array();

        R::selectDatabase('DBsqlite');
        $listOfTables = R::inspect();
        foreach ($listOfTables as $tableName) {
            $this->log->addDebug( "import $tableName\n of DBsqlite" );
            $fields = R::inspect($tableName);

            $fieldList = '';
            foreach ($fields as $field => $type) {
                $this->log->addDebug( $field.'['.$type.']'."\n" );
                $fieldList .= $field.',';
            }
            $fieldList = substr($fieldList, 0, -1);
            $this->log->addDebug("Fields ".$fieldList);

            $righe = R::getAll( 'select '.$fieldList.' from '.$tableName );
            $this->log->addDebug('Trovate '.count($righe).' righe');

            if ( isset($dump[$tableName]) ) {
                $righe = array_merge($dump[$tableName],$righe);
            }

            $dump[$tableName] = $righe;
        }

        R::selectDatabase('default');
        R::ext( 'prefix', array('RedBean_Prefix', 'prefix') );
        // il prefix si ha sia sulla tabella che sulle colonne
        $prefix = 'asa_';
        R::prefix($prefix);
        R::freeze(false);
        foreach ($dump as $tableName => $righe) {

            $tableName = str_replace('_', '', $tableName);
            $tableName = strtolower($tableName);

            $this->log->addInfo('Verso tabella '.$tableName.' con '.count($righe).' righe');

            foreach ($righe as $rowid => $elenco_campi) {

                $query = '';
                foreach ($elenco_campi as $key => $value) {
                    $query .= $prefix.$key .' = \''. addslashes($value) .'\' AND ';
                }
                $query = substr($query, 0, -4);

                //SELECT * FROM `asa_recapiti`  WHERE asa_CSOCIO = "1354314" AND asa_NUMERO = "C/O Seminario Vescovile "Don Benzi" - Via Covignan" AND asa_TIPO = "A"
                $book  = R::findOne( $tableName, $query);
                if ( $book == null ) {
                    $x = R::dispense($tableName);
                    //se metto l'id pensa automaticamente che sia un update... -.-
                    foreach ($elenco_campi as $key => $value) {
                        $x->$key = $value;
                    }
                    R::store($x);
                    $this->log->addDebug('Inserito nuovo elemento');
                } else {
                    $this->log->addDebug('Elemento gia presente');
                }

            }

        }

        $validator = new EmailValidator;
        $profili = array();
        $elenco = R::getAll( 'select asa_csocio,asa_cognome,asa_nome,asa_indirizzo,asa_residenza,asa_datan,asa_nascita,asa_cap,asa_prov,asa_ord,asa_foca from asa_soci' );
        foreach ($elenco as $row => $asa_info) {

            //CUN ???? TIPO ????
            $cod_socio = $asa_info['asa_csocio'];

            $p = new ProfiloAsa();
            $p->setCsocio($cod_socio);

            //R::getRow('select asa_nome from asa_regionisys',array($asa_info['asa_ord']));

            $recapiti = R::getAll('select asa_numero,asa_tipo from asa_recapiti where asa_csocio = ?',array($cod_socio));
            $contacts = array();

            $email_found = false;
            foreach($recapiti as $recapito){
                if ( $recapito['asa_tipo'] == 'E' ){

                    $email = $recapito['asa_numero'];
                    if ( $validator->isValid($email) ) {
                        if ( !$email_found ) {
                            $p->setEmail($email);
                            $email_found = true;
                        } else {
                            $this->log->addWarning('Seconda email '.$email.' trovata per utente '.$cod_socio);
                        }
                    } else {
                        $this->log->addWarning('Email '.$email.' non valida - utente '.$cod_socio);
                    }
                }
                if ( $recapito['asa_tipo'] == 'B' ){
                    $cellulare = preg_replace('/\s+/', '', $recapito['asa_numero']);
                    $cellulare = str_replace('+39','',$cellulare);
                    $cellulare = str_replace('-','',$cellulare);
                    if ( is_numeric($cellulare) ) {
                        $contacts[] = $cellulare;
                    } else {
                        $this->log->addWarning('Cellulare '.$cellulare.' non valido - utente '.$cod_socio);
                    }
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
            if ( $email_found ) {
                $profili[$cod_socio] = $p;
            } else {
                $this->log->addError('Socio '.$cod_socio.' senza email');
                $this->errori[] = 'Socio '.$cod_socio.' senza email';
            }
        }

        $this->setSociAsa($profili);

        R::selectDatabase('DBsqlite'); //reset
    }
}
