<?php
require_once ('api/bd/mysql.class.php');

/**
 * @author david.richard
 *
 */
class factGeneric {
	
	/**
	 * @param string $psClass
	 * @param array $paMap
	 * @param array $paType
	 * @param string $psTable
	 * @param string $psKey
	 * @param string $psValue
	 * @return mixed|NULL
	 */
	protected static function _getBy ($psClass, $paMap, $paType, $psTable, $psKey, $psValue) {
		foreach ( $paMap as $lsKey => $lsValue ) {
			$laKey [$lsKey] = array('type'=> $paType[$lsKey]);
		}
		$laData = mysql::getmysql ()->readData ( $psTable, $laKey , $psKey, $psValue );
		if (!empty($laData)) {
			$loRetour = new $psClass ($psValue);
			foreach ( $paMap as $lsKey => $lsValue ) {
				$loRetour->__set ( $lsValue, $laData[$lsKey] );
			}
		} else {
			$loRetour = null;
		}
		return $loRetour;
	}
	
}
