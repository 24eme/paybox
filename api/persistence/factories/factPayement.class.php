<?php
require_once('api/persistence/objets/payement.class.php');
require_once('api/persistence/factories/factGeneric.class.php');

/**
 *
 * @author david.richard
 *
 */
class factPayement extends factGeneric
{
    /**
     *
     * @var array
     */
    protected static $caMap = array(
        "y_pk" => "ciPk",
        "y_reference" => "csReference",
        "y_status" => "csStatus",
        "y_date" => "cdDate",
        "y_montant" => "ciMontant",
        "y_type_paiement" => "ciTypePaiement");

    /**
     * @var array
     */
    protected static $caType = array(
        "y_pk" => "int",
        "y_reference" => "string",
        "y_status" => "string",
        "y_date" => "date",
        "y_montant" => "int",
        "y_type_paiement" => "int");

    /**
     *
     * @var string
     */
    protected static $csClass = 'payement';
    /**
     *
     * @var string
     */
    protected static $csTable = 'payement';
    /**
     *
     * @var string
     */
    protected static $csPrimaryKey = 'y_pk';

    /**
     *
     * @param $pPk
     * @return payement
     */
    public static function getPayementByPk($pPk)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, self::$csPrimaryKey, $pPk);
    }

    /**
     *
     * @param payement $poPayement
     * @throws Exception
     */
    public static function writePayement($poPayement)
    {
        $laData = array();
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laData [$lsKey] = array('type' => self::$caType[$lsKey], 'data' => $poPayement->__get($lsValue));
        }
        if ($poPayement->_isNew()) {
            mysql::getmysql()->insertData(self::$csTable, $laData);
        } elseif ($poPayement->_isUpdate()) {
            mysql::getmysql()->updateData(self::$csTable, self::$csPrimaryKey, $laData);
        } else {
            throw new Exception('Objet non Enregistré : Pas un nouveau, pas une MAJ');
        }
    }

    /**
     *
     * @return payement
     */
    public static function getNewPayement()
    {
        return new self::$csClass(mysql::getmysql()->getNextId(self::$csPrimaryKey, self::$csTable), null);
    }

    /**
     * @param $psRef
     * @return NULL|payement
     */
    public static function getPayementByReference($psRef)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, "y_reference", $psRef);
    }
}
