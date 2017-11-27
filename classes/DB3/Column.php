<?php
namespace DB3;
/**
 class used to define columns when creating a table in an sql respository
 *
 * @version 1.0
 * @author Ernesto Basoalto
 */
class Column
{
    private $name;
    private $type;
    private $size;
    private $isNullable;
    public $isPK;
    public $isAutoIncr;
    public $bindType;

    /**
     * this class takes in column name and the rest are optional
     * also figures out the bindType based on the column type
     * @param mixed $name - the name of the column/field
     * @param mixed $type - the type of coulumn in the database using the Type Enum we created
     * @param mixed $size - integer that specifies the size of a string column
     * @param mixed $isNullable -  specifies whether the column allows nulls to be stored in the table
     * @param mixed $isPK - specifies if this column is the primary key for the table
     * @param mixed $isAutoIncr - specifies whether the column is auto-incrementing
     */
    public function __construct($name,$type=Type::TXT, $size=null, $isNullable = true, $isPK = false, $isAutoIncr =false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->isNullable = $isNullable;
        $this->isPK = $isPK;
        $this->isAutoIncr = $isAutoIncr;
        $this->bindType = Type::bindType($type);
    }

    /**
     * overwrite the to string function to return an sql column defintion string
     */
    public function __toString()
    {
        $sql = $this->name .  ' ' . $this->type;
        $sql .= is_int($this->size)? "({$this->size})" : '';
        $sql .= $this->isPK || $this->isNullable ? '' : ' NOT NULL';
        $sql .= $this->isPK ? ' PRIMARY KEY' : '';
        $sql .= $this->isAutoIncr? ' AUTOINCREMENT' : '';

        return $sql;
    }
}