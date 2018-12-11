<?php

namespace App\Factory;

use App\Factory\Generic;
use App\Database\Mysql;
use App\Object\Achat as OAchat;

//require_once('api/bd/mysql.class.php');
//require_once('api/persistence/objets/achat.class.php');
//require_once('api/persistence/factories/factGeneric.class.php');

class Achat extends Generic
{
    protected static $caMap = array(
        "a_c_pk" => "ciCPk",
        "a_y_pk" => "ciYPk",
        "a_p_pk" => "ciPPk");

    /**
     * @var array
     */
    protected static $caType = array(
        "a_c_pk" => "int",
        "a_y_pk" => "int",
        "a_p_pk" => "int");

    protected static $csClass = OAchat::class;

    protected static $csTable = 'achat';

    protected static $csPrimaryKey = array('a_c_pk', 'a_y_pk', 'a_p_pk');

    public static function getAchatByClient($pPk)
    {
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laKey [$lsKey] = array('type' => self::$caType[$lsKey]);
        }
        $laData = Mysql::getmysql()->readData(self::$csTable, $laKey, self::$csPrimaryKey[0], $pPk);
        $loRetour = array();
        foreach (self::$caMap as $lsKeyObj => $laLine) {
            $loRetour[$lsKeyObj] = new self::$csClass();
            foreach ($laLine as $lskey => $lsValue) {
                $loRetour[$lsKeyObj]->__set($lsValue, $laData [$lsKey]);
            }
        }
        return $loRetour;
    }

    public static function getAchatByProduit($pPk)
    {
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laKey [$lsKey] = array('type' => self::$caType[$lsKey]);
        }
        $laData = Mysql::getmysql()->readData(self::$csTable, $laKey, self::$csPrimaryKey[2], $pPk);
        $loRetour = array();
        foreach (self::$caMap as $lsKeyObj => $laLine) {
            $loRetour[$lsKeyObj] = new self::$lsClass();
            foreach ($laLine as $lskey => $lsValue) {
                $loRetour[$lsKeyObj]->__set($lsValue, $laData [$lsKey]);
            }
        }
        return $loRetour;
    }

    public static function getAchatByPayement($pPk)
    {
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laKey [$lsKey] = array('type' => self::$caType[$lsKey]);
        }
        $laData = Mysql::getmysql()->readData(self::$csTable, $laKey, self::$csPrimaryKey[1], $pPk);
        $loRetour =  new self::$csClass();
        foreach (self::$caMap as $lsKeyObj => $laLine) {
            $loRetour->__set($laLine, $laData [$lsKeyObj]);
            /*foreach ($laLine as $lskey => $lsValue) {
                $loRetour[$lsKeyObj]->__set($lsValue, $laData [$lsKey]);
            }*/
        }
        return $loRetour;
    }

    /**
     * @param achat $poAchat
     * @throws Exception
     */
    public static function writeAchat($poAchat)
    {
        $laData = array();
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laData [$lsKey] = array('type' => self::$caType[$lsKey], 'data' => $poAchat->__get($lsValue));
        }
        if ($poAchat->_isNew()) {
            Mysql::getmysql()->insertData(self::$csTable, $laData);
        } elseif ($poAchat->_isUpdate()) {
            Mysql::getmysql()->updateData(self::$csTable, self::$csPrimaryKey, $laData);
        } else {
            throw new \Exception('Objet non Enregistré : Pas un nouveau, pas une MAJ');
        }
    }

    /**
     * @return Achat
     */
    public static function getNewAchat()
    {
        return new self::$csClass();
    }
}
