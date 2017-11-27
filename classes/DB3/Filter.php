<?php
namespace DB3;
/**
 * Use to help prepare a where clause for an sql statement
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Filter
{
    public $field;
    public $value;
    public $comparer;

    /**
     * sets the properties that are later used to generate an SQL where clause
     * @param string $field - the column/property in the db whose data is being compared
     * @param mixed $value - the value to compare against
     * @param string $comparer - how to compare the value in the table column with the value in the filter
     * if the value parameter is null then the comparer is changed to 'IS' or 'IS NOT' accordingly
     */
    public function __construct($field,$value,$comparer='=')
    {
        $this->field = $field;
        $this->value = $value;
        if(is_null($value))
        {
            $comparer = $comparer==='='?'IS':'IS NOT';
        }
        $this->comparer = $comparer;
    }

    /**
     * override to string method
     * @return string
     */
    public function __toString()
    {
        return "{$this->field} {$this->comparer} ?\n";
    }
}