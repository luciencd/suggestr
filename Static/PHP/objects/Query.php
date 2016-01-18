<?php

require_once(ROOT.'Static/PHP/connect.php'); // Include DB connect
require_once(ROOT.'Static/PHP/objects/ORM.php');

class Query{
	
	private $table;
	
	public function __construct($table){
		/*
		 * $table - the name of the table
		 */
		$table = strtolower($table);
		if(preg_match('/^[A-Za-z0-9_]+$/', $table)&&in_array($table, array(
'action',
'courses',
'departments',
'model',
'sessions',
'tagaction',
'tags',
'year',
'sliders',
'slideraction',
'majorrelations')))
			$this->table = ucfirst($table);
		else
			throw new Exception("Unable to create Query instance!");
	}
	
	public function select($fields='*', $where='', $order='', $limit='', $returnORM=true, $debug=false, $justQueryString=false){
		/*
		 * List of optional fields:
		 * 	$fields
		 * 		- a string containing the one field to select.
		 * 		- or an array containing all of the fields to select.
		 *		- do not include: ``
		 * 	$where
		 *		- a two-dimensional array of where statements.
		 *		- for example: array(array('id', '!=', 100), array('firstname', '=', 'Leo'))
		 *	$order
		 * 		- a two element array containing the field to order by, and the order method (ASC or DESC)
		 *		- for example: array('id', 'DESC')
		 * 	$limit
		 *		- maximum number of desired fields to return.
		 *  $returnORM
		 *  	- Decides what return type is desired.
		 *			- If set to true (recommended), an array of ORM objects will be returned (of the correct sub-objects: User, Event, Host, etc.).
		 *			- If set to false, the actual MySQL query result will be returned. This is primary available due to the ORM class itself
		 *			  needing to use this Query class.
		 *  $returnORM
		 *      - If an array of ORMs should be returned as the result set.
		 *  $debug
		 *      - If the resulting SQL query should be printed before being returned (for debugging).
		 *  $justQueryString
		 *      - If the resulting query string should only be printed and no result set should be returned.
		 */
		$statement = 'SELECT ';
		$relationalTables = array('conversationtouser','userCommitsEvent','friends');
		if(in_array($this->table, $relationalTables)){
			$statement .= '* ';
		}elseif($returnORM==false){
			if(is_string($fields)&&$fields!='*'&&preg_match('/^[A-Za-z0-9_]+$/', $fields)) /* Select fields */
				$statement .= '`'.$fields.'` ';
			elseif(is_array($fields)){
				foreach($fields as $f){
					if(preg_match('/^[A-Za-z0-9_]+$/', $f))
						$statement .= '`'.$f.'`, ';
				}
				$statement = substr($statement, 0, -2).' ';
			}else
				$statement .= '* ';
		}else{
			$statement .= '`id` '; // If we are using ORM objects
		}
		$statement .= 'FROM `'.$this->table.'` ';
		if(is_array($where)){ /* Where */
			if(count($where)>0)
				$statement .= 'WHERE ';
			$connectorAdded = false; // No connector added for this selector yet
			$validWhere = false;
			foreach($where as $cond){
				if(is_string($cond)){
					if(in_array(strtoupper(trim($cond)), array('(', ')', 'AND', 'OR'))){
						$statement .= strtoupper(trim($cond)).' '; // Add an ), (, OR, or AND statement.
						$connectorAdded = true; // Connector added
					}
				}else{
					if(in_array($cond[1], array('=', '!=', '<>', '>', '<', '>=', '<=', '!<', '!>', 'LIKE'))){
						if(is_array($cond)&&count($cond)==3&&preg_match('/^[A-Za-z0-9_]+$/', $cond[0])){
							if(!$validWhere){
								$statement .= '`'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" ';
								$validWhere = true;
								$connectorAdded = false; // Connector has been used, now set back to False.
							}else{
								if(!$connectorAdded) // Is a connector needed?
									$statement .= 'AND `'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" '; // A connector is needed, add an AND to the beginning...
								else{
									$statement .= ' `'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" '; // Connector was already added...
									$connectorAdded = false; // Connector has been used, now set back to False.
								}
							}
						}
					}
				}
			}
		}
		if(is_array($order)&&preg_match('/^[A-Za-z0-9_]+$/', $order[0])&&in_array($order[1], array('ASC', 'DESC'))) /* Order */
			$statement .= 'ORDER BY `'.$order[0].'` '.$order[1].' ';
		if(strlen($limit)>0) /* Limit */
			$statement .= ' LIMIT '.$limit;
		//$debug=false;
		if($debug){
			echo $statement;
		}
		$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);
		if($justQueryString==true){
			return $statement;
		}elseif($returnORM==false){
			return $result;
		}else{
			if($result==false||mysqli_num_rows($result)==0)
				return array();
			$array = array();
			while($row=mysqli_fetch_array($result)){
				$newORM = new ORM($this->table);
				if(array_key_exists('id', $row))
					$newORM->findById($row['id']);
				else{
					$finalArray = array();
					foreach($row as $k=>$v){
						if(!is_numeric($k)){
							array_push($finalArray, array($k, $v));
						}
					}
					$newORM->findByXs($finalArray);
				}
				array_push($array, $newORM);
			}
			return $array; // Return an array of ORM objects
		}
	}
	
	public function union($fieldsOfThisTable='*', $whereOfThisTable='', $queries){
		/*
		 * Used to create an SQL union statement.
		 *  $fieldsOfThisTable
		 * 		- a string containing the one field to select (from the current table).
		 * 		- or an array containing all of the fields to select (from the current table).
		 *		- do not include: ``
		 *  $whereOfThisTable
		 *      - a two-dimensional array of where statements (for the current table).
		 *		- for example: array(array('id', '!=', 100), array('firstname', '=', 'Leo'))
		 *  $queries
		 *      - an n-element array where each element is in this format:
		 *          - array($n-thElement_tableName, $n-thElement_fields, $n-thElement_where)
		 *          - where:
		 *              - $n-thElement_tableName
		 *                  - represents the name of the table this query is for.
		 *              - $n-thElement_fields
		 *                  - represets a string containing the one field to select (from this query's table).
		 * 		            - or an array containing all of the fields to select (from this query's table).
		 *		            - do not include: ``
		 *              - $n-thElement_where
		 *                  - represents a two-dimensional array of where statements (for the current table).
		 *		            - for example: array(array('id', '!=', 100), array('firstname', '=', 'Leo'))
		 */
		for($i=0;$i<count($queries);$i++){
			if(count($queries[$i])!=3)
				return false;
			else{
				if(!preg_match('/^[A-Za-z0-9_]+$/', strtolower($queries[$i][0]))||!in_array(strtolower($queries[$i][0]), array(
'action',
'courses',
'departments',
'model',
'sessions',
'tagaction',
'tags',
'year',
'sliders',
'slideraction',
'majorrelations'))){
					return false;
				}
			}
		}
		$str = $this->select($fieldsOfThisTable, $whereOfThisTable, '', '', false, false, true);
		for($i=0;$i<count($queries);$i++){
			$ORM = new Query($queries[$i][0]);
			$str .= ' UNION ';
			$str .= $ORM->select($queries[$i][1], $queries[$i][2], '', '', false, false, true);
		}
		return mysqli_query($GLOBALS['CONFIG']['mysqli'], $str);
	}
	
	public function insert($pairs){
		/*
		 * $pairs - an array of field-value pairs to insert into the database.
		 * Returns an array: array((true/false whether the query was successful), (id of new row));
		 */
		if(is_array($pairs)&&count($pairs)>0){
			$statement = 'INSERT INTO `'.$this->table.'` (';
			foreach($pairs as $p){
				if(preg_match('/^[A-Za-z0-9_]+$/', $p[0])){
					$statement .= '`'.$p[0].'`, ';
				}
			}
			$statement = substr($statement, 0, -2).') VALUES (';
			foreach($pairs as $p){
				$statement .= '"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $p[1]).'", ';
			}
			$statement = substr($statement, 0, -2).')';
			$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);
			return array($result, mysqli_insert_id($GLOBALS['CONFIG']['mysqli']));
		}else
 			return false;
	}
	
	public function delete($where){
		/*
		 * 	$where
		 *		- a two-dimensional array of where statements.
		 *		- for example: array(array('id', '!=', 100), array('firstname', '=', 'Leo'))
		 */
		$statement = 'DELETE FROM `'.$this->table.'` ';
		if(is_array($where)){ /* Where */
			if(count($where)>0)
				$statement .= 'WHERE ';
			else
				throw new Exception("Unable to delete fields!");
			$connectorAdded = false; // No connector added for this selector yet
			$validWhere = false;
			foreach($where as $cond){
				if(is_string($cond)){
					if(in_array(strtoupper(trim($cond)), array('(', ')', 'AND', 'OR'))){
						$statement .= strtoupper(trim($cond)).' '; // Add an ), (, OR, or AND statement.
						$connectorAdded = true; // Connector added
					}
				}else{
					if(in_array($cond[1], array('=', '!=', '<>', '>', '<', '>=', '<=', '!<', '!>'))){
						if(is_array($cond)&&count($cond)==3&&preg_match('/^[A-Za-z0-9_]+$/', $cond[0])){
							if(!$validWhere){
								$statement .= '`'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" ';
								$validWhere = true;
								$connectorAdded = false; // Connector has been used, now set back to False.
							}else{
								if(!$connectorAdded) // Is a connector needed?
									$statement .= 'AND `'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" '; // A connector is needed, add an AND to the beginning...
								else{
									$statement .= ' `'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" '; // Connector was already added...
									$connectorAdded = false; // Connector has been used, now set back to False.
								}
							}
						}
					}
				}
			}
		}else
			throw new Exception("Unable to delete fields!");
		$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);
		if(mysqli_affected_rows($GLOBALS['CONFIG']['mysqli'])>0) // Returns true if delete was successful, false if not.
			return true;
		else
			return false;
	}
	
	public function update($changes, $where){
		/*
		 *  $changes - an array of changes to make to the table.
		 * 	$where (not optional for security reasons)
		 *		- a two-dimensional array of where statements.
		 *		- for example: array(array('id', '!=', 100), array('firstname', '=', 'Leo'))
 		 */
 		if(is_array($changes)&&count($changes)>0&&is_array($changes[0])&&count($changes[0])==2&&preg_match('/^[A-Za-z0-9_]+$/', $changes[0][0])){
 			$statement = 'UPDATE `'.$this->table.'` SET ';
 			foreach($changes as $c){
 				if(is_array($c)&&count($c)==2&&preg_match('/^[A-Za-z0-9_]+$/', $c[0])){
 					if($c[0]=='lastloginPlace') // This is for running the special built-in SQL function on the `lastloginPlace` field.
		 				$statement .= '`'.$c[0].'`=INET_ATON("'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $c[1]).'"), ';
		 			else
		 				$statement .= '`'.$c[0].'`="'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $c[1]).'", ';
	 			}
 			}
 			$statement = substr($statement, 0, -2).' ';
 			if(is_array($where)&&is_array($where[0])){ /* Where */
 				$validWhere = false;
				foreach($where as $cond){
					if(in_array($cond[1], array('=', '!=', '<>', '>', '<', '>=', '<=', '!<', '!>'))){
						if(is_array($cond)&&count($cond)==3&&preg_match('/^[A-Za-z0-9_]+$/', $cond[0])){
							if(!$validWhere){
								$statement .= 'WHERE ';
								$statement .= '`'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" ';
								$validWhere = true;
							}else
								$statement .= 'AND `'.$cond[0].'`'.$cond[1].'"'.mysqli_real_escape_string($GLOBALS['CONFIG']['mysqli'], $cond[2]).'" ';
						}
					}
				}
				$statement = substr($statement, 0, -1);
				return mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);
			}else
				return false;
 		}else
 			return false;
	}
	
	public function getColsArry(){
		/* Returns array of all field names in the table */
		$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], 'DESCRIBE `'.$this->table.'`');
		if(!$result||mysqli_num_rows($result)<1)
			throw new Exception("Unable to retrieve table columns!");
		$a = array();
		while($row=mysqli_fetch_assoc($result)){
			$a[$row['Field']] = '';
			//echo "<h3> a: ".$row['Field']."</h3>";
		}
		return $a;
	}
	
	public function nextId(){
		/* Returns next auto_increment id of `table` */
		$query = mysqli_query($GLOBALS['CONFIG']['mysqli'], "SHOW TABLE STATUS LIKE '".$this->table."'");
		$row = mysqli_fetch_array($query);
		return $row['Auto_increment'];
	}
}

?>