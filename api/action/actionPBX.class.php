<?php

require_once('api/persistence/factories/factParametre.class.php');
/**
 *
 * @author david.richard
 *        
 */
class actionPBX {

  public static function send ($poClient, $poPayement, $poAchat) {
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
	  $loParamUrlPaybox = factParametre::getParametreByCode("PBX_URL");
		$lcSeparateur = ' ';
		 
		$lsCgiBin = '/usr/lib/cgi-bin/modulev2.cgi';

		$cmd = $lsCgiBin 
			 . $loParamMode->renderUrl($lcSeparateur)
		 	 . $loParamSite->renderUrl($lcSeparateur) 
		 	 . $loParamRang->renderUrl($lcSeparateur) 
		 	 . $loParamIdentifiant->renderUrl($lcSeparateur)
			 . ' PBX_TOTAL='.$poAchat->getProduit()->getMontantEnCentime()
			 . $loParamDevise->renderUrl($lcSeparateur)
			 . ' PBX_CMD='.$poPayement->getReference()
			 . ' PBX_PORTEUR="'.$poClient->getEmail().'"'
			 . $loParamRetour->renderUrl($lcSeparateur)
			 . $loParamEffectue->renderUrl($lcSeparateur)
			 . $loParamRefuse ->renderUrl($lcSeparateur)
			 . $loParamRepondreA->renderUrl($lcSeparateur)
			 . $loParamRetour->renderUrl($lcSeparateur)
			 . $loParamAnnule ->renderUrl($lcSeparateur)
			. $loParamUrlPaybox->renderUrl($lcSeparateur);
	  //. ' PBX_PAYBOX=https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
		
         return shell_exec($cmd);
	}

}
