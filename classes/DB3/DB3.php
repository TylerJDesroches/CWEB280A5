<?php
namespace DB3; //this further identities this class now this class is a DB3\DB3 class
spl_autoload_register(function ($class) {
    require_once '..\\' .$class . '.php';
    // ..\classes\DB3\DB3.php
});
/**
 * DB3 extends the SQLite3 extension in php
 *
 * this class will execute various database commands on a database for a passed in model class
 * NOTE: any external class references (any class outside of the db namespace) you must put a \ infront of the class name
 * @version 3.0
 * @author Ernesto Basoalto
 */
class DB3 extends \SQLite3
{
    /**
     * checks to see if the passed in class as a corresponding record in the database
     * @param Model $class an object class with public properties
     */
    public function exists($class){

        $table = $class->tableName(); //assumes class extend Model and has teh tableName method
        $pk = $class->getPKField(); //assumes the class extends Model and has the getPKField function

        //prepare a statement for the query
        $stmt = $this->prepare("SELECT COUNT($pk) FROM $table WHERE $pk=?");
        $stmt->bindValue(1,$class->pk(),$class->getBindType($pk)); //assume that class extends Model and has a getBindType method
        $result = $stmt->execute();
        if(!$result) //if the query did not succeed
        {
            return 0;
        }else{
            //return the first item out of the returned numeric array
            $res = $result->fetchArray(SQLITE3_NUM)[0]; //save result before closing statement
            $stmt->close(); //best practice to close a statement to prevent a database lock
            return $res;
        }

    }


    /**
     * inserts a populated class into a corresponding table record
     * assumes the classname is the same as the table name
     * @param Model $class
     * @return boolean
     */
    public function insert($class)
    {
		if(!$class->validate()){
			return false;
		}
        $table = $class->tableName(); //assumes class extend Model and has the tableName method
        $fieldValues = get_object_vars($class);//get array with properties/column and values
        $pk = $class->getPKField();
        if($class->isPKAutoIncr()){unset($fieldValues[$pk]);}
        $fieldList = implode(',',array_keys($fieldValues));
        $qmarks = rtrim(str_repeat('?,',count($fieldValues)), ',' );  //add a question mark for every field

        $stmt = $this->prepare("INSERT INTO $table ($fieldList) VALUES($qmarks)");
        $i=1;
        foreach($fieldValues as $field=>$value){
            $stmt->bindValue($i,$value,$class->getBindType($field));
            $i++;
        }

        $result = $stmt->execute();

        if(!$result){
            return false;
        }else {
            //check if the pk is autoincrementing , if so set the pk fields value in the class
            if($class->isPKAutoIncr()){
                //should be the value of the primary key that got generated for the last insert
                $class->pk($this->lastInsertRowID());
            }
            $stmt->close(); //best practice to close a statement to prevent a database lock
            return true;
        }

    }

    /**
     * This function updates the values in the table for the row that correspondes to the passed in class
     * does not update the primary key instead uses the pk to find the corresponding row
     * @param Model $class
     * @return boolean
     */
    public function update($class)
    {
		if(!$class->validate()){
			return false;
		}
        $table = $class->tableName(); //assumes class extend Model and has the tableName method
        $fieldValues = get_object_vars($class);//get array with properties/column and values
        $pk = $class->getPKField();
        unset($fieldValues[$pk]);
        $setList = implode("=?,\n",array_keys($fieldValues)).'=?';

        $stmt = $this->prepare("UPDATE $table SET $setList WHERE $pk=?");

        $i=1;
        foreach($fieldValues as $field=>$value){
            $stmt->bindValue($i,$value,$class->getBindType($field));//assume that class extends Model and has the getBindType method
            $i++;
        }
        $stmt->bindValue($i,$class->pk(), $class->getBindType($pk)); //assume that class extends Model and has the getBindType method

        $result = $stmt->execute();

        if(!$result){
            return false;
        }else{
            //you may want to return the number of affected rows
            $stmt->close(); //best practice to close a statement to prevent a database lock
            return true;
        }
    }

    /**
     * This function takes in a class with the pk property populated
     * then uses the pk value to find a corresponding row in the table
     * and sets all the other properties in the class from the corresponding column in the row
     * @param Model $class
     * @return boolean
     */
    public function select ($class)
    {
        $table = $class->tableName(); //assumes class extend Model and has the tableName method
        $fieldValues = get_object_vars($class);//get array with properties/column and values
        $pk = $class->getPKField();
        unset($fieldValues[$pk]);
        $fieldList = implode(',',array_keys($fieldValues));

        $stmt = $this->prepare("SELECT $fieldList FROM $table WHERE $pk=?");
        $stmt->bindValue(1,$class->pk(),$class->getBindType($pk)); //assume that class extends Model and has the getBindType method

        $result = $stmt->execute();

        if(!$result){
            return false;
        }else{
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $stmt->close();
            foreach($row as $col=>$val)
            {
                $class->$col = $val; //set the value of the class property with the same name as the column
            }
            return true;
        }
    }

