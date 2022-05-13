<?php

namespace App\Object;

use App\Object\Generic;
use App\Factory\Produit as FProduit;
use App\Factory\Paiement as FPaiement;
use App\Factory\Client as FClient;

/**
 *
 * @author david.richard
 *
 */
class Achat extends Generic
{
    protected $ciCPk;
    protected $ciYPk;
    protected $ciPPk;

    public function __construct()
    {
        $liNbArg = func_num_args();
        $laArgs = func_get_args();
        switch ($liNbArg) {
            case 0:
                $this->_nouveau();
                break;
            default:
                die('Utilise la factory !!');
        }
    }

    public function getClientPk()
    {
        return $this->__get("ciCPk");
    }

    public function getProduitPk()
    {
        return $this->__get("ciPPk");
    }

    public function getPayementPk()
    {
        return $this->__get("ciYPk");
    }

    public function setClientPk($piCpk)
    {
        if ($piCpk!=$this->ciCPk) {
            $this->__set("ciCPk", $piCpk);
            $this->_nouveau();
        }
    }

    public function setProduitPk($piPpk)
    {
        if ($piPpk!=$this->ciPPk) {
            $this->__set("ciPPk", $piPpk);
            $this->_nouveau();
        }
    }

    public function setPayementPk($piYpk)
    {
        if ($piYpk!=$this->ciYPk) {
            $this->__set("ciYPk", $piYpk);
            $this->_nouveau();
        }
    }

    public function getClient()
    {
        return Client::getClientByPk($this->ciCPk);
    }

    public function getProduit()
    {
        return Produit::getProduitByPk($this->ciPPk);
    }

    public function getPayement()
    {
        return FPaiement::getPayementByPk($this->ciYPk);
    }
}
