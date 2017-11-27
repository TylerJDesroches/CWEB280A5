<?php
namespace HTMLTools;

/**
 * Generate an HTML table from an array of objects.
 *
 * @version 1.0
 * @author cst243
 */
class Table
{
    private $html;

    /**
     * Takes in an array of objects and prepares the html required to produce a table from the array.
     * @param mixed $objectArray the array to produce a table from
     * @param mixed $generateHeader whether or not to generate a table header based off the labels defined in the object
     * @param mixed $additionalProperties any additional properties to add to the opening table tag
     */
    public function __construct($objectArray, $generateHeader=true, $additionalProperties='')
    {
        // Gather some information about the object passed in
        $tableName = get_class($objectArray[0]); // we assume the table name is the same as the class name
        $props = get_class_vars($tableName); // we are assuming the first property is the primary key in the table
        $fields = array_keys($props); // get an array with just the field names

        // Generate the html and place it into a string to return in the render funciton
        // Create the start of the table
        $this->html .= <<<EOT

<table $additionalProperties>
EOT;


        // Generate a header if needed
        if ($generateHeader)
        {
            // produce a table row
            $this->html .= <<<EOT

    <tr>
EOT;
            // for each property of the object taken in, loop
            foreach ($fields as $field)
            {
                // For the current property in the object, get the label from it
            	$headerName = $objectArray[0]->getLabel($field);
                // Add it to the table html
                $this->html .= <<<EOT

        <th>$headerName</th>
EOT;
            }
            // Close off the row
            $this->html .= <<<EOT

    </tr>
EOT;
        }

        // generate the rest of the table data
        // for each object in the array...
        foreach ($objectArray as $currentObject)
        {
            // Start a row in the table
            $this->html .= <<<EOT

    <tr>
EOT;
            // For each property in the object, loop
            foreach ($fields as $currentField)
            {
                // Get the data of the current property 
                // Use html entities to make sure the data is safe to output to the page
            	$currentData = htmlentities($currentObject->$currentField);
                // Add table data with the data from the property
                $this->html .= <<<EOT

        <td>$currentData</td>
EOT;
            }

            // End the table row
            $this->html .= <<<EOT

    </tr>
EOT;
        }

        // Close off the table
        $this->html .= <<<EOT

</table>

EOT;
    }

    /**
     * Renders the already generated table html by echoing it out to the page
     */
    public function render()
    {
        echo $this->html;
    }

}