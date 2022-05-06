<?php


use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class ProduitSeeder extends AbstractSeed
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
        $faker = Factory::create('fr_FR');
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'p_libelle' => $faker->sentence(3),
                'p_montant' => $faker->randomFloat(2, 10, 5000),
                'p_open' => $faker->boolean(),
                'p_salt' => 'ENVA',
                'p_type_paiement' => $faker->numberBetween(1, 2)
            ];
        }

        $this->table('produits')->insert($data)->saveData();
    }
}
