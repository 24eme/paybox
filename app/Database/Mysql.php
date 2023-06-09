<?php

namespace App\Database;

/**
 * @author david.richard
 *
 */
class Mysql
{

    /**
     * @var string
     */
    const DBINIFILE = __DIR__.'/../../db.ini';

    /**
     * @var integer
     */
    const MYSQL_GET = 1;

    /**
     * @var integer
     */
    const MYSQL_PUT = 0;

    /**
     * @var mysql
     */
    private static $instance;

    /**
     * @var \PDO
     */
    private $coPdo;

    private function __contruct()
    {
    }

    /**
     * @return mysql
     */
    public static function getmysql()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        if (is_file(self::DBINIFILE)) {
            try {
                $db_con = parse_ini_file(self::DBINIFILE);
                $lsConnection = $db_con['driver'].':host=' . $db_con['host'] . ';dbname=' . $db_con['basename'];
                $arrExtraParam = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
                $this->coPdo = new \PDO($lsConnection, $db_con['user'], $db_con['password'], $arrExtraParam);
            } catch (\PDOException $e) {
                $msg = 'Erreur PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
                throw new \Exception('Connexion impossible à la base <br> message : ' . $msg);
            }
        } else {
            throw new \Exception('db.ini : File not found');
        }
    }

    /**
     * @param string $pTable
     * @param array  $paFields
     * @param string $psField
     * @param int    $pkey
     * @return array()
     */
    public function readData($pTable, $paFields, $psField, $pkey)
    {
        $lsFields = join(', ', $this->converteType($paFields, self::MYSQL_GET));
        $lsQuery = "SELECT " . $lsFields . " from " . $pTable . " WHERE " . $psField . "= :" . $psField . ";";
        $loStmt = $this->coPdo->prepare($lsQuery);
        $loStmt->bindValue(':' . $psField, $pkey);
        $loStmt->execute();

        if ($loStmt) {
            return $loStmt->fetch(\PDO::FETCH_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * @param array $paData
     * @param int   $piMode
     * @return array
     */
    public function converteType($paData, $piMode)
    {
        $laResultat = array();
        foreach (array_keys($paData) as $lsKey) {
            switch ($paData[$lsKey]['type']) {
            case 'date':
                if ($piMode == self::MYSQL_GET) {
                    $laResultat[$lsKey] = 'UNIX_TIMESTAMP(' . $lsKey . ') as ' . $lsKey;
                } elseif ($piMode == self::MYSQL_PUT) {
                    $laResultat[$lsKey] = 'FROM_UNIXTIME(' . $paData[$lsKey]['data'] . ')';
                } else {
                    die('Erreur de mode d\'accès');
                }
                break;
            case 'int':
                if ($piMode == self::MYSQL_GET) {
                    $laResultat[$lsKey] = $lsKey;
                } elseif ($piMode == self::MYSQL_PUT) {
                    $laResultat[$lsKey] = $paData[$lsKey]['data'];
                } else {
                    die('Erreur de mode d\'accès');
                }
                break;
            case 'string':
                if ($piMode == self::MYSQL_GET) {
                    $laResultat[$lsKey] = $lsKey;
                } elseif ($piMode == self::MYSQL_PUT) {
                    $laResultat[$lsKey] =  $this->coPdo->quote($paData[$lsKey]['data']) ;
                } else {
                    die('Erreur de mode d\'acces');
                }
                break;
            }
        }
        return $laResultat;
    }

    /**
     * @param string $pTable
     * @param array  $paData
     * @throws \Exception
     */
    public function insertData($pTable, $paData)
    {
        try {
            $lsFields = join(', ', array_keys($paData));
            $lsData = join(', ', $this->converteType($paData, self::MYSQL_PUT));
            $lsQuery = "INSERT INTO " . $pTable . "(" . $lsFields . ") VALUES (" . $lsData . ");";
            $loStatment = $this->coPdo->prepare($lsQuery);
            $res = $loStatment->execute();
        } catch (\PDOException $e) {
            $msg = 'Erreur PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            throw new \Exception('Erreur PDO : ' . $msg);
        }

        if (! $res) {
            throw new \Exception("[PDO] Erreur d'ecriture : ".implode(', ', $loStatment->errorInfo()));
        }
    }

    /**
     * @param string $pTable
     * @param string $psKey
     * @param array  $paData
     * @throws \Exception
     */
    public function updateData($pTable, $psKey, $paData)
    {
        try {
            $lsQuery = "UPDATE " . $pTable . " SET ";
            $laData = $this->converteType($paData, self::MYSQL_PUT);
            $cptPos = count($laData);
            foreach ($laData as $lsKey => $lsData) {
                $lsQuery .= $lsKey . " = " . $lsData;
                if ($cptPos > 1) {
                    $lsQuery .= ", ";
                }
                $cptPos--;
            }

            $lsQuery .= " WHERE " . $psKey . " = " . $paData[$psKey]['data'];
            $loStmt = $this->coPdo->exec($lsQuery);
        } catch (\PDOException $e) {
            $msg = 'Erreur PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            throw new \Exception('Erreur PDO : ' . $msg);
        }
    }

    /**
     * @param string $psId
     * @param string $psTable
     * @throws \Exception
     * @return int
     */
    public function getNextId($psId, $psTable)
    {
        try {
            $lsQuery = "SELECT IFNULL(max(" . $psId . "),0)+1 as id FROM " . $psTable . ";";
            $loStmt = $this->coPdo->query($lsQuery);
            if ($loStmt) {
                $laData = $loStmt->fetch(\PDO::FETCH_ASSOC);
                return $laData['id'];
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $msg = 'Erreur PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            throw new \Exception('Erreur PDO : ' . $msg);
        }
    }

    /**
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->coPdo->lastInsertId();
    }
}
