<?php

namespace App\Factory;

use App\Database\Mysql;

//require_once('api/bd/mysql.class.php');

/**
 * @author david.richard
 *
 */
class Generic
{
    /**
     * @param string $psClass
     * @param array $paMap
     * @param array $paType
     * @param string $psTable
     * @param string $psKey
     * @param string $psValue
     * @return mixed|NULL
     */
    protected static function _getBy($psClass, $paMap, $paType, $psTable, $psKey, $psValue)
    {
        foreach ($paMap as $lsKey => $lsValue) {
            $laKey [$lsKey] = array('type'=> $paType[$lsKey]);
        }
        $laData = Mysql::getmysql()->readData($psTable, $laKey, $psKey, $psValue);
        if (!empty($laData)) {
            $loRetour = new $psClass($psValue);
            foreach ($paMap as $lsKey => $lsValue) {
                $loRetour->__set($lsValue, $laData[$lsKey]);
            }
        } else {
            $loRetour = null;
        }
        return $loRetour;
    }
}
