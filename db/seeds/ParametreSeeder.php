<?php


use Phinx\Seed\AbstractSeed;

class ParametreSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'pbx_code'  => 'PBX_SITE',
                'pbx_value' => '',
                'pbx_desc'  => 'Identifiant du site'
            ],
            [
                'pbx_code'  => 'PBX_RANG',
                'pbx_value' => '',
                'pbx_desc'  => 'Rang'
            ],
            [
                'pbx_code'  => 'PBX_DEVISE',
                'pbx_value' => '978',
                'pbx_desc'  => 'Identifiant de la devise'
            ],
            [
                'pbx_code'  => 'PBX_HASH',
                'pbx_value' => 'SHA512',
                'pbx_desc'  => 'Algorithme de hachage'
            ],
            [
                'pbx_code'  => 'PBX_PRIV_KEY',
                'pbx_value' => '',
                'pbx_desc'  => 'Clé privé HMAC'
            ],
            [
                'pbx_code'  => 'PBX_IDENTIFIANT',
                'pbx_value' => '',
                'pbx_desc'  => 'Identifiant du site'
            ],
            [
                'pbx_code'  => 'REF_SEPA',
                'pbx_value' => '-',
                'pbx_desc'  => 'Séparateur de référence'
            ],
            [
                'pbx_code'  => 'PBX_RETOUR',
                'pbx_value' => 'Mt:M;Ref:R;Auto:A;Reponse:E;Sign:K',
                'pbx_desc'  => 'Informations retournés par Paybox'
            ],
            [
                'pbx_code'  => 'PBX_MODE',
                'pbx_value' => '4',
                'pbx_desc'  => 'Mode Formulaire'
            ],
            [
                'pbx_code'  => 'PBX_REPONDRE_A',
                'pbx_value' => '_URL_/validation_paiement.php',
                'pbx_desc'  => 'URL d\'IPN'
            ],
            [
                'pbx_code'  => 'PBX_EFFECTUE',
                'pbx_value' => '_URL_/retour.php?action=effectue',
                'pbx_desc'  => 'URL Retour utilisateur'
            ],
            [
                'pbx_code'  => 'PBX_REFUSE',
                'pbx_value' => '_URL_/retour.php?action=refuse',
                'pbx_desc'  => 'URL Retour refusé'
            ],
            [
                'pbx_code'  => 'PBX_ANNULE',
                'pbx_value' => '_URL_/retour.php?action=annule',
                'pbx_desc'  => 'URL Retour annulé'
            ],
            [
                'pbx_code'  => 'PBX_ATTENTE',
                'pbx_value' => '_URL_/retour.php?action=attente',
                'pbx_desc'  => 'URL Retour attente'
            ],
            [
                'pbx_code'  => 'PBX_PAYBOX',
                'pbx_value' => 'https://tpeweb.paybox.com',
                'pbx_desc'  => 'URL de paiement Paybox'
            ]
        ];

        $parametre = $this->table('parametre');
        $parametre->insert($data)
                  ->save();
    }
}