    /**
     * this function takes in an empty class and returns an array of the same class
     * each class is then populated from the corresponding row in the db table
     * @param mixed $class
     * @return \boolean|object[]
     */
    public function selectAll($class)
    {
        return $this->selectSomeOrder($class);
    }

    /**
     * this function takes in a class and an array of filters and returns an array of objects
     * that meet the criteria.
     * @param Model $class
     * @param array $filters - array of Filter objects
     * @param bool $andOperator - true join teh filters with AND , false join filters with OR
     * @return \boolean|object[]
     */
    public function selectSome($class, $filters=array(), $andOperator=true)
    {
        return $this->selectSomeOrder($class,array(),$filters,$andOperator);
    }
    /**
     * this function takes in a class ,an array of orderby fields and an array of filters and returns an array of objects
     * that meet the criteria and ordered as specified.
     * @param Model $class
     * @param array $orders - associative array, field names are the keys and DESC or ASC for the values
     * @param array $filters - array of Filter objects
     * @param bool $andOperator - true join teh filters with AND , false join filters with OR
     * @return \boolean|object[]
     */
    public function selectSomeOrder($class, $orders=array() ,$filters=array(), $andOperator=true)
    {
        $table = $class->tableName(); //assumes class extend Model and has the tableName method
        $fieldValues = get_object_vars($class);//get array with properties/column and values
        $fieldList = implode(',',array_keys($fieldValues));

        //remove invalid field names
        $orders = array_intersect_key($orders,$fieldValues);
        //flatten the array key (field name) and the array value (order by direction DESC or ASC)
        array_walk($orders, function(&$val,$key){$val = $val==='DESC' ? "$key $val" : $key;});
        //if orders is empty then orderby the primary key field which is a reasonable default behavior
        $orderList = empty($orders)?$class->getPKField(): implode(",\n",$orders);

        // Determine which operator to connect the filters - either the AND operator or the OR operator
        $oper = $andOperator? ' AND ': ' OR ';

        //remove filters with invalid fields
        $filters = array_filter($filters,
            function($filt)use($fieldValues){
                return array_key_exists($filt->field,$fieldValues);
            });
        //convert the filters to strings and implode the with the operator determnied above
        $filterList= empty($filters)?'1=1':implode($oper,$filters);//if no filters use a where clause that returns all rows

        $stmt = $this->prepare("SELECT $fieldList FROM $table WHERE $filterList ORDER BY $orderList");

        $i=1;
        foreach($filters as $filt){
            $stmt->bindValue($i,$filt->value,
                is_null($filt->value)? SQLITE3_NULL :$class->getBindType($filt->field)
            );//assume that class extends Model and has the getBindType method
            $i++;
        }

        $result = $stmt->execute();

        if(!$result){
            return false;
        }else{
            $items = array(); //create blank array to fill and return
            while($row = $result->fetchArray(SQLITE3_ASSOC))
            {
                $item = new $table(); //using the class name to create an instance of the class
                foreach($row as $col=>$val)
                {
                    $item->$col = $val; //set the value of the class propert with the same name as the column
                }

                $items[]=$item; //add the new class to the return array
            }
            $stmt->close();
            return $items;
        }

    }

   /**
     * checks to see if the passed in class has a corresponding record in the database
	 * and it does not the data in the object will be inserted into the data table
	 * otherwise updates the values in the table for the row that correspondes to the passed in class
     * does not update the primary key instead uses the pk to find the corresponding row
     * @param Model $class an object class with public properties
     * @return boolean
     */
    public function save($class){
        if($this->exists($class)) {
            return $this->update($class);
        }else{
            return $this->insert($class);
        }
    }


    /**
     * This function takes in a class with the pk property populated
     * then uses the pk value to find a corresponding row in the table
     * and deletes the row
     * @param Model $class
     * @return boolean
     */
    public function delete ($class)
    {
        $table = $class->tableName(); //assumes class extend Model and has the tableName method
        $pk = $class->getPKField();

        $stmt = $this->prepare("DELETE FROM $table WHERE $pk=?");
        $stmt->bindValue(1,$class->pk(),$class->getBindType($pk)); //assume that class extends Model and has the getBindType method

        $result = $stmt->execute();

        if(!$result){
            return false;
        }else{
            //you may want to return the number of affected rows
            $stmt->close(); //best practice to close a statement to prevent a database lock
            return true;
        }
    }

}