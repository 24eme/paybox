<?php

require_once('api/persistence/factories/factParametre.class.php');

/**
 *
 * @author david.richard
 *
 */
class actionPBX
{

    private $client,
            $paiement,
            $achat,
            $site,
            $rang,
            $identifiant,
            $devise,
            $repondreA,
            $mode,
            $retour,
            $effectue,
            $refuse,
            $annule,
            $urlPaybox,
            $separateur,
            $hash,
            $time;

    /*
     * @param $client client
     * @param $paiement paiement
     * @param $achat achat
     */
    public function __construct($client, $paiement, $achat)
    {
        $this->site = factParametre::getParametreByCode("PBX_SITE");
        $this->rang = factParametre::getParametreByCode("PBX_RANG");
        $this->identifiant = factParametre::getParametreByCode("PBX_IDENTIFIANT");
        $this->devise = factParametre::getParametreByCode("PBX_DEVISE");
        $this->repondreA = factParametre::getParametreByCode("PBX_REPONDRE_A");
        $this->mode = factParametre::getParametreByCode("PBX_MODE");
        $this->retour = factParametre::getParametreByCode("PBX_RETOUR");
        $this->effectue = factParametre::getParametreByCode("PBX_EFFECTUE");
        $this->refuse = factParametre::getParametreByCode("PBX_REFUSE");
        $this->annule = factParametre::getParametreByCode("PBX_ANNULE");
        $this->hash = factParametre::getParametreByCode("PBX_HASH");
        $this->separateur = '&';
        $this->time = date('c');

        $this->client = $client;
        $this->paiement = $paiement;
        $this->achat = $achat;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $total = '';

        switch($this->paiement->getTypePaiement()) {
            case 2: // paiement en 3x
                $today = new DateTime();

                $unMois = clone $today;
                $unMois->add(new DateInterval('P1M')); // +1 mois

                $deuxMois = clone $today;
                $deuxMois->add(new DateInterval('P2M')); // +2 mois

                // Retourne la valeur entière de la division
                $tier = (int)($this->achat->getProduit()->getMontantEnCentime() / 3);
                $modulo = $this->achat->getProduit()->getMontantEnCentime() % 3;

                $total =
                // Montant initial
                  '&PBX_TOTAL=' . $tier

                // 1er prélèvement
                . '&PBX_DATE1=' . $unMois->format('d/m/Y')
                . '&PBX_2MONT1=' . $tier

                // 2eme prélèvement
                . '&PBX_DATE2=' . $deuxMois->format('d/m/Y')
                . '&PBX_2MONT2=' . ($tier + $modulo)
                ;

                break;
            default: // paiement en 1x
                $total = '&PBX_TOTAL=' . $this->achat->getProduit()->getMontantEnCentime();
                break;
        }

        return
                $this->mode->renderUrl()
                . $this->site->renderUrl($this->separateur)
                . $this->rang->renderUrl($this->separateur)
                . $this->identifiant->renderUrl($this->separateur)
                . $total
                . $this->devise->renderUrl($this->separateur)
                . '&PBX_CMD=' . $this->paiement->getReference()
                . '&PBX_PORTEUR="' . $this->client->getEmail() . '"'
                . $this->retour->renderUrl($this->separateur)
                . $this->effectue->renderUrl($this->separateur)
                . $this->refuse->renderUrl($this->separateur)
                . $this->repondreA->renderUrl($this->separateur)
                . $this->retour->renderUrl($this->separateur)
                . $this->annule->renderUrl($this->separateur)
                . $this->hash->renderUrl($this->separateur)
                //. $this->urlPaybox->renderUrl($this->separateur)
                . '&PBX_TIME=' . $this->time
                ;

    }

    public function form($hmac)
    {
        return
                $this->mode->renderInput()
                . $this->site->renderInput()
                . $this->rang->renderInput()
                . $this->identifiant->renderInput()
                //. $total
                . $this->devise->renderInput()
                . '<input type="hidden"
                        name="PBX_CMD"
                        value="' . $this->paiement->getReference() . '">'
                . '<input type="hidden"
                        name="PBX_PORTEUR"
                        value="' . $this->client->getEmail() . '">'
                . $this->retour->renderInput()
                . $this->effectue->renderInput()
                . $this->refuse->renderInput()
                . $this->repondreA->renderInput()
                . $this->retour->renderInput()
                . $this->annule->renderInput()
                //. $this->urlPaybox->renderInput()
                . $this->hash->renderInput()
                . '<input type="hidden" name="PBX_TIME" value="'.$this->time.'">'
                . '<input type="hidden" name="PBX_HMAC" value="'.$hmac.'">'
                ;
    }
}
