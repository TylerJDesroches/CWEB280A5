<?php

/**
 * Contains functionality for producing select boxes in HTML
 *
 * @version 1.0
 * @author Matthew Thompson, cst243
 */
class HTMLFormSelect
{
    private $html; // contains all the html to render a selectbox with options and label.

    function __construct($name, $optionItems, $selectedKey="0", $id="", $firstText="", $firstValue="", $attributeString="")
    {
        $this->html = "";
        $this->html .= $this->selectTag($name, $id, $attributeString);
        if(!empty($firstText) || !empty($firstValue))
        {
            // You may add logic here to see if the developer wants to use the default values defined in the firstOption
            $this->html .= $this->firstOption($firstText, $firstValue);
        }
        $this->html .= $this->options($optionItems, $selectedKey);
        $this->html .= <<<EOT

</select>
EOT;
    }

    /**
     * Echo out all the html created with the various functions.
     */
    function render()
    {
        echo $this->html;
    }


    /**
     * Takes in an associative array and echoes out options in a select box. The key of the array is the value for the select box,
     * and the value of the array is the text displayed to the user.
     * @param mixed $inArray The associative array to produce select box options from.
     */
    function options($items, $selectedKey="0")
    {
        $htmlOptions = "";
        // Loop through all the values in the array
        foreach ($items as $key=>$value)
        {
            $sel = ($selectedKey === $key) ? "selected" : "";
        	// Echo out the values in the array in the form of a select box option. Key as the value of the select box, the value as the text displayed.
            // .= short hand concatenation operator like += but for strings.
            $htmlOptions .= <<<EOT

<option value="$key" $sel>$value</option>
EOT;
        }

        return $htmlOptions;
    }

    // minicise 11
    // write a function that will echo out the first option in the selectbox.
    // you decide the parameters and the default values of those parameters.
    /**
     * Echoes out the first default option for a select box.
     * @param mixed $optionText The text for the option [default ...]
     * @param mixed $optionValue The value for the option [default 0]
     */
    function firstOption($optionText = "...", $optionValue="0")
    {
        return <<<EOT

<option value="$optionValue">$optionText</option>
EOT;
    }

    /**
     * echoes out the opening select tag
     * @param mixed $name name attribute of the select tag
     * @param mixed $id optional id attribute of the select tag
     * @param mixed $attributeString open string for any number of attribute value pairs
     */
    function selectTag($name, $id="", $attributeString="")
    {
        return <<<EOT

<select name="$name" id="$id" $attributeString>
EOT;
        // render the closing select tag in the constructor
    }

    /**
     * prepend the html for a label before the selectbox html
     * @param mixed $label text of the label
     * @param mixed $for id of the selectbox
     * @param mixed $attributeString open string for any number of attribute value pairs
     */
    function addLabel($label, $for, $attributeString)
    {
        $htmlLabel = <<<EOT

<label for="$for" $attributeString>$label</label>
EOT;
        $this->html = $htmlLabel . $this->html;
    }


    /**
     * appends the html for an error message after the selectbox html
     * @param bool $showError whether or not to display the error message
     * @param mixed $errorMessage text of the error message
     * @param mixed $class css class attribute of the span element
     */
    function addError($showError, $errorMessage, $class="error")
    {
        if ($showError)
        {
            $this->html .= <<<EOT

<span class="$class">$errorMessage</span>
EOT;
        }
    }
}