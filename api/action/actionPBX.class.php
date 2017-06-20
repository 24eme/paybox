<?php

require_once('api/persistence/factories/factParametre.class.php');

/**
 *
 * @author david.richard
 *
 */
class actionPBX
{

	/**
	 * @param $poClient client
	 * @param $poPayement payement
	 * @param $poAchat achat
	 * @return string
	 */
	public static function send($poClient, $poPayement, $poAchat)
	{
		$loParamSite = factParametre::getParametreByCode("PBX_SITE");
		$loParamRang = factParametre::getParametreByCode("PBX_RANG");
		$loParamIdentifiant = factParametre::getParametreByCode("PBX_IDENTIFIANT");
		$loParamDevise = factParametre::getParametreByCode("PBX_DEVISE");
		$loParamRepondreA = factParametre::getParametreByCode("PBX_REPONDRE_A");
		$loParamMode = factParametre::getParametreByCode("PBX_MODE");
		$loParamRetour = factParametre::getParametreByCode("PBX_RETOUR");
		$loParamEffectue = factParametre::getParametreByCode("PBX_EFFECTUE");
		$loParamRefuse = factParametre::getParametreByCode("PBX_REFUSE");
		$loParamAnnule = factParametre::getParametreByCode("PBX_ANNULE");
		$loParamUrlPaybox = factParametre::getParametreByCode("PBX_PAYBOX");
		$lcSeparateur = ' ';

		$lsCgiBin = '/usr/lib/cgi-bin/modulev2.cgi';

		// Si paiement en 1x
		if ($poPayement->getTypePaiement() == 1) {
			$cmd = $lsCgiBin
				. $loParamMode->renderUrl($lcSeparateur)
				. $loParamSite->renderUrl($lcSeparateur)
				. $loParamRang->renderUrl($lcSeparateur)
				. $loParamIdentifiant->renderUrl($lcSeparateur)
				. ' PBX_TOTAL=' . $poAchat->getProduit()->getMontantEnCentime()
				. $loParamDevise->renderUrl($lcSeparateur)
				. ' PBX_CMD=' . $poPayement->getReference()
				. ' PBX_PORTEUR="' . $poClient->getEmail() . '"'
				. $loParamRetour->renderUrl($lcSeparateur)
				. $loParamEffectue->renderUrl($lcSeparateur)
				. $loParamRefuse->renderUrl($lcSeparateur)
				. $loParamRepondreA->renderUrl($lcSeparateur)
				. $loParamRetour->renderUrl($lcSeparateur)
				. $loParamAnnule->renderUrl($lcSeparateur)
				. $loParamUrlPaybox->renderUrl($lcSeparateur);

			return shell_exec($cmd);
		} // Si paiement en 3x
		elseif ($poPayement->getTypePaiement() == 2) {
			$today = new DateTime();

			$unMois = clone $today;
			$unMois->add(new DateInterval('P1M')); // +1 mois

			$deuxMois = clone $today;
			$deuxMois->add(new DateInterval('P2M')); // +2 mois

			// Retourne la valeur entière de la division
			$tier = (int)($poAchat->getProduit()->getMontantEnCentime() / 3);
			$modulo = $poAchat->getProduit()->getMontantEnCentime() % 3;

			$cmd = $lsCgiBin
				. $loParamMode->renderUrl($lcSeparateur)
				. $loParamSite->renderUrl($lcSeparateur)
				. $loParamRang->renderUrl($lcSeparateur)
				. $loParamIdentifiant->renderUrl($lcSeparateur)

				// Montant initial
				. ' PBX_TOTAL=' . $tier

				// 1er prélèvement
				. ' PBX_DATE1=' . $unMois->format('d/m/Y')
				. ' PBX_2MONT1=' . $tier

				// 2eme prélèvement
				. ' PBX_DATE2=' . $deuxMois->format('d/m/Y')
				. ' PBX_2MONT2=' . ($tier + $modulo)

				. $loParamDevise->renderUrl($lcSeparateur)
				. ' PBX_CMD=' . $poPayement->getReference()
				. ' PBX_PORTEUR="' . $poClient->getEmail() . '"'
				. $loParamRetour->renderUrl($lcSeparateur)
				. $loParamEffectue->renderUrl($lcSeparateur)
				. $loParamRefuse->renderUrl($lcSeparateur)
				. $loParamRepondreA->renderUrl($lcSeparateur)
				. $loParamRetour->renderUrl($lcSeparateur)
				. $loParamAnnule->renderUrl($lcSeparateur)
				. $loParamUrlPaybox->renderUrl($lcSeparateur);

			return shell_exec($cmd);
		}

		return "Une erreur est survenue.";
	}

}
