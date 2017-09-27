<?php
require_once('api/bd/mysql.class.php');
require_once('api/persistence/objets/produits.class.php');
require_once('api/persistence/factories/factGeneric.class.php');

/**
 * @author david.richard
 *
 */
class factProduits extends factGeneric
{
    /**
     * @var array
     */
    protected static $caMap = array(
            "p_pk" => "ciPk",
            "p_libelle" => "csLibelle",
            "p_montant" => "ciMontant" ,
        'p_open' => 'ciOpen',
        'p_salt' => 'csSalt',
        'p_type_paiement' => 'ciTypePaiement');
    /**
     * @var array
     */
    protected static $caType = array(
            "p_pk" => "int",
            "p_libelle" => "string",
            "p_montant" => "int",
        "p_open" => "int",
        "p_salt" => "string",
        "p_type_paiement" => "int");
    /**
     * @var string
     */
    protected static $csClass = 'produits';
    /**
     * @var string
     */
    protected static $csTable = 'produits';
    /**
     * @var string
     */
    protected static $csPrimaryKey = 'p_pk';
    
    /**
     * @param int $pPk
     * @return produits
     */
    public static function getProduitByPk($pPk)
    {
        return self::_getBy(self::$csClass, self::$caMap, self::$caType, self::$csTable, self::$csPrimaryKey, $pPk);
    }

    /**
     * @param produits $poProduit
     */
    public static function writeProduit($poProduit)
    {
        $laData = array();
        foreach (self::$caMap as $lsKey => $lsValue) {
            $laData [$lsKey] = array('type'=> self::$caType[$lsKey],  'data'=> $poProduit->__get($lsValue));
        }
        if ($poProduit->_isNew()) {
            mysql::getmysql()->insertData(self::$csTable, $laData);
        } elseif ($poProduit->_isUpdate()) {
            mysql::getmysql()->updateData(self::$csTable, self::$csPrimaryKey, $laData);
        } else {
            throw new Exception('Objet non Enregistré : Pas un nouveau, pas une MAJ');
        }
    }
    /**
     * @return produits
     */
    public static function getNewProduit()
    {
        return new self::$csClass(mysql::getmysql()->getNextId(self::$csPrimaryKey, self::$csTable));
    }
}
