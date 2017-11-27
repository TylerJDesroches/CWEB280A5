<?php
namespace HTMLForm;
/**
 * Parent class for HTMLFormInput children
 * @version 1.0
 * @author Ernesto Basoalto
 */
class InputBase
{
    protected $html=""; //contains all the html to render a selectbox with options and label
    protected $name;
    public $id;
    private $labelled = false;

    /**
     * base class constructor needs name and optionally id
     * set this class properties with the same names
     * @param string $name 
     * @param string $id 
     */
    function __construct($name,$id="")
    {
        $this->name = $name;
        $this->id = empty($id)? $name : $id;
    }

    /**
     * echo out all the html created with the various functions
     */
    function render()
    {
        echo $this->html;
    }

    /**
     * prepend the html for a label before the selectbox html
     * @param string $label - text of the labe
     * @param string $for - id of the selectbox
     * @param string $attributeString - open string for any number of
     * attribute value pairs
     */
    function addLabel($label,$for="",$attributeString="")
    {
        if(!$this->labelled){
            $for = empty($for)? $this->id : $for;
            $htmlLabel = <<<EOT

    <label for="$for" $attributeString>$label</label>
EOT;
            $this->html = $htmlLabel.$this->html;
            $this->labelled = true;
        }
    }

    /**
     * appends the html for an error message after the selectbox html
     * @param bool $showError - whether or not to display the error message
     * @param string $errorMesage - text of the error message
     * @param string $class - css class attribute of the span element
     */
    function addError($showError,$errorMesage,$class="error")
    {
        if($showError)
        {
            $this->html .= <<<EOT

    <span class="$class">$errorMesage</span>
EOT;
        }
    }
}