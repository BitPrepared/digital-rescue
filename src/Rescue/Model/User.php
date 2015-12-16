<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 05/04/14 - 23:52.
 */
namespace Rescue\Model;

class User
{
    private $csocio;
    private $role;
    private $password;
    private $last_login;
    private $active;
    private $display_name;
    private $registred;
    private $activation_key;

    /**
     * @param mixed $activation_key
     */
    public function setActivationKey($activation_key)
    {
        $this->activation_key = $activation_key;
    }

    /**
     * @return mixed
     */
    public function getActivationKey()
    {
        return $this->activation_key;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
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
     * @param mixed $display_name
     */
    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $registred
     */
    public function setRegistred($registred)
    {
        $this->registred = $registred;
    }

    /**
     * @return mixed
     */
    public function getRegistred()
    {
        return $this->registred;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }
}
