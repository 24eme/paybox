<?php


use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $client = $this->table('client', ['id' => 'c_pk', 'signed' => false]);
        $client->addColumn('c_nom', 'string', ['limit' => 250])
               ->addColumn('c_prenom', 'string', ['limit' => 250])
               ->addColumn('c_mail', 'string', ['limit' => 250])
               ->addColumn('c_identifiant', 'string', ['limit' => 250])
               ->addIndex('c_mail', ['unique' => true])
               ->create();

        $payement = $this->table('payement', ['id' => 'y_pk', 'signed' => false]);
        $payement->addColumn('y_reference', 'string', ['limit' => 250])
                 ->addColumn('y_status', 'string', ['limit' => 50])
                 ->addColumn('y_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->addColumn('y_montant', 'float')
                 ->addColumn('y_type_paiement', 'integer')
                 ->addIndex('y_reference')
                 ->create();

        $produits = $this->table('produits', ['id' => 'p_pk', 'signed' => false]);
        $produits->addColumn('p_libelle', 'string', ['limit' => 250])
                 ->addColumn('p_montant', 'float', ['precision' => 25, 'scale' => 3, 'signed' => false])
                 ->addColumn('p_open', 'boolean', ['default' => '1'])
                 ->addColumn('p_salt', 'string', ['limit' => 20])
                 ->addIndex('p_libelle', ['unique' => true])
                 ->create();

        $achat = $this->table('achat', ['id' => false, 'primary_key' => ['a_c_pk', 'a_y_pk', 'a_p_pk']]);
        $achat->addColumn('a_c_pk', 'integer')
              ->addColumn('a_y_pk', 'integer')
              ->addColumn('a_p_pk', 'integer')
              ->addForeignKey('a_c_pk', 'client', 'c_pk')
              ->addForeignKey('a_y_pk', 'payement', 'y_pk')
              ->addForeignKey('a_p_pk', 'produits', 'p_pk')
              ->create();

        $parametre = $this->table('parametre', ['id' => 'pbx_id', 'signed' => false]);
        $parametre->addColumn('pbx_code', 'string', ['limit' => 15])
                  ->addColumn('pbx_value', 'string', ['limit' => 250])
                  ->addColumn('pbx_desc', 'string', ['limit' => 50])
                  ->create();
    }
}
