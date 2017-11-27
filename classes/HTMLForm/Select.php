<?php
namespace HTMLForm;
require_once 'InputBase.php';
/**
 * HTML Form Select has functions that render any part or
 * all the parts of an HTML select box including labels
 * and error mesages
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Select extends InputBase
{

	/**
     * constructor - generates a select element with the specified attributes
	 * if neither firstText or firstValue are specified then the first/default option will not be added to the select element
     * @param string $name - name attribute of element
     * @param array $optionItems - associative array specifing the value and text of each option in the select element
     * @param string $selectedKey - the value of the selected option
     * @param string $id - id attribute of the element - if not specified then name parameter is used
	 * @param string $firstText - the text displayed in the first/default option
	 * @param string $firstValue - the value attribute of the first/default option
     * @param string $attributeString - any number of attribute-value pairs for the element
     */
    function __construct($name,$optionItems,$selectedKey="0",$id="",$firstText="",$firstValue="",$attributeString="")
    {
        parent::__construct($name,$id);
        $this->html .= self::selectTag($this->name,$this->id,$attributeString);
        if(!empty($firstText) || !empty($firstValue))
        {
            //you may add logic here to see if the developer
            // wants to use the default values defined in the firstOption  function
            $this->html .= self::firstOption($firstText,$firstValue);
        }
        $this->html .= self::options($optionItems,$selectedKey);
        $this->html .= <<<EOT

    </select>
EOT;
    }



    /**
     * takes in an associative array and loops through the array
     * and echoes out just the select box options
     * The key of the array is the value of the option
     * The value of the array is the text displayed to the user
     * @param array $items - associative array that correspond to the option value and text
     * @param string $selectedKey - the array key that correspondes to the the selected option
     */
    static function options ($items,$selectedKey="0")
    {
        $htmlOptions = "";
         foreach($items as $key=>$val)
         {
             $sel = $selectedKey===$key ? "selected" : "";
             //.= short hand concatenation operator like += but for strings
             $htmlOptions .= <<<EOT

    <option value="$key" $sel>$val</option>
EOT;
        }
         return $htmlOptions;

    }


    /**
     * returns the first option of a select box
     * @param string $text - text to display to the user
     * @param string $value - value attribute of the option element
     */
    static function firstOption($text="...",$value="0")
    {
        return <<<EOT

    <option value="$value">$text</option>
EOT;
    }

    /**
     * returns the opening select tag
     * @param string $name - name attribute of the select tag
     * @param string $id - optional id attribute of the select tag
     * @param string $attributeString - open string for any number of
     * attribute value pairs
     */
    static function selectTag($name,$id="", $attributeString="")
    {
        return <<<EOT

    <select name="$name" id="$id" $attributeString>
EOT;

        //render the closing select tag in the constructor
    }

}