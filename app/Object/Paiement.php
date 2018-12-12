<?php

namespace App\Object;

use App\Object\Generic;

/**
 *
 * @author david.richard
 *
 */
class Paiement extends Generic
{
    public static $STATUS = array(0 => 'A_CONFIRMER', // En attente du paiement PBX
        1 => 'EFFECTUE', // Paiement effectué et confirmé
        2 => 'EN_ATTTENTE', // Paiement en attente de validation
        3 => 'ERREUR_BANQUE', // Erreur de la banque client
        4 => 'ERREUR', // Erreur Paybox
        5 => 'INCONNU', // Code retour inconnu
        6 => 'ANNULER'); // Paiement annulé par le client
    protected $ciPk;
    protected $csReference;
    protected $csStatus;
    protected $cdDate;
    protected $ciMontant;
    protected $ciTypePaiement;

    public function __construct()
    {
        $this->_nouveau();
    }

    public function getReference()
    {
        return $this->__get("csReference");
    }

    public function getStatus()
    {
        return $this->__get("csStatus");
    }

    public function getKey()
    {
        return $this->__get("ciPk");
    }

    public function getDate()
    {
        return $this->__get("cdDate");
    }

    public function getMontant()
    {
        return $this->__get("ciMontant");
    }

    public function setReference($psRef)
    {
        if ($psRef != $this->csReference) {
            $this->__set("csReference", $psRef);
            $this->_update();
        }
    }

    public function setPStatus($pStat)
    {
        if (is_numeric($pStat) && array_key_exists($pStat, Paiement::$STATUS)) {
            $lsCode = Paiement::$STATUS[$pStat];
        } else {
            $lsCode = $pStat;
        }

        if ($lsCode != $this->csStatus) {
            $this->__set("csStatus", $lsCode);
            $this->_update();
        }
    }

    public function setDate()
    {
        $this->__set("cdDate", time());
    }

    public function setMontant($piMontant)
    {
        $this->__set('ciMontant', $piMontant);
    }

    public function getTypePaiement()
    {
        return $this->__get('ciTypePaiement');
    }

    public function setTypePaiement($pTypePaiement)
    {
        $this->__set('ciTypePaiement', $pTypePaiement);
    }
}
