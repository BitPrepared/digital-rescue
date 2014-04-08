<?php

namespace Bitprepared\Asa;

use BitPrepared\Asa\Driver\BaseDriver;
use Monolog\Logger;
use RedBean_Facade;
use Rescue\Model\User;

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

    // -- config --

    private $log;
    private $db;

    // -- esterne --

    private $update = false;

    // -- interne --

    /**
     * Profili utenti
     * @var array
     */
    private $profili;

    public function __construct(Logger $logger,RedBean_Facade $db)
    {
        $this->log = $logger;
        $this->db = $db;
        $this->profili = array();
    }

    public function load(BaseDriver $driver)
    {
        $this->log->addInfo('Inizio caricamento da driver dei profili');
        try {
            $driver->carica();
            $this->profili = array_merge($this->profili,$driver->getProfili());
            foreach ($driver->getErrori() as $error ) {
                $this->log->addError($error);
            }
        } catch (\Exception $e) {
            $this->log->addError($e->getMessage());
            throw new \Exception('Impossibile completare l\'import');
        }
        $this->log->addInfo('Caricamento da driver dei profili terminato');
    }

    /*
    public function writeOnDb()
    {



    }
    */

    public function syncAsaToRescue(){

        $this->log->addInfo('Scrittura profili su database');
        $soci = $this->profili;
        $R = $this->db;

        $R::selectDatabase('default');
        $R::freeze(true);

        // $newUsers = array();
        foreach ($soci as $asa_socio) {
            $cod_socio = $asa_socio->getCsocio();
            $this->log->addDebug('Import del codice socio '.$cod_socio);
            try {
                $find = $R::findOne('profiles',' csocio = ? ',array($cod_socio));
                if (null == $find) {
                    $asa = $R::dispense('profiles');

                    $asa->csocio           = $cod_socio;
                    $asa->gruppo           = $asa_socio->getGruppo();
                    $asa->cognome          = $asa_socio->getCognome();
                    $asa->nome             = $asa_socio->getNome();
                    $asa->email            = $asa_socio->getEmail();
                    $asa->indirizzo        = $asa_socio->getIndirizzo();
                    $asa->cap              = $asa_socio->getCap();
                    $asa->residenza        = $asa_socio->getResidenza();
                    $asa->prov             = $asa_socio->getProv();
                    $asa->datanascita      = $asa_socio->getDatanascita();
                    $asa->luogonascita     = $asa_socio->getLuogonascita();
                    $asa->foca             = $asa_socio->getFoca();

                    $id = $R::store($asa);

                    $contacts = $asa_socio->getContacts();
                    $ids = '';
                    if ( null != $contacts ){
                        $ids = ' e con i seguenti contatti ';
                        if ( count($contacts) > 1 ){
                            $contactBeans = $R::dispense('contacts' , count($contacts) );
                            for($i = 0; $i < count($contacts); $i++){
                                $contactBeans[$i]->csocio = $cod_socio;
                                $contactBeans[$i]->telefono = $contacts[$i];
                                $contactBeans[$i]->type = 'CELLULARE';
                            }
                            foreach( $R::storeAll($contactBeans) as $singleId) {
                                $ids .= $singleId.',';
                            }
                            $ids = substr($ids, 0, -1);
                        } else {
                            $contactBean = $R::dispense('contacts');
                            $contactBean->csocio = $cod_socio;
                            $contactBean->telefono = $contacts[0];
                            $contactBean->type = 'CELLULARE';
                            $ids .= $R::store($contactBean);
                        }

                    }

                    $this->log->addInfo('Aggiunto utente '.$cod_socio.' con id '.$id.$ids);

                } else {

                    if ( $this->update ) {

                        $this->log->addInfo('Utente '.$cod_socio.' in aggiornamento');

                        //VERSIONING
                        //@Todo: fare versioning del dato precedente

                        //UPDATE
                        $find->csocio           = $cod_socio;
                        $find->gruppo           = $asa_socio->getGruppo();
                        $find->cognome          = $asa_socio->getCognome();
                        $find->nome             = $asa_socio->getNome();
                        $find->email            = $asa_socio->getEmail();
                        $find->indirizzo        = $asa_socio->getIndirizzo();
                        $find->cap              = $asa_socio->getCap();
                        $find->residenza        = $asa_socio->getResidenza();
                        $find->prov             = $asa_socio->getProv();
                        $find->datanascita      = $asa_socio->getDatanascita();
                        $find->luogonascita     = $asa_socio->getLuogonascita();
                        $find->foca             = $asa_socio->getFoca();

                        $R::store($find);

                        $contacts = $asa_socio->getContacts();
                        if ( null != $contacts ){

                            $presentContacts = $R::findAll('contacts',' csocio = ? ',array($cod_socio));
                            if ( null != $presentContacts ){
                                $this->log->addDebug('Rimosso i vecchi contatti di '.$cod_socio);
                                $R::trashAll($presentContacts);
                            }
                            $this->log->addDebug('chiedo '.count($contacts).' contacts beans');

                            if ( count($contacts) > 1 ){
                                $contactBeans = $R::dispense('contacts' , count($contacts) );
                                for($i = 0; $i < count($contacts); $i++){
                                    $contactBeans[$i]->csocio = $cod_socio;
                                    $contactBeans[$i]->telefono = $contacts[$i];
                                    $contactBeans[$i]->type = 'CELLULARE';
                                }

                                $R::storeAll($contactBeans);
                            } else {
                                $contactBean = $R::dispense('contacts');
                                $contactBean->csocio = $cod_socio;
                                $contactBean->telefono = $contacts[0];
                                $contactBean->type = 'CELLULARE';
                                $R::store($contactBean);
                            }

                            $this->log->addInfo('Aggiunti all\'utente '.$cod_socio.' '.count($contacts).' contatti');


                        } else {
                            $this->log->addWarning('Utente '.$cod_socio.' senza contatti');
                        }

                    } else {
                        $this->log->addWarning('Utente '.$cod_socio.' gia esistente. con id '.$find->id.' skip');
                    }

                }

            } catch (Exception $e) {
                $this->log->addError('Problema con codice socio : '+$cod_socio);
                throw $e;
            }
        } //for

    }

    /**
     * @param boolean $update
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    }

    /**
     * @return boolean
     */
    public function getUpdate()
    {
        return $this->update;
    }

}
