<?php


use Phinx\Migration\AbstractMigration;

class ViewSqlAdmin extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $client = $this->table('client');
        $client->renameColumn('c_mail', 'c_email')
            ->save();

        $this->execute('
            CREATE VIEW v_paiement_effectue AS
            SELECT client.c_pk AS c_pk,
                client.c_nom AS c_nom,
                client.c_prenom AS c_prenom,
                client.c_email AS c_email,
                payement.y_status AS y_status,
                payement.y_montant AS y_montant,
                payement.y_date AS y_date,
                payement.y_reference AS y_reference,
                produits.p_pk AS p_pk,
                produits.p_libelle AS p_libelle
            FROM achat
            JOIN client ON client.c_pk = achat.a_c_pk
            JOIN payement ON payement.y_pk = achat.a_y_pk
            JOIN produits ON produits.p_pk = achat.a_p_pk
        ');
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $client = $this->table('client');
        $client->renameColumn('c_email', 'c_mail')
            ->save();

        $this->execute('DROP VIEW v_paiement_effectue');
    }
}
