<?php

namespace App\Object;

use App\Object\Generic;

/**
 *
 * @author david.richard
 *
 */
class Produit extends Generic
{
    protected $ciPk;

    protected $csLibelle;

    protected $ciMontant;

    protected $ciOpen;

    protected $csSalt;

    protected $ciTypePaiement;

    public function __construct()
    {
        $liNbArg = func_num_args();
        $laArgs = func_get_args();
        switch ($liNbArg) {
            case 1:
                $this->__set("ciPk", $laArgs [0]);
                $this->_nouveau();
                break;
            default:
                die('Utilise la factory !!');
        }
    }
    public function getLibelle()
    {
        return $this->__get("csLibelle");
    }
    public function getMontantEnEuro()
    {
        return  round($this->__get("ciMontant"), 2);
    }
    public function getMontantEnCentime()
    {
        return $this->__get("ciMontant")*100;
    }
    public function getKey()
    {
        return $this->__get("ciPk");
    }
    public function getTypePaiement()
    {
        return $this->__get("ciTypePaiement");
    }
    public function setLibelle($psLib)
    {
        if ($psLib != $this->csLibelle) {
            $this->__set("csLibelle", $psLib);
            $this->_update();
        }
    }
    public function setMontant($pMont)
    {
        if ($pMont != $this->ciMontant) {
            $this->__set("ciMontant", $pMont);
            $this->_update();
        }
    }
    public function isOpen()
    {
        return $this->__get('ciOpen');
    }

    public function getSalt()
    {
        return $this->__get('csSalt');
    }
}
