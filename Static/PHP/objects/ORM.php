<?php

require_once(ROOT.'Static/PHP/objects/Query.php');

class ORM{ /* Object-relational mapping class */
	
	private $table, $dirty = array();
	protected $data, $query, $id, $virgin;
	
	public function __construct($table){
		/* Creates Query object */
		if(!in_array(strtolower($table), array(
'action',
'courses',
'departments',
'model',
'sessions',
'tagaction',
'tags',
'year')))
			throw new Exception('Table name invalid!');
		$this->table = $table;
		$this->query = new Query($this->table);
		$this->data = $this->query->getColsArry();
		$this->virgin = true; // Since FindById hasn't been run, must assume
	}
	
	public function type(){
		return $this->table;
	}
	
	public function findById($id){
		/* Finds an ID from table and sets that row's data to this object. */
		$id = trim($id);
		if(!is_numeric($id)){
			throw new Exception('Id must be a number!');
		}
		if($this->data = $this->query->select('*', array(array('id', '=', $id)), '', 1, false)){
			$this->data = mysqli_fetch_array($this->data);
			if(isset($this->data['id'])){
				$this->id = $this->data['id'];
				$this->virgin = false;
			}else
				throw new Exception("Query failed: no `id` returned!");
		}else
			throw new Exception("Query failed: no such row!");
	}
	
	public function findByXs($fieldsAndValues){
		/* Finds the row by the fields from table and sets that row's data to this object. */
		$deleteArgumentArray = array();
		$i = 0;
		foreach($fieldsAndValues as $value){ // Delete based on values of every single field
			if(count($value)!=2)
				throw new Exception("Query failed: two arguments must be given!");
			$deleteArgumentArray[$i] = array($value[0], '=', $value[1]);
			$i++;
		}
		if($this->data = $this->query->select('*', $deleteArgumentArray, '', 1, false)){
			if(mysqli_num_rows($this->data)==0){
				throw new Exception("Query failed: no such row!");
			}
			$this->data = mysqli_fetch_array($this->data);
			if(isset($this->data['id']))
				$this->id = $this->data['id']; // Only add id if it exists
			$this->virgin = false;
		}else
			throw new Exception("Query failed: no such row!");
	}
	
	public function get($field){
		/* Get a piece of stored data. */
		if(!is_numeric($field)&&isset($this->data[$field])){
			// NOTE: This removes all backslashes in data (*this may need to be augmented*)
			return str_replace('\\', '', $this->data[$field]);
		}else{
			return false;
		}
	}
	
	public function set($field, $value){
		/* Set a piece of data to a given value. **NOTE** This does NOT save this change to the table, use save(). */
		if(!is_numeric($field)&&isset($this->data[$field])){
			if(!isset($this->dirty[$field]))
				array_push($this->dirty, $field); // Mark this as dirty
			$this->data[$field] = $value;
		}else
			return false;
	}
	
	public function push($field, $value){
		/* Add $value to a field that is a json encoded array. **NOTE** This does NOT save this change to the table, use save(). */
		$array = json_decode($this->data[$field]);
		if(!is_numeric($field)&&isset($this->data[$field])&&json_last_error()==JSON_ERROR_NONE&&is_array($array)){
			if(!isset($this->dirty[$field]))
				array_push($this->dirty, $field); // Mark this as dirty
			array_push($array, $value);
			$this->data[$field] = json_encode($array);
		}else
			return false;
	}
	
	public function save(){
		/* Save all dirty (modified) values in the object to the table.
		 * If a new row is created in the database (an insert), the `id` field of this object is updated to the new value.
		 * NOTE: if the saved ORM is new, the `id` field is appropriately updated to its new value.
		 */
		$a = array();
		foreach($this->dirty as $d){
			array_push($a, array($d, $this->data[$d]));
		}
		if(count($a)>0){
			if(!$this->virgin){
				if(!is_numeric($this->id)||$this->id<1)
					return false; // Since you can't save/update data of fields on tables that have no id field.
				if($this->query->update($a, array(array('id', '=', $this->id))))
					return true;
				else
					return false;
			}else{
				$insert = $this->query->insert($a);
				if($insert[0]){
					if($insert[1]>0) // Only update the id field if the table contains an auto-incrementing field.
						$this->set('id', $insert[1]);
					return true;
				}else
					return false;
			}
		}else
			return true;
	}
	
	public function delete(){
		/*
		 * Delete row from database and remove everything from the data array
		 */
		if(!$this->virgin){
			if(!is_numeric($this->id)||$this->id<1){ // Is a row from a table without an id field
				$deleteArgumentArray = array();
				$i = 0;
				foreach($this->data as $key=>$value){ // Delete based on values of every single field
					if(!is_numeric($key))
						$deleteArgumentArray[$i] = array($key, '=', $value);
					$i++;
				}
				$worked = $this->query->delete($deleteArgumentArray);
			}else
				$worked = $this->query->delete(array(array('id', '=', $this->id)));
			if($worked){ // This will delete this row from the table...
				// Now we must delete the data from this object's array
				foreach($this->data as $key=>$value)
					unset($this->data[$key]);
				foreach($this->dirty as $key=>$value)
					unset($this->dirty[$key]);
				$this->id = 0;
			}else
				return false;
		}else
			return false;
	}
}

// class User extends ORM{

// 	public function __construct(){
// 		parent::__construct('users');
// 	}

// 	public function findByEmail($email){
// 		/* Finds an email from table and sets that row's data to this object. */
// 		$email = trim($email);
// 		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
// 			throw new Exception("You must pass an email address to ".__FUNCTION__."()");
// 		if($this->data = $this->query->select('*', array(array('email', '=', $email)), '', 1, false)){
// 			$this->data = mysqli_fetch_array($this->data);
// 			if(isset($this->data['id'])){
// 				$this->id = $this->data['id'];
// 				$this->virgin = false;
// 			}else
// 				throw new Exception("Query failed: no `id` returned!");
// 		}else
// 			throw new Exception("Query failed: no such row!");
// 	}
// }

// class Tag extends ORM{
	
// 	public function __construct(){
// 		parent::__construct('tags');
// 	}
	
// 	public function findByName($name){
// 		/* Finds an name from table and sets that row's data to this object. */
// 		$name = trim($name);
// 		if(strlen($name)<0||strlen($name)>50)
// 			throw new Exception("You must pass a name to ".__FUNCTION__."() that is between 1 and 50 characters.");
// 		if($this->data = $this->query->select('*', array(array('name', '=', $name)), '', 1, false)){
// 			$this->data = mysqli_fetch_array($this->data);
// 			if(isset($this->data[id]))
// 				$this->id = $this->data[id];
// 			else
// 				throw new Exception("Query failed: no `id` returned!");
// 		}else
// 			throw new Exception("Query failed: no such row!");
// 	}
	
// }

class Action extends ORM{
	public function __construct(){
		parent::__construct('action');
	}
}

class Course extends ORM{
	public function __construct(){
		parent::__construct('courses');
	}
}

class Department extends ORM{
	public function __construct(){
		parent::__construct('departments');
	}
}

class Model extends ORM{
	public function __construct(){
		parent::__construct('model');
	}
}

class Session extends ORM{
	public function __construct(){
		parent::__construct('sessions');
	}
}

class Year extends ORM{
	public function __construct(){
		parent::__construct('year');
	}
}

class Tags extends ORM{
	public function __construct(){
		parent::__construct('tags');
	}
}

class TagAction extends ORM{
	public function __construct(){
		parent::__construct('tagaction');
	}
}

?>