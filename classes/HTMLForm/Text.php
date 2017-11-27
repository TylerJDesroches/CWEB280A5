<?php
namespace HTMLForm;
require_once 'InputBase.php';
/**
 * generates a textarea element
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Text extends InputBase
{
    /**
     * constructor- generates a textarea element with the specified attributes
     * @param string $name - name attribute of element
     * @param string $value - value attribute of element - cleaned before display
     * @param string $id - id attribute of the element - if not specified then name parameter is used
     * @param string $attributeString - any number of attribute-value pairs for the element
     */	
    function __construct($name, $value='',$id='',$attributeString='')
    {
        parent::__construct($name,$id);//call parent to set properties
        $safeValue = htmlentities($value);
        $this->html .= <<<EOT

    <textarea name="{$this->name}" id="{$this->id}" $attributeString>$safeValue</textarea>
EOT;
    }
}