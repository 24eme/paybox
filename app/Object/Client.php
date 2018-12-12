<?php

namespace App\Object;

use App\Object\Generic;

/**
 *
 * @author david.richard
 *
 */
class Client extends Generic
{
    protected $ciPk;

    protected $csNom;

    protected $csPrenom;

    protected $csIdentifiant;

    protected $csEmail;

    public function __construct()
    {
        $liNbArg = func_num_args();
        $laArgs = func_get_args();
        switch ($liNbArg) {
            case 2:
                $this->_nouveau();
                // no break
            case 1:
                $this->__set("ciPk", $laArgs [0]);
                break;
            default:
                die('Utilise la factory !!');
        }
    }

    public function getNom()
    {
        return $this->__get("csNom");
    }

    public function getPrenom()
    {
        return $this->__get("csPrenom");
    }

    public function getKey()
    {
        return $this->__get("ciPk");
    }

    public function getIdentifiant()
    {
        return $this->__get("csIdentifiant");
    }

    public function setNom($psNom)
    {
        if ($psNom != $this->csNom) {
            $this->__set("csNom", $psNom);
            $this->_update();
        }
    }

    public function setPrenom($pPrenom)
    {
        if ($pPrenom != $this->csPrenom) {
            $this->__set('csPrenom', $pPrenom);
            $this->_update();
        }
    }

    public function setIdentifiant($pId)
    {
        if ($pId != $this->csIdentifiant) {
            $this->__set("csIdentifiant", $pId);
            $this->_update();
        }
    }

    public function getEmail()
    {
        return $this->__get("csEmail");
    }

    public function setEmail($psemail)
    {
        if ($psemail != $this->csEmail) {
            $this->__set("csEmail", $psemail);
            $this->_update();
        }
    }
}
