<?php
namespace DB3; //not the name of teh class is 'DB3\Type'
/**
 abstract class used as an ENUM and includes a function to get the binding type
 *can not be instantiated
 * @version 1.0
 * @author Ernesto Basoalto
 */
abstract class Type
{
    const INT = 'INTEGER';
    const TXT = 'TEXT';
    const NUM = 'NUMERIC';
    const REL = 'REAL';
    const NON = 'NONE';
    const VRC = 'VARCHAR';
    const NVC = 'NVARCHAR';
    const LNG = 'BIGINT';
    const DEC = 'DECIMAL';
    const DBL = 'DOUBLE';
    const BOL = 'BOOLEAN';
    const DAT = 'DATETIME';
    const BLB = 'BLOB';

    static public function bindType ($type)
    {
        switch($type)
        {
            case 'CHAR':
            case 'VARCHAR':
            case 'TINYTEXT':
            case 'TEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
            case 'NCHAR':
            case 'NVARCHAR':
            case 'CLOB':
            case 'DATE':
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'TIME':
                return SQLITE3_TEXT;
                break;
            case 'INTEGER':
            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'BIGINT':
            case 'INT2':
            case 'INT4':
            case 'INT8':
            case 'BOOLEAN':
                return SQLITE3_INTEGER;
                break;
            case 'REAL':
            case 'DOUBLE':
            case 'FLOAT':
            case 'NUMERIC':
            case 'DECIMAL':
                return SQLITE3_FLOAT;
                break;
            case 'BLOB':
                return SQLITE3_BLOB;
                break;
            default:
                return SQLITE3_TEXT;
                break;

        }
    }
}
/*
SQLite essentially uses these basic datatypes:

TEXT
INTEGER
NUMERIC
REAL
NONE

SQLite allows you to use the common datatype names

CHAR(size)	    Equivalent to TEXT (size is ignored)
VARCHAR(size)	Equivalent to TEXT (size is ignored)
TINYTEXT(size)	Equivalent to TEXT (size is ignored)
TEXT(size)	    Equivalent to TEXT (size is ignored)
MEDIUMTEXT(size)Equivalent to TEXT (size is ignored)
LONGTEXT(size)	Equivalent to TEXT (size is ignored)
NCHAR(size)	    Equivalent to TEXT (size is ignored)
NVARCHAR(size)	Equivalent to TEXT (size is ignored)
CLOB(size)	    Equivalent to TEXT (size is ignored)
TINYINT	    Equivalent to INTEGER
SMALLINT	Equivalent to INTEGER
MEDIUMINT	Equivalent to INTEGER
INTEGER	    Equivalent to INTEGER
BIGINT	    Equivalent to INTEGER
INT2	    Equivalent to INTEGER
INT4	    Equivalent to INTEGER
INT8	    Equivalent to INTEGER
NUMERIC	    Equivalent to NUMERIC
DECIMAL	    Equivalent to NUMERIC
REAL	    Equivalent to REAL
DOUBLE	    Equivalent to REAL
FLOAT	    Equivalent to REAL
BOOLEAN	    Equivalent to NUMERIC
DATE	    Equivalent to NUMERIC
DATETIME	Equivalent to NUMERIC
TIMESTAMP	Equivalent to NUMERIC
TIME	    Equivalent to NUMERIC
BLOB	    Equivalent to NONE

 */
