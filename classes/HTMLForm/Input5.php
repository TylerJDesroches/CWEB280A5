<?php
namespace HTMLForm;
require_once 'Input.php';
/**
 * generates an input element with some html5 attributes
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Input5 extends Input
{
    /**
     * constructor extends HTMLForm Input and uses the parent to render the input element/tag
     * @param string $name - name attribute of element
     * @param string $type - type attribute of element
     * @param string $value - value attribute of element - cleaned before display
     * @param string $id - id attribute of the element - if not specified then name parameter is used
     * @param bool $required - specifies whether or not to add the required attribute to the element
     * @param string $regex - specifies the pattern attribute of the element - if not specified the pattern attribute is not added to the element
     * @param string $attributeString - any number of attribute-value pairs for the element
     */
    function __construct($name, $type, $value='',$id='', $required=false, $regex='', $attributeString='')
    {
        $attributeString .= !$required ? '' : ' required';
        $attributeString .= empty($regex) ? '' : ' pattern="'.$regex.'"';
        parent::__construct($name, $type, $value, $id, $attributeString);
    }
}