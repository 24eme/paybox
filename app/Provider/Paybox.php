<?php

namespace App\Provider;

/**
 * Cette classe permet de gérer les actions relatives à Paybox
 * La première étant de générer les élements envoyés au service,
 * et le deuxième de vérifier l'état des serveurs Paybox
 *
 * @author Gabriel Poma
 */

class Paybox
{
    const UNEFOIS = 1;
    const TROISFOIS = 2;

    const CHECK_URL = '/load.html';
    const ENTRY_POINT = '/php/';

    /** @var array $elements */
    private $elements = [];

    /** @var string $url Url de paybox */
    private $url = '';

    /**
     * Constructeur. On remet à zéro les variables
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Ajoute un élément dans le tableau des valeurs
     * à envoyer
     *
     * @param string $cle Nom de la valeur PBX_
     * @param string $valeur Valeur de la variable PBX_
     */
    public function add($cle, $valeur)
    {
        $this->elements[$cle] = $valeur;
    }

    /**
     * Enlève un élément du tableau des valeurs
     *
     * @param string $cle Nom de la valeur PBX_
     */
    public function remove($cle)
    {
        unset($this->elements[$cle]);
    }

    /**
     * Ajoute une url
     *
     * @param string $url Une url
     */
    public function setUrl($url)
    {
        $this->url = filter_var($url, FILTER_VALIDATE_URL);

        if ($this->url === false) {
            throw new \Exception('BadUrlException: Bad URL. This is not a valid URL');
        }
    }

    /**
     * Retourne l'Url à appeler avec le formulaire
     *
     * @return string L'url
     */
    public function getUrl()
    {
        return $this->url . self::ENTRY_POINT;
    }

    /**
     * Remet à zéro le tableau des valeurs
     */
    public function reset()
    {
        $this->url = '';
        $this->elements = [];
    }

    /**
     * Génère le formulaire en fonction des éléments du tableau
     *
     * @return string $form Formulaire généré
     */
    public function formulaire()
    {
        $form = '';
        foreach ($this->elements as $cle => $valeur) {
            $form .= '<input type="hidden"
                name="' . $cle . '"
                value="' . $valeur . '"
            >';
        }
        return $form;
    }

    /**
     * Génère le message à chiffrer avec la clé HMAC
     *
     * @return string $message Message au format POST
     */
    public function message($separateur = '&')
    {
        $message = '';
        foreach ($this->elements as $cle => $valeur) {
            $message .= "$cle=$valeur$separateur";
        }
        return substr($message, 0, -1);
    }

    /**
     * Vérifie que les serveurs paybox sont disponibles
     *
     * @return bool Disponible ou non
     */
    public function check()
    {
        $dom = new \DOMDocument();
        $dom->loadHTMLFile($this->url . self::CHECK_URL);

        $status = '';
        $element = $dom->getElementById('server_status');
        if ($element) {
            $status = $element->textContent;
        }

        return $status === 'OK';
    }

    /**
     * Fonction de test pour vérifier les valeurs
     *
     * @return array
     */
    public function _dump()
    {
        return [
            'formulaire' => $this->formulaire(),
            'message' => $this->message()
        ];
    }
}
