<?php
require_once 'define.php';

class utils
{
	protected static $code = array(
			'00000' => 'Opération réussie.',
			'00001' => 'La connexion au centre d’autorisation a échoué ou une erreur interne est survenue.',
			'001xx' => 'Paiement refusé par le centre d’autorisation.',
			'00101' => 'Contacter l’émetteur de carte',
			'00102' => 'Contacter l’émetteur de carte',
			'00103' => 'Commerçant invalide',
			'00104' => 'Conserver la carte',
			'00105' => 'Ne pas honorer',
			'00107' => 'Conserver la carte, conditions spéciales',
			'00108' => 'Approuver après identification du porteur',
			'00112' => 'Transaction invalide',
			'00113' => 'Montant invalide',
			'00114' => 'Numéro de porteur invalide',
			'00115' => 'Émetteur de carte inconnu',
			'00117' => 'Annulation client',
			'00119' => 'Répéter la transaction ultérieurement',
			'00120' => 'Réponse erronée (erreur dans le domaine serveur)',
			'00124' => 'Mise à jour de fichier non supportée',
			'00125' => 'Impossible de localiser l’enregistrement dans le fichier',
			'00126' => 'Enregistrement dupliqué, ancien enregistrement remplacé',
			'00127' => 'Erreur en « edit » sur champ de mise à jour fichier',
			'00128' => 'Accès interdit au fichier',
			'00129' => 'Mise à jour de fichier impossible',
			'00130' => 'Erreur de format',
			'00131' => 'Identifiant de l’organisme acquéreur inconnu',
			'00133' => 'Carte expirée',
			'00134' => 'Suspicion de fraude',
			'00138' => 'Nombre d’essais code confidentiel dépassé',
			'00141' => 'Carte perdue',
			'00143' => 'Carte volée',
			'00151' => 'Provision insuffisante ou crédit dépassé',
			'00154' => 'Date de validité de la carte dépassée',
			'00155' => 'Code confidentiel erroné',
			'00156' => 'Carte absente du fichier',
			'00157' => 'Transaction non permise à ce porteur',
			'00158' => 'Transaction interdite au terminal',
			'00159' => 'Suspicion de fraude',
			'00160' => 'L’accepteur de carte doit contacter l’acquéreur',
			'00161' => 'Dépasse la limite du montant de retrait',
			'00163' => 'Règles de sécurité non respectées',
			'00168' => 'Réponse non parvenue ou reçue trop tard',
			'00175' => 'Nombre d’essais code confidentiel dépassé',
			'00176' => 'Porteur déjà en opposition, ancien enregistrement conservé',
			'00189' => 'Échec de l’authentification',
			'00190' => 'Arrêt momentané du système',
			'00191' => 'Émetteur de cartes inaccessible',
			'00194' => 'Demande dupliquée',
			'00196' => 'Mauvais fonctionnement du système',
			'00197' => 'Échéance de la temporisation de surveillance globale',
			'00198' => 'Serveur inaccessible (positionné sur le serveur)',
			'00199' => 'Incident domaine initiateur',
			'00003' => 'Erreur  Paybox.',
			'00004' => 'Numéro de porteur ou cryptogramme visuel invalide.',
			'00006' => 'Accès refusé ou site/rang/identifiant incorrect.',
			'00008' => 'Date de fin de validité incorrecte.',
			'00009' => 'Erreur de création d’un abonnement.',
			'00010' => 'Devise inconnue.',
			'00011' => 'Montant incorrect.',
			'00015' => 'Paiement déjà effectué.',
			'00016' => 'Abonné  déjà  existant  (inscription  nouvel  abonné).',
			'00021' => 'Carte non autorisée.',
			'00029' => 'Carte  non  conforme.',
			'00030' => 'Temps  d’attente  >  15  mn  par  l’internaute/acheteur  au  niveau  de  la  page  de paiements.',
			'00031' => 'Réservé',
			'00032' => 'Réservé',
			'00033' => 'Code pays de l’adresse IP du navigateur de l’acheteur non autorisé.',
			'00040' => 'Opération sans authentification 3-DSecure, bloquée par le filtre.',
			'99999' => 'Opération en attente de validation par l’émetteur du moyen de paiement.'
				);

	public static function code_retour($key)
	{
		return (key_exists($key, self::$code)) ? self::$code[$key] : false;
	}

	public static function display_error_page($msg, $psComplement='')
	{
		ob_end_clean();
		utils::log(LOG_FILE, $msg.' : '.$psComplement );
		require VIEW.'/error.phtml';
		exit(-1);
	}

	public static function log($file, $msg)
	{
		$h = fopen($file, 'a');
		fwrite($h, '[' . date('d/m/Y H:i:s') . '] [' . $_SERVER['REMOTE_ADDR'] . '] ' . $msg . PHP_EOL);
		fclose($h);
	}

	public static function debug($var)
	{
		utils::log(LOG_FILE, '--------DEBUG--------');
		utils::log(LOG_FILE, print_r($var, 1));
		utils::log(LOG_FILE, '---------------------');
	}

	public static function mail($to, $template, $data)
	{
		$subject = "Notification de paiement";

		/*$message = file_get_contents($template);

		foreach($data as $key => $value) {
			$message = str_replace('{'.$key.'}', $value, $message);
		}*/

		$message = self::render($template, $data);

		$headers = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		$headers[] = "***REMOVED***";
		$headers[] = "Subject: {$subject}";
		$headers[] = "X-Mailer: PHP/".phpversion();

		mail($to, $subject, $message, implode("\r\n", $headers));
	}

	public static function render($template, $data) {
		
		$page = file_get_contents($template);

		foreach($data as $key => $value) {
			$page = str_replace('{'.$key.'}', $value, $page);
		}
		return $page;
	}
	/*
	   public static function buildRefClient($psSalt) {
	   $paramRefSepar = factParametre::getParametreByCode("REF_SEPA");
	   return $psSalt.$paramRefSepar->getValue()
	   .$_POST['pers_id'] . $paramRefSepar->getValue()
	   .$_POST['promo'] . $paramRefSepar->getValue()
	   .$_POST['A_Etude'];
	   }

	   public static function buildRefPayement($psSalt) {
	   $paramRefSepar = factParametre::getParametreByCode("REF_SEPA");
	   return $psSalt.$paramRefSepar->getValue().
	   $_POST['pers_id'] . $paramRefSepar->getValue() .
	   $_POST['nom'] . $paramRefSepar->getValue() .
	   $_POST['prenom'] . $paramRefSepar->getValue() .
	   $_POST['email'] . $paramRefSepar->getValue() .
	   $_POST['promo'] . $paramRefSepar->getValue() .
	   $_POST['A_Etude'];
	   }*/

	public static function buildMessage() {
		$paramRefSepar = factParametre::getParametreByCode("REF_SEPA");
		return $paramSite->renderUrl() .
			$paramRang->renderUrl('&') .
			$paramIdentifiant->renderUrl('&') .
			"&PBX_TOTAL=" . $_POST['Montant'] .
			$paramDevise->renderUrl("&") .
			"&PBX_CMD=" . $refPayement .
			"&PBX_PORTEUR=" . $_POST['email'] .
			$paramRepondreA->renderUrl('&') .
			"&PBX_RETOUR=Mt:M;Ref:R;Auto:A;Erreur:E" .
			$paramHash->renderUrl('&') .
			"&PBX_TIME=" . $dateTime;
	}

	public static function verify_sign($data, $sign) {
		$pubkeyid = openssl_pkey_get_public('file://'.PUBKEY);
		return openssl_verify($data, $sign, $pubkeyid);
	}
}
