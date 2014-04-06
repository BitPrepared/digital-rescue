<?php
/**
 * BitPrepared - ASA Import Bundle
 *
 * @author      Stefano Tamagnini <yoghi@sigmalab.net>
 * @copyright   2014 Stefano Tamagnini
 * @date : 05/04/14 - 21:36
 * @link
 * @license
 * @version
 * @package
 */
namespace Bitprepared\Asa\Model;

class ProfiloAsa
{
    private $csocio;
    private $gruppo;
    private $cognome;
    private $nome;
    private $email;
    private $indirizzo;
    private $cap;
    private $residenza;
    private $prov;
    private $datanascita;
    private $luogonascita;
    private $foca;

    private $contacts;

    /**
     * @param array $contacts
     */
    public function setContacts(array $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param mixed $residenza
     */
    public function setResidenza($residenza)
    {
        $this->residenza = $residenza;
    }

    /**
     * @return mixed
     */
    public function getResidenza()
    {
        return $this->residenza;
    }

    /**
     * @param mixed $cap
     */
    public function setCap($cap)
    {
        $this->cap = $cap;
    }

    /**
     * @return mixed
     */
    public function getCap()
    {
        return $this->cap;
    }

    /**
     * @param mixed $cognome
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;
    }

    /**
     * @return mixed
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * @param mixed $csocio
     */
    public function setCsocio($csocio)
    {
        $this->csocio = $csocio;
    }

    /**
     * @return mixed
     */
    public function getCsocio()
    {
        return $this->csocio;
    }

    /**
     * @param mixed $datanascita
     */
    public function setDatanascita($datanascita)
    {
        $this->datanascita = $datanascita;
    }

    /**
     * @return mixed
     */
    public function getDatanascita()
    {
        return $this->datanascita;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $foca
     */
    public function setFoca($foca)
    {
        $this->foca = $foca;
    }

    /**
     * @return mixed
     */
    public function getFoca()
    {
        return $this->foca;
    }

    /**
     * @param mixed $gruppo
     */
    public function setGruppo($gruppo)
    {
        $this->gruppo = $gruppo;
    }

    /**
     * @return mixed
     */
    public function getGruppo()
    {
        return $this->gruppo;
    }

    /**
     * @param mixed $indirizzo
     */
    public function setIndirizzo($indirizzo)
    {
        $this->indirizzo = $indirizzo;
    }

    /**
     * @return mixed
     */
    public function getIndirizzo()
    {
        return $this->indirizzo;
    }

    /**
     * @param mixed $luogonascita
     */
    public function setLuogonascita($luogonascita)
    {
        $this->luogonascita = $luogonascita;
    }

    /**
     * @return mixed
     */
    public function getLuogonascita()
    {
        return $this->luogonascita;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $prov
     */
    public function setProv($prov)
    {
        $this->prov = $prov;
    }

    /**
     * @return mixed
     */
    public function getProv()
    {
        return $this->prov;
    }

}
