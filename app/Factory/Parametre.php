<?php

namespace App\Factory;

use App\Database\Mysql;
use App\Factory\Generic;
use App\Object\Parametre as OParametre;

//require_once('api/bd/mysql.class.php');
//require_once('api/persistence/objets/parametre.class.php');
//require_once('api/persistence/factories/factGeneric.class.php');

/**
 * @author david.richard
 *
 */
class Parametre extends Generic
{
    /**
     * @var array
     */
    protected static $caMap = array(
            "pbx_id" => "ciPk",
            "pbx_code" => "csCode",
            "pbx_value" => "csValue",
            "pbx_desc" => "csDescription"
    );

    /**
     * @var array
     */
    protected static $caType = array(
            "pbx_id" => "int",
            "pbx_code" => "string",
            "pbx_value" => "string",
            "pbx_desc" => "string"
    );

    /**
     * @var string
     */
    protected static $csClass = OParametre::class;

    /**
     * @var string
     */
    protected static $csTable = 'parametre';

    /**
     * @var string
     */
    protected static $csPrimaryKey = 'pbx_id';

    /**
     * @param int $pPk
     * @return parametre
     */
    public static function getParametreByPk($pPk)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, self::$csPrimaryKey, $pPk);
    }

    /**
     * @param parametre $poProduit
     */
    public static function writeParametre($poParam)
    {
        $laData = array();
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laData [$lsKey] = array('type'=> self::$caType[$lsKey],  'data'=> $poParam->__get($lsValue));
        }
        if ($poParam->_isNew()) {
            mysql::getmysql()->insertData(self::$csTable, $laData);
        } elseif ($poParam->_isUpdate()) {
            mysql::getmysql()->updateData(self::$csTable, self::$csPrimaryKey, $laData);
        } else {
            throw new Exception('Objet non Enregistrer : Pas un nouveau, pas une MAJ');
        }
    }

    /**
     * @return Produit
     */
    public static function getNewParametre()
    {
        return new self::$csClass(mysql::getmysql()->getNextId(self::$csPrimaryKey, self::$csTable));
    }

    /**
     * @param string $pPk
     * @return parametre
     */
    public static function getParametreByCode($pCode)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, "pbx_code", $pCode);
    }
}
