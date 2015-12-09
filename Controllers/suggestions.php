<?php

class Student {
    public $id = "";
    public $major = "";
    public $year = "";
    public $taken;
    public $no;
    public $yes;
    function __construct($id_){
        $this->id = $id_;
        ##Rep invariant:
        ## 
        ##Abstraction Function:
        ## unique id of given model in mysql
        ## session_id can be shared among many Student objects as it is same student.
        ## major = student's major
        ## year = whether a student is a freshman, sophomore, junior or senior
        ## taken = [] chronological list of courses this student has taken
        ## no = [] list of courses student does not want to take next semester
        ## yes = [] list of courses student would like to take next semester.
        $this->taken = array();
        $this->no = array();
        $this->yes = array();
    }
    function getId(){
        return $this->id;
    }
    function getTaken(){
        return $this->taken;
    }
    

    function addCourse($course_id,$type){
        if($type == 0){
            array_push($this->yes,$course_id);
        }else if($type == 1){
            array_push($this->no,$course_id);
        }else if($type == 2){
            array_push($this->taken,$course_id);
        }
    }

}

##ADT {id  => Student()}
class Database {
    public $StudentList = array();

    function __construct(){
        

        $this->loadAllStudents();
    }

    ##requires: id is a session id in the database.
    ##returns: 
    function loadAllStudents(){
        $query = new Query('action');
        $queryActions = $query->select('*',true,'ASC');//Need to return ordered by session_id
        

        echo "size Query:",count($queryActions);
        //$newStudent = new Student();
        //In order, add each student to the list, adding each course that they took, no, or yes.
        foreach($queryActions as $action){


            $id = $action->get('session_id');
            $course = $action->get('course_id');
            //echo "<br>".$id." ".$course;
            if (isset($this->studentList[$id])) {
                //echo "set";

                $CurrentStudent = $this->StudentList[$id];
                $CurrentStudent->addCourse($action->get('course_id'),$action->get('type'));
            }else{
                
                //echo "not set yet";
                $CurrentStudent = new Student($id);
                $CurrentStudent->addCourse($action->get('course_id'),$action->get('type'));
                
                //echo $this->StudentList[$id]->getId();
            }
            $this->StudentList[$id] = $CurrentStudent;
            

            //Now that we know $CurrentStudent is the Student we are working with, 
            //we need to modify the object.

            
            
            
            //array_push($ListActions, $newStudent);
        }
    }
    //Takes session id of a student, and returns the classes the guy took.
    function studentExists($id){
        if(isset($this->StudentList[$id])){
            return "Yes";
        }else{
            return "No";
        }
    }
    function getStudent($id){
        if(isset($this->StudentList[$id])){
            return $this->StudentList[$id];
        }else{
            return "no";
        }
    }
    function numStudents(){
        return count($this->StudentList);
    }

    function getStudentsTakenCourses($id){
        return $this->getStudent($id)->getTaken();
    }
    //function jaccardIndex(s1,s2){
        //return float(len(list(set(s1) & set(s2))))/float(len(list(set(s1) | set(s2))))
    //}
}

?>
    