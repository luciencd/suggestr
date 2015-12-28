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
        //ENSURE THAT THESE ARE THE PROPER TYPE LABELS.
        if($type == "0"){//Yes
            array_push($this->yes,$course_id);
        }else if($type == "1"){//Taken
            array_push($this->taken,$course_id);
        }else if($type == "2"){//No
            array_push($this->no,$course_id);  
        }
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
        $result = $query->select('*',true,'','',false);///Need to return ordered by session_id

        //In order, add each student to the list, adding each course that they took, no, or yes.
        //foreach($result as $action){
        while($row = mysqli_fetch_array($result)){
            

            $id = $row['session_id'];
            $course = $row['course_id'];
            $major = $row['major'];
            $year = $row['year'];
            $choice = $row['choice'];
            
            if (isset($this->StudentList[$id])) {

                $CurrentStudent = $this->StudentList[$id];
                $CurrentStudent->addCourse($course,$choice);

            }else{

                $CurrentStudent = new Student($id);
                $CurrentStudent->setMajor($major);
                $CurrentStudent->setYear($year);
                $CurrentStudent->addCourse($course,$choice);

            }
            $this->StudentList[$id] = $CurrentStudent;

        }

    }


    //GET FUNCTIONS.
    
    //Checks whether a student exists or not.
    function studentExists($student_id){
        if(isset($this->StudentList[$student_id])){
            return true;
        }else{
            return false;
        }
    }
    
    //Takes session id of a student, and returns the classes the guy took as an array.
    function getStudent($student_id){
        if(isset($this->StudentList[$student_id])){
            return $this->StudentList[$student_id];
        }else{
            return new Student($student_id);
        }
    }

    //Returns the name of a course from it's id.
    function getClassNameById($course_id){
        $result = new Course();
        $result->findById($course_id);
        return $result->get('name');
        
    }

    //returns the amount of students currently in the database.
    function numStudents(){
        return count($this->StudentList);
    }

    //returns the courses of a given student by their id.
    //returns array.
    function getStudentsTakenCourses($id){
        return $this->getStudent($id)->getTaken();
    }
    /* Gives you the Jaccard index between two arrays, that is 
    The cardinality of the Intersection over the cardinality of the Union
    @params: $s1, $s2, two arrays of classes(course id's)
    @returns: float.
    */
    function jaccardIndex($s1,$s2){
        $Union = array_unique(array_merge($s1, $s2));
        $Intersection = array_intersect($s1,$s2);
        if(Count($s1) == 0 || Count($s2) == 0){
            return 1;
        }else{
            return (Count($Intersection))/(Count($Union));
        }
        
    }

    /* Gives you a list of suggested courses based on a list of courses 
    coming into the function.
    
    @params: $coursesTaken : an array of courseId's

    @returns: $likelyClasses : an associative Array of courses to suggestion score
    map[course] => score.
    */
    function getSuggestedCourses($coursesTaken){
        $start = microtime(true);


        $scores = array();
        foreach($this->StudentList as $otherStudent){

            $otherStudentTaken = $otherStudent->getTaken();
            if((abs(Count($coursesTaken) - Count($otherStudentTaken)) < 6)){
                $score = $this->jaccardIndex($coursesTaken,$otherStudentTaken);
                $scores[$otherStudent->getId()] = array($score,$otherStudentTaken);
            }
            
        }
        arsort($scores);

        $likelyClasses = array();
        foreach($scores as $first => $second){
            $score = $second[0];
            $classes = $second[1];
            //echo $score.'<br>';
            if($score > .2){
                foreach($classes as $class){
                    if(!in_array($class,$coursesTaken)){//If this is a hashtable, don't think this matters
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
        /*array_filter($likelyClasses,function($k, $v){
            return !in_array($v,$coursesTaken);

        },ARRAY_FILTER_USE_BOTH);*/
        /*
        foreach($classes as $class){
            if(in_array($class,$coursesTaken)){
                unset($class);
            }
        }*/
        $end = microtime(true);
        //echo 'Generating course Suggestions took ' . ($end-$start) . ' seconds!<br>';
        
        return $likelyClasses;


    }
    /* Gives you frequency of a particular class. 
    The amount of times students have taken a course.
    @param: $id : int, the session id of a given student
    @return: int, the amount of times a class has been taken.

    Perhaps this needs to be cached in some way.
    */
    function courseFrequency($course_id){
        //Should I be using ORMs to do this?
        //Replace with query.
        $statement = "SELECT Count FROM courseFrequency WHERE course_id =".$course_id;
        $result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);

        if(mysqli_fetch_array($result)==null){
            return 1;
        }
        return mysqli_fetch_array($result)[0];
    }


    /*
    Get an array of the tags associated with a particular course.
    Draw visual of array here:
    */
    function courseTags($course_id){
        $query = new Query('tagaction');
        $result = $query->select('*',array(array('course_id','=',$course_id)),'','',false);

        $tagResults = array();

        foreach($result as $row){
            if(sizeof($tagResults) >= 5){
                break;
            }
            $tag_id = $row['tag_id'];
            $tag_name = $row['tag_name'];
            //echo "<h4> do it:".$tag_id."</h4>";
            if(!isset($tagResults[$tag_id])){
                $tagResults[$tag_id]['tagName'] = $tag_name;
                $tagResults[$tag_id]['tagId'] = $tag_id;
                $tagResults[$tag_id]['count'] = 1;
            }else{
                $count = $tagResults[$tag_id]['count']+=1;
                
                $tagResults[$tag_id]['tagName'] = $tag_name;
                $tagResults[$tag_id]['tagId'] = $tag_id;
                $tagResults[$tag_id]['count'] = $count;
            }
        }
        
        $query = new Query('Tags');
        $result = $query->select('*',true,'','',false);
        $numTags = mysqli_num_rows($result);

        while(sizeof($tagResults) <= 5){
            $tag_id = new ORM('Tags');
            $random_id = rand(0,$numTags);
            $tag_id->findById($random_id);            
            $tag_name = $tag_id->get('name');

            if(!isset($tagResults[$random_id])){
                $tagResults[$random_id] = array('tagName' => $tag_name,
                                            'tagId' => $random_id,
                                            'count' => 0);

            }
        }

        $tagResultsArray = array();
        //must make this into a list, not an associative array, due to the precondition of mustache.
        //Here is the transformation:
        foreach($tagResults as $key => $value){
            array_push($tagResultsArray,array('tagName' => $value['tagName'],
                                                'tagId' => $value['tagId'],
                                                'count' => $value['count']));

        }
        


        return $tagResultsArray;
    }
}

?>
    
