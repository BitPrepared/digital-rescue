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

use Monolog\Logger;

abstract class BaseDriver
{
    /**
     * @var array di ProfiloAsa
     */
    private $profili;

    private $errori;

    protected $log;

    public function __construct(Logger $log)
    {
        $this->log = $log;
        $this->profili = array();
        $this->errori = array();
    }

    abstract public function carica();

    protected function setSociAsa(array $soci)
    {
        $this->log->addDebug('Aggiunto a '.count($this->profili).' '.count($soci));
        $this->profili = array_merge($this->profili, $soci);
    }

    /**
     * @return array
     */
    public function getProfili()
    {
       return $this->profili;
    }

    /**
     * @return array
     */
    public function getErrori()
    {
        return $this->errori;
    }

    /**
     * @param array $errori
     */
    public function addErrore($errore)
    {
        $this->errori[] = $errore;
    }

}
