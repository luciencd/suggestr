<?php

$GLOBALS['CONFIG']['mysqli'] = mysqli_connect($GLOBALS['CONFIG']['db-address'], 
											  $GLOBALS['CONFIG']['db-username'], 
											  $GLOBALS['CONFIG']['db-password'], 
											  $GLOBALS['CONFIG']['db-database']); 
?>