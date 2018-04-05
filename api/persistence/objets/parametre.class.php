<?php
require_once 'objGeneric.class.php';

/**
 *
 * @author david.richard
 *
 */
class parametre extends objGeneric
{
    
    /**
     *
     * @var int
     */
    protected $ciPk;
    /**
     *
     * @var string
     */
    protected $csCode;
    /**
     *
     * @var string
     */
    protected $csValue;
    /**
     *
     * @var string
     */
    protected $csDescription;
    
    /**
     */
    public function __construct()
    {
        $liNbArg = func_num_args();
        $laArgs = func_get_args();
        switch ($liNbArg) {
            case 2:
                $this->_nouveau();
                // no break
            case 1:
                $this->__set("ciPk", $laArgs [0]);
                break;
            default:
                die('Utilise la factory !!');
        }
    }
    
    /**
     *
     * @return String
     */
    public function getCode()
    {
        return $this->__get("csCode");
    }
    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->__get("csValue");
    }
    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->__get("csDescription");
    }
    /**
     *
     * @return int
     */
    public function getKey()
    {
        return $this->__get("ciPk");
    }
    /**
     *
     * @return string
     */
    public function setCode($psCode)
    {
        if ($psNom != $this->csNom) {
            $this->__set("csCode", $psCode);
            $this->_update();
        }
    }

    /**
     * @param string $pValue
     */
    public function setValue($pValue)
    {
        if ($pPrenom != $this->csPrenom) {
            $this->__set("csValue", $pValue);
            $this->_update();
        }
    }
    /**
     * @param string $pDesc
     */
    public function setDescription($pDesc)
    {
        if ($pId != $this->csDescription) {
            $this->__set("csDescription", $pDesc);
            $this->_update();
        }
    }
    /**
     * @return string
     */
    public function renderInput()
    {
        return '<input name="' . $this->getCode() . '" value="' . $this->getValue() . '" type="hidden" />';
    }
    /**
     * @param string $pcSeparateur
     * @return string
     */
    public function renderUrl($pcSeparateur = '')
    {
        return $pcSeparateur . $this->getCode() . '="' . $this->getValue().'"';
    }
}
