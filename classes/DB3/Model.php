<?php
namespace DB3;
use DB3\Column;
use DB3\Type;
/**
 * a class that will add functionality to its children thnat will make using the database easier
 * the model class will also make validation of the child properties easier as well
 *
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Model
{
    private $cols; //variable to store an array of Column objects (one for each property of the data class)
    private $labels;
    private $errors;
    private $pkField;//stores the name of the property that acts as the primary key for the object

    /**
     * adds a new column object to the cols property array
     * @param string $field - the name of the column/field
     * @param DB3\Type $type - the type of coulumn in the database using the Type Enum we created
     * @param integer $size - integer that specifies the size of a string column
     * @param bool $isNullable -  specifies whether the column allows nulls to be stored in the table
     * @param bool $isPK - specifies if this column is the primary key for the table
     * @param bool $isAutoIncr - specifies whether the column is auto-incrementing
     */
    protected function defineColumn($field,$type=Type::TXT, $size=null, $isNullable = true, $isPK = false, $isAutoIncr =false)
    {
        //create new column object and add it to the cols array property
        $this->cols[$field] = new Column($field,$type, $size, $isNullable, $isPK, $isAutoIncr);
    }

    /**
     * returns the SQL string that will create the table for the current object
     * meant to be called from a child of this Model Class
     * @return \boolean|string
     */
    public function tableDefinition()
    {
        if(is_array($this->cols)){
            $table =  $this->tableName();
            $colString = implode(','.PHP_EOL,$this->cols); // creates a comma separated string of SQL column definitions
            return "CREATE TABLE IF NOT EXISTS $table ($colString)";
        }else{
            return false;
        }

    }

    /**
     * Returns the name of this class (meant to be called by a child of this Model class)
     * to be used as the table name in the database
     * this  function can be overridden by a child class so as to use any name the developer wants
     * @return string
     */
    public function tableName()
    {
        return get_class($this);
    }

    /**
     * checks if a particular property is valid and adds or removes
     * error messages from the errors array as neeeded
     * @param string $field - the name of the field being checked and the key of the error in the errors array
     * @param bool $isValid - whether or not the value for the field is valid
     * @param string $msg - a format string error message to be stored in the errors array - the placeholder is replaced by the field's label
     * @return bool - whether or not the value for the field is valid
     */
    protected function checkProperty($field,$isValid,$msg="%s is invalid.")
    {
        if($isValid)
        {
            //remove error from errors array
            unset($this->errors[$field]);
        }else{
            // add error to errors array
            //sub the property label in the error message for a more user friendly message
            $this->errors[$field] = sprintf($msg,$this->getLabel($field));
        }
        return $isValid;
    }

    /**
     * looks for an error message (in the errors array) that corresponds to the passed in field name 
	 * and if it exists returns the error message otherwise returns an empty string
     * @param string $field
     * @return string
     */
    public function getError($field)
    {
		if(!is_array($this->errors)){
			$this->validate();
		}
			
        return  is_array($this->errors) && isset($this->errors[$field])? $this->errors[$field] : '';
    }

    /**
     * This function will check all the properties' validation logic
     */
    public function validate()
    {
        //clean out the errors in the errors array
        $this->errors = array(); //set it equal to a new empty array to clear the items in the array

        // need find a way to check each property
        // lets uses a naming convention that any function that begins with 'validate_'
        //will be executed in this method
        $methods = get_class_methods($this);
        $validationResults = array();
        foreach($methods as $func)
        {
            //see if the function begins with the text 'validate_'
            if(strpos($func,'validate_')===0)
            {
                //call the method with the name stored in the $func variable
                $validationResults[] = call_user_func([$this,$func]);
            }
        }
        //check to see if any of the property validation functions returned false
        return !in_array(false,$validationResults);

    }

    /**
     * adds a new label to the labels array using the given field as the key
     * @param mixed $field
     * @param mixed $label
     */
    protected function setLabel($field,$label)
    {
        $this->labels[$field]=$label;
    }

    /**
     * looks for the label that corresponds to the passed in field and if it exists returns the label
     * otherwise returns what was passed in
     * @param string $field
     * @return string
     */
    public function getLabel($field)
    {
        return is_array($this->labels) && isset($this->labels[$field]) ? $this->labels[$field] : $field;
    }

    /**
     * looks for the corresponding column definition object for the passed in field name
     * if found returns the bind type otherwise returns  the default of SQLITE3_TEXT
     * @param mixed $field
     * @return mixed
     */
    public function getBindType($field)
    {
        return is_array($this->cols) && isset($this->cols[$field]) ? $this->cols[$field]->bindType : SQLITE3_TEXT ;
    }


    /**
     * looks through the column definition objects in the cols array for the field that is desginated as the pk
     * and sets the pkField property.
     * If the pkField is already set it will not run the loop it will just return the value of pkField
     * @return mixed
     */
    public function getPKField()
    {
        if(!isset($this->pkField) && is_array($this->cols))
        {
            foreach($this->cols as $field => $colDef)
            {
                if ($colDef->isPK)
                {
                    $this->pkField = $field;
                }
            }
        }
        return $this->pkField;
    }

    /**
     * determines the property that acts as the primary key and sets it to the passed in value
     * if the primary key field is not determined then false is returned
     * @param mixed $val - the new value of the primary key field - if val is null then the primary key value will be left unchanged
     * @return mixed - the current value of the primary key field
     */
    public function pk($val=null)
    {
        $pk = $this->getPKField(); //determines the property that acts as the primary key
        if(empty($pk)){ return false; } //if no pk is set then return false
        if(!is_null($val)){ $this->$pk = $val;} //set new value of pk field if not null
        return $this->$pk;//returns the actual value of the property that is the primary key
    }

    /**
     * will determine if a property is desginated as the primary key and returns whether or not the
     * primary key is Auto-incrementing otherwise returns false
     * @return mixed
     */
    public function isPKAutoIncr()
    {
        if(is_array($this->cols) && array_key_exists($this->getPKField(), $this->cols))
        {
            return $this->cols[$this->pkField]->isAutoIncr;
        }else{
            return false;
        }
    }
}