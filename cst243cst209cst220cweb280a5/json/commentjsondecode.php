<?php
session_start();
use DB3\DB3;
use DB3\Filter;

if(isset($_SESSION['member']))
{
    
}
else
{
    echo 'Not authenticated';
}

?>