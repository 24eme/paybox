<?php

/**
 * @author Gabriel Poma
 */

class Paybox {
    const UNEFOIS = 1;
    const TROISFOIS = 2;

    /** @var array $elements */
    private $elements = [];

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
        foreach($this->elements as $cle => $valeur) {
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
        foreach($this->elements as $cle => $valeur) {
            $message .= "$cle=$valeur$separateur";
        }
        return substr($message, 0, -1);
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
