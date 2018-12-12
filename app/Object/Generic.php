<?php

namespace App\Object;

/**
 * @author david.richard
 *
 */
class Generic
{
    private $cbNew;
    private $cbUpdate;

    /**
     *
     * @param string $pProperty
     * @throws \Exception
     */
    public function __get($pProperty)
    {
        if (property_exists($this, $pProperty)) {
            return $this->$pProperty;
        } else {
            throw new \Exception('Objet ' . __CLASS__ . ' : Propriété inconnue : ' . $pProperty);
        }
    }

    /**
     *
     * @param string $pProperty
     * @param string $pValue
     * @throws \Exception
     */
    public function __set($pProperty, $pValue)
    {
        if (property_exists($this, $pProperty)) {
            $this->$pProperty = $pValue;
        } else {
            throw new \Exception('Objet ' . __CLASS__ . ' : Propriété inconnue : ' . $pProperty);
        }
    }

    public function _isNew()
    {
        return $this->cbNew;
    }

    public function _isUpdate()
    {
        return $this->cbUpdate;
    }

    /**
     */
    protected function _update()
    {
        $this->cbUpdate = true;
    }

    /**
     */
    protected function _nouveau()
    {
        $this->cbNew = true;
    }

    /**
     */
    private function __contruct()
    {
        $this->cbNew = false;
        $this->cbUpdate = false;
    }
}
