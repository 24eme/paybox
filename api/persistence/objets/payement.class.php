<?php
require_once 'objGeneric.class.php';

/**
 *
 * @author david.richard
 *
 */
class payement extends objGeneric
{
   public static $STATUS = array(0 => 'A_CONFIRMER', // En attente du paiement PBX
                                      1 => 'EFFECTUE', // Paiement effectué et confirmé
                                      2 => 'EN_ATTTENTE', // Paiement en attente de validation
                                      3 => 'ERREUR_BANQUE', // Erreur de la banque client
                                      4 => 'ERREUR', // Erreur Paybox
                                      5 => 'INCONNU', // Code retour inconnu
                                      6 => 'ANNULER' ); // Paiement annulé par le client
	protected $ciPk;
	protected $csReference;
	protected $csStatus;
	protected $cdDate;
	protected $ciMontant;

	public function __construct()
	{
		$liNbArg = func_num_args();
		$laArgs = func_get_args();
		switch ($liNbArg) {
			case 2 :
				$this->_nouveau();
			case 1 :
				$this->__set("ciPk", $laArgs [0]);
				break;
			default :
				die ('Utilise la factory !!');
		}
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
      if (is_numeric($pStat) &&  array_key_exists($pStat, payement::$STATUS) ) { $lsCode= payement::$STATUS[$pStat]; }
      else { $lsCode=$pStat; }

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
}
