<?php
echo $GLOBALS['CONFIG']['db-address'].'<br>';
echo $GLOBALS['CONFIG']['db-username'].'<br>';
echo $GLOBALS['CONFIG']['db-password'].'<br>';
echo $GLOBALS['CONFIG']['db-database'].'<br>';
var_dump(function_exists('mysqli_connect'));
$GLOBALS['CONFIG']['mysqli'] = mysqli_connect($GLOBALS['CONFIG']['db-address'], 
											  $GLOBALS['CONFIG']['db-username'], 
											  $GLOBALS['CONFIG']['db-password'], 
											  $GLOBALS['CONFIG']['db-database']); 
?>