<?php

namespace App\Factory;

use App\Database\Mysql;
use App\Object\Client as ObjectClient;
use App\Factory\Generic;

/**
 * @author david.richard
 *
 */
class Client extends Generic
{

    /**
     * @var array Tableau de mappage entre champs en base et propriété de l'objet
     */
    protected static $caMap = array(
            "c_pk" => "ciPk",
            "c_nom" => "csNom",
            "c_prenom" => "csPrenom",
            "c_email" => "csEmail",
            "c_identifiant" => "csIdentifiant" );

    /**
     * @var array
     */
    protected static $caType = array(
            "c_pk" => "int",
            "c_nom" =>"string",
            "c_prenom" => "string",
            "c_email" => "string",
            "c_identifiant" => "string");

    /**
     * @var string  Class factory
     */
    protected static $csClass = ObjectClient::class;

    /**
     * @var string table correspondante
     */
    protected static $csTable = 'client';

    /**
     * @var string clé primaire de la table
     */
    protected static $csPrimaryKey = 'c_pk';

    /**
     * @param int $pPk valeure rechercher
     * @return client objet trouve ou null
     */
    public static function getClientByPk($pPk)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, self::$csPrimaryKey, $pPk);
    }

    /**
     * @param client $poClient Objet client a ajouter dans la base.
     */
    public static function writeClient($poClient)
    {
        $laData = array();
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laData [$lsKey] = array('type'=> self::$caType[$lsKey],  'data'=> $poClient->__get($lsValue));
        }
        if ($poClient->_isNew()) {
            Mysql::getmysql()->insertData(self::$csTable, $laData);
        } elseif ($poClient->_isUpdate()) {
            Mysql::getmysql()->updateData(self::$csTable, self::$csPrimaryKey, $laData);
        } else {
            throw new \Exception('Objet non Enregistré : Pas un nouveau, pas une MAJ');
        }
    }

    /**
     * @return client
     */
    public static function getNewClient()
    {
        return new self::$csClass(Mysql::getmysql()->getNextId(self::$csPrimaryKey, self::$csTable), null);
    }

    /**
     * @param string $psRef
     * @return client
     */
    public static function getClientByReference($psRef)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, "c_identifiant", $psRef);
    }
}
