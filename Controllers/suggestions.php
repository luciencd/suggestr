<?php

class Student {
    public $id = "";
    public $major = "";
    public $year = "";
    public $taken = array();
    public $no = array();
    public $yes = array();
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
        
    }
    function getId(){
        return $this->id;
    }
    function getTaken(){
        return $this->taken;
    }
    
    
    function setMajor($_major){
        $this->major = $_major;
    }
    function setYear($_year){
        $this->year = $_year;
    }

    function getMajor(){
        return $this->major;
    }
    function getYear(){
        return $this->year;
    }

    function addCourse($course_id,$type){

        //echo "type push size before: ",count($this->taken);
        if($type == "0"){
            //$this->yes[$course_id] = true;
            array_push($this->yes,$course_id);
        }else if($type == "2"){
            //$this->no[$course_id] = true;
            array_push($this->no,$course_id);
        }else if($type == "1"){
            //$this->taken[$course_id] = true;
            array_push($this->taken,$course_id);  
        }
        //echo "type push size after: ",count($this->taken);
    }


}

##ADT {id  => Student()}
class Database {
    public $StudentList = array();
    public $ClassList = array();

    function __construct(){
        

        $this->loadAllStudents();
        //$this->loadAllClasses();
    }
    /*
    function loadAllClasses(){
        $query = new Query('courses');
        $queryActions = $query->select('*',true,'ASC');//Need to return ordered by session_id
        
        foreach($queryActions as $action){
            $id = $action->get('id');
            $courseName = $action->get('name');
            $this->ClassList[$id] = $courseName;
        }

    }*/
    ##requires: id is a session id in the database.
    ##returns: 
    function loadAllStudents(){
        $query = new Query('action');
        $queryActions = $query->select('*',true,'ASC');//Need to return ordered by session_id
        

       
        //$newStudent = new Student();
        //In order, add each student to the list, adding each course that they took, no, or yes.
        foreach($queryActions as $action){


            $id = $action->get('session_id');
            $course = $action->get('course_id');
            $major = $action->get('major');
            $year = $action->get('year');
            
            if (isset($this->StudentList[$id])) {
                //echo "set";

                $CurrentStudent = $this->StudentList[$id];


                //echo "<h3>test</h3>";
                $CurrentStudent->addCourse($action->get('course_id'),$action->get('choice'));
            }else{
                
                //echo "not set yet";
                $CurrentStudent = new Student($id);
                $CurrentStudent->setMajor($major);
                $CurrentStudent->setYear($year);
                //echo "<br>".$CurrentStudent->getId()." ".$CurrentStudent->getMajor()." ".$CurrentStudent->getYear();
                $CurrentStudent->addCourse($action->get('course_id'),$action->get('choice'));
                
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
            return array();
        }
    }
    function getClassNameById($id){

        $result = new Course();
        $result->findById($id);
        return $result->get('name');
        
    }
    function numStudents(){
        return count($this->StudentList);
    }

    function getStudentsTakenCourses($id){
        return $this->getStudent($id)->getTaken();
    }
    function jaccardIndex($s1,$s2){
        $Union = array_unique(array_merge($s1, $s2));
        $Intersection = array_intersect($s1,$s2);
        //echo "<br>".Count($Intersection);
        //echo "<br>".Count($Union);

        return (1+Count($Intersection))/(1+Count($Union));
    }

    function getSuggestedCourses($coursesTaken){
        $scores = array();
        foreach($this->StudentList as $otherStudent){
            $otherStudentTaken = $otherStudent->getTaken();
            $score = $this->jaccardIndex($coursesTaken,$otherStudentTaken);
            $scores[$otherStudent->getId()] = array($score,$otherStudentTaken);
            //array_push(array($score,$otherStudentTaken),$scores);
        }
        arsort($scores);

        $likelyClasses = array();
        foreach($scores as $first => $second){
            $score = $second[0];
            $classes = $second[1];
            //echo $score.'<br>';
            if($score > .2 and (abs(Count($classes) - Count($otherStudentTaken)) < 3)){
                foreach($classes as $class){
                    if(!in_array($class,$coursesTaken)){
                        if(isset($likelyClasses[$class])){
                            //Weird function need to analyse this.
                            $likelyClasses[$class] += $score*(1/log($this->courseFrequency($class)+5));//Multiply by classification modifier
                            //The more common a class is, the less it matters.
                        }else{
                            $likelyClasses[$class] = $score*(1/log($this->courseFrequency($class)+5));
                            
                        }
                    }
                }
            }
        }
        arsort($likelyClasses);
        return $likelyClasses;


    }

    function courseFrequency($id){
        //echo $id;
        $statement = "SELECT Count FROM courseFrequency WHERE course_id =".$id;
        $result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);
        //echo "<h4>".$result."</h4>";
        //return $result->get('Count');
        return mysqli_fetch_array($result)[0];
    }
}

?>
    