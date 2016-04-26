<?php
class Blob {
    public $titleArray = array();
    public $array = array();
    public $wordCount = array();
    public $set = array();
    function __construct($arraywords,$arraytitle){
        //Unfortunately, if there is no description, the
        //Title gets a shit ton more weight.
        //Perhaps make it not calculate if no description?

        $this->array = array_diff(explode(" ",$arraywords),array(''," "));
        //Add title, here; kinda hacky.
        
        $this->titleArray = array_diff(explode(" ",$arraytitle),array(''," "));

        if(Count($this->array)>5){//So classes with less than 5 words in desc aren't considered.
            $compiledArray = array_merge($this->array,$this->titleArray);

            foreach($compiledArray as $word){

                //So case does not matter
                $word = strtolower($word);
                

                //So puctuation doesn't matter either.
                $word = str_replace(",","",$word);
                $word = str_replace(".","",$word);
                $word = str_replace(":","",$word);
                $word = str_replace(";","",$word);

                



                //Figure out how to get stemming to work..
                //$word = stemword($word, 'english', 'UTF_8');


                if(isset($this->wordCount[$word])){
                    $this->wordCount[$word] += 1;
                }else{
                    $this->wordCount[$word] = 1;
                    array_push($this->set,$word);
                }

            }
        }
        
    }

    function getArray(){
        return $this->array;
    }
    function getSet(){
        return $this->set;
    }

    function size(){
        return Count($this->array);
    }
    function getCountWord($word){
        if(isset($this->wordCount[$word])){
            return $this->wordCount[$word];
        }else{
            return 0;
        }
    }

}
class CourseObject {
    public $id = "";
    public $name = "";
    public $number = "";
    public $description = "";
    public $department_id = "";
    public $blob;
    function __construct($id_,$name_,$number_,$description_,$department_id_){
        $this->id = $id_;
        $this->name = $name_;
        $this->number = $number_;
        $this->description = $description_;
        $this->department_id = $department_id_;
        $this->setBlob($this->description,$this->name);
    }
    function setBlob($array,$name){
        $this->blob = new Blob($array,$name);
    }
    function getBlob(){
        return $this->blob;
    }

    function controllerArray(){
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'department_id' => $this->department_id,
            'number' => $this->number
            );
    }

}


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
    function getAdded(){
        return $this->yes;
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
    public $courseList = array();
    public $RatingsList = array();
    public $MajorList = array();
    public $RelationsList = array(array());

    public $containingCache = array();
    public $idfCache = array();
    public $blobList = array();
    function __construct(){
        
        
        //Make it so that it keeps track of last import id, and only imports stuff after those id's.
        //

    }
    function load(){
        //$StudentList = array();
        $this->loadAllClasses();
        $this->loadAllStudents();
        $this->loadAllMajors();
        $this->loadAllRelations();
        $this->loadAllRatings();
        //echo "loaded";
    }

    function testConnection($table){
       
        $query = new Query($table);
        $queryActions = $query->select('*',false,'','',false);
        
        
        if($queryActions == false){
            return false;
        }else{
            return true;
        }
    }
    function loadAllClasses(){
        if(!$this->testConnection('courses')){
            return;
        }

        $query = new Query('courses');
        $queryActions = $query->select('*',true,'','',false);//Need to return ordered by session_id
        
        foreach($queryActions as $course){
            $id = $course['id'];
            $department_id = $course['department_id'];
            $courseName = $course['name'];
            $courseNumber = $course['number'];
            $courseDescription = $course['description'];


            //$this->courseList[$id] = array('name' => $courseName,'number'=> $courseNumber,'description' => $courseDescription,'department_id'=>$department_id);
            $courseObj = new CourseObject($id,$courseName,$courseNumber,$courseDescription,$department_id);
            //$courseObj->setBlob($course['description']);
            $this->courseList[$id] = $courseObj;
            
            //$arrayList = $newCourse->controllerArray();
            //echo $arrayList['name'];
        }

    }
    ##requires: id is a session id in the database.
    ##returns: 

    function loadAllStudents(){
        if(!$this->testConnection('action')){
            return;
        }
        $studentList = array();
        //Need to reset array first, so we don't duplicate data entry.

        $query = new Query('action');
        $result = $query->select('*',true,'','',false);///Need to return ordered by session_id

        //In order, add each student to the list, adding each course that they took, no, or yes.
        //foreach($result as $action){
        while($row = mysqli_fetch_array($result)){
            

            $id = $row['session_id'];
            $course = $row['course_id'];
            $choice = $row['choice'];
            
            if (isset($this->StudentList[$id])) {

                $CurrentStudent = $this->StudentList[$id];
                $CurrentStudent->addCourse($course,$choice);

            }else{
                $CurrentStudent = new Student($id);
                $CurrentStudent->addCourse($course,$choice);
            }
            $this->StudentList[$id] = $CurrentStudent;
        }

        $query = new Query('sessions');
        $result = $query->select('*',true,'','',false);
        while($row = mysqli_fetch_array($result)){
            //echo $id;
            $id = $row['id'];
            $major = $row['major_id'];
            if($major == 0){
                $major = 1;
            }
            $year = $row['year_id'];
            $student = $this->getStudent($id);
            $student->setMajor($major);
            $student->setYear($year);
            $this->StudentList[$id] = $student;
        }

    }
    //Preprocesses all the ratings from the slideractions Table.
    //Grabs all the data from the 
    function loadAllRatings(){
        $RatingsList = array();
        //Need to reset as would add same entries multiple times potentially.
        if(!$this->testConnection('slideraction')){
            return;
        }

        $query = new Query('slideraction');
        $result = $query->select('*','','','',false);

        
        foreach($result as $row){
            //echo $row;
            /*
            Probably not best practice here. fix it eventually.
            */
            $source_major = $this->getStudent($_COOKIE['sessionId'])->getMajor();
            $target_major = $this->getStudent($row['session_id'])->getMajor();
            //if($target_major == "" or $source_major == ""){
            //    continue;
            //}
            //if($target_major > 132 and $target_major <178 and $source_major > 132 and $source_major <178){
            //$target_major = $this->getClassMajorById($row['course_id']);

            //echo "(".$source_major." ".$target_major. "),";
            // arts taking calc 1
            // ['arts'][.1,[.2,.2,.2,.2],4]['Mathematics'][]

            //When it comes to preferences and stuff like that, we want a weighting that accuratly represents
            //similar majors as perhaps 60-40% of the rating.
            if($row['slider_type'] == "preference"){
                if(isset($this->RatingsList[$row['course_id']][$row['slider_id']][$target_major])){
                
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = $this->majorSimilarity($source_major,$target_major);
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][1] += $row['vote'];
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][2] += 1;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][3] = $source_major;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][4] = $row['session_id'];
                    
                    
                }else{
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = $this->majorSimilarity($source_major,$target_major);
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][1] = $row['vote'];
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][2] = 1;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][3] = $source_major;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][4] = $row['session_id'];
                }   
                if(isset($this->RatingsList[$row['course_id']][$row['slider_type']][$target_major])){
                    $this->RatingsList[$row['course_id']][$row['slider_type']][$target_major] += 1;
                }else{
                    $this->RatingsList[$row['course_id']][$row['slider_type']][$target_major] = 1;
                }
            //When it comes to individual majors' requirements, it should be 90% the major.
            }else if($row['slider_type'] == "advised"){
                if(isset($this->RatingsList[$row['course_id']][$row['slider_id']][$target_major])){
                    
                    //$this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = $this->majorSimilarity($source_major,$target_major);
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][1] += $row['vote'];
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][2] += 1;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][3] = $source_major;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][4] = $row['session_id'];
                    
                }else{
                    //Basically if we have an "advised" slider, then the weight for the same major's
                    //Information is .95 
                    //and the different majors are fractions of a percent, but when the .95 doesn't exist, they
                    //resume proportional allocation.
                    //$this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = .1122;
                    if($source_major == $target_major){
                        $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = .95;
                    }else{
                        $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][0] = .001+.005*$this->majorSimilarity($source_major,$target_major);
                    }
                    
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][1] = $row['vote'];
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][2] = 1;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][3] = $source_major;
                    $this->RatingsList[$row['course_id']][$row['slider_id']][$target_major][4] = $row['session_id'];
                    
                }
                if(isset($this->RatingsList[$row['course_id']][$row['slider_type']][$target_major])){
                    $this->RatingsList[$row['course_id']][$row['slider_type']][$target_major] += 1;
                    //echo 'adding binary rate: '.$row['slider_type']."<br>";
                }else{
                    $this->RatingsList[$row['course_id']][$row['slider_type']][$target_major] = 1;
                }
            }
        }
        
        foreach($this->RatingsList as $course => &$key){
            
            //Get the total amount of ratins for this particular course
            foreach($key as $k => &$target){
                //echo $k."<br>";

                if($k == 1 || $k == 2 || $k == 3){
                    
                    //If $k, the slider id, is a preference.
                    $weightedVoteSum = 0.0;
                    $weightSum = 0.0;
                    foreach($target as $major => &$values){
                        //$values contains the major similarity, (coefficient)
                        //The amount of votes cast by these students in this major
                        //The total sum of those votes. 
                        // 1 + 1 + 1  =  $values[1] = $numVotes = 3
                        // 0 + 1 + .5 =  $values[2] = $sumVotes = 1.5 
                        //
                        // average rating for this major should be $avgVotes = 3/1.5 = 0.5
                        // the weight of this rating will be $values[4] = $weight = .40
                        $weight = $values[0];
                        $sumVotes = $values[1];
                        $numVotes = $values[2];
                        


                        $weightedVoteSum += $weight*($sumVotes/$numVotes);
                        $weightSum += $weight;
                    }

                   
                    //must normalize the result = .4*(.5) + .2*(0.0)
                    // = (.4(.5)+.2(0))/.6 = .33
                    // End rating .33 / 1 , 33%

                    //Obviously if there is no weightSum
                    if($weightSum == 0){
                        $target[0] = 0;
                    }else{
                        $target[0] = $weightedVoteSum/$weightSum;
                    }
                    //echo $target[0];

                    

                }else if($k == 4 || $k == 5 || $k == 6){
                    //If $k, the slider id, is an advisory.
                    //$totalCourseTypeRatings = $key['advisory'];
                    $weightedVoteSum = 0.0;
                    $weightSum = 0.0;
                    $totalVotes = 0;
                    foreach($target as $major => &$values){

                        $totalCourseTypeRatings = $key['advised'][$major];
                        //echo $this->getClassNameById($course)." ".$course."Major(".$major.") -> totalcount(".$totalCourseTypeRatings.')';
                        //$values contains the major similarity, (coefficient)
                        //The amount of votes cast by these students in this major
                        //The total sum of those votes. 
                        // 1 + 1 + 1  =  $values[1] = $numVotes = 3
                        // 1 + 1 + 1 =  $values[2] = $sumVotes = 3 
                        // 3 + 1 + 1  = $key['advisory']['major'] = 5
                        // 3/5 = $actualVote
                        // average rating for this major should be $avgVotes = 3/1.5 = 0.5
                        // the weight of this rating will be $values[4] = $weight = .40
                        $weight = $values[0];
                        $sumVotes = $values[1];
                        $numVotes = $values[2];
                        //$actualVote = $sumVotes/$totalCourseTypeRatings;
                        //echo 'advised: '.$k.' '.$actualVote.'<br>';
                        $totalVotes +=$sumVotes;

                        //$weightedVoteSum += $weight*($actualVote);// .44(3/5)
                        $weightedVoteSum += $weight*($sumVotes/$totalCourseTypeRatings);
                        $weightSum += $weight;// .44
                    }

                    if($weightSum == 0){
                        $target[0] = 0;
                    }else{
                        $target[0] = $weightedVoteSum;///$totalVotes;//$weightedVoteSum/$weightSum;
                    }
                    //echo $target[0].'<br>';
                    //$target[0] = 1;
                }
            }
        }
        /*
                $avg = 0.0;
                $sum = 0.0;
                foreach($a as $m => &$b){
                    if($k >3 and $k <7){
                        $votes = $b[2];

                        //maybe can do this outside of here?
                        $sum = 0;
                        
                        foreach($key as $s => $slider){
                            //echo "SLIDER: ".$slider;
                            if($s >3 and $s <7){
                                if(isset($slider[$m][2])){
                                    $sum += $slider[$m][2];
                                    //echo "1 ";
                                }else{
                                    //echo "0 ";
                                }
                            }
                            

                        }
                        //echo " -> ";
                        foreach($key as $s => $slider){
                            //echo "SLIDER: ".$slider;
                            if($s >3 and $s <7){
                                if(isset($slider[$m][2])){
                                    //echo $slider[$m][2]/$sum." ";
                                }
                            }
                            

                        }
                        //echo "<br>";
                       
                        $b[4] = $votes/$sum;
                        //echo "AA k ".$k." b[2] ".$b[2]. " b[4] ".$b[4]." votes: ".$votes. "/".$sum."<br>";
                    }
                    



                    
                    //meant to give you the average rating from a particular 
                    if(isset($b[4])){
                        $avg += $b[4];
                        $sum += $b[0];
                        //echo "normalized: (".$b[4].")";
                    }else{
                        $avg += $b[0]*($b[1]/$b[2]);//
                        $sum += $b[0];
                    }
                    
                    //echo $course."::".$this->getClassNameById($course)."(".$b[3]." -> ".$m." weight:".$b[0]." slider:".$b[1]/$b[2]."{".$b[1].",".$b[2]."})";
                }*/
                //$avg = $avg*$
                //echo "(".$major." -> ".$sum." ".$avg.")";
                //echo "FINAL: sum:".$sum.",avg:".$avg.",slider: ".$avg/$sum."<br>"
    }

    function loadAllMajors(){
        if(!$this->testConnection('departments')){
            return;
        }
        $query = new Query('departments');
        $result = $query->select('*','','','',false);
        foreach($result as $row){
            $id = $row['id'];
            $name = $row['name'];
            $amount = $row['amount'];
            $this->MajorList[$id] = array('id'=>$id,'name'=>$name,'amount'=>$amount);
        }
    }

    function loadAllRelations(){
        if(!$this->testConnection('majorrelations')){
            return;
        }
        $query = new Query('majorrelations');
        $result = $query->select('*','','','',false);
        foreach($result as $row){
            //echo '<br> score:'.$row['score'];
            $this->RelationsList[$row['source_id']][$row['target_id']] = $row['score'];
            //echo ' relations:'.$this->RelationsList[$row['source_id']][$row['target_id']];
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

    function getClassMajorById($course_id){
        if(isset($this->courseList[$course_id]->controllerArray()['department_id'])){
            return $this->courseList[$course_id]->controllerArray()['department_id'];
        }else{
            return -1;
        }

    }
    //Returns the name of a course from it's id.
    function getClassNameById($course_id){
        if(isset($this->courseList[$course_id]->controllerArray()['name'])){
            return $this->courseList[$course_id]->controllerArray()['name'];
        }else{
            return "not a class";
        }
    }

    //Returns array of object that you want will all features.
    //you need in outputting it on web app.

    function getReturnArray($id,$type){
        //Wrapper class for features in class.
        $returnArray = array();
        if($type == "course"){
            $returnArray = $this->courseList[$id]->controllerArray();
        }else if($type == "student"){
            //$returnArray = $this->studentList[$id]->controllerArray();
        }

        /*if(!isset($returnArray)){
            throw new Exception("type does not exist error");
        }*/
        return $returnArray;
    }

    //Returns number of credits in a course.
    function courseCredits($course_id){
        //Will edit later.
        return 4;
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


    //Given that you are of major $m1, what is the percentage of classes of major $m2 that you take at rpi.
    function majorSimilarity($m1,$m2){
        //Obviously, if the majors are the same it should return 1.
        /*if($m1 == $m2){
            return 1;
        }*/



        //Essentially 0 to 1... out of the classes a major takes, what percentage
        //does another major's classes make a part of percentage-wise.

        //Remember that if Arts majors never choose a class, they will never change the rating
        //and it will stick to those who actually are in that class.
        if(isset($this->RelationsList[$m1][$m2])){
            $number = $this->RelationsList[$m1][$m2];///(0.00001+1-$this->RelationsList[$m1][$m1]);
            //echo "<br><h4>num :";
            //echo $number." )</h4>";
            if($number == 0){
                return .01;
            }
            return $number;
            
            

        }else{
            return .0001;//if the thing never got set in the database.
        }
    }

    /* Gives you a list of suggested courses based on a list of courses 
    coming into the function.
    
    @params: $coursesTaken : an array of courseId's

    @returns: $likelyClasses : an associative Array of courses to suggestion score
    map[course] => score.
    */
    function getSuggestedCourses($student, $coursesTaken){
        $start = microtime(true);

        //echo "<h3>SUGGESTED COURSES!!</h3>";
        $scores = array();
        foreach($this->StudentList as $otherStudent){

            $otherStudentClasses = $otherStudent->getTaken();
            array_merge($otherStudentClasses,$otherStudent->getAdded());

            //if((abs(Count($coursesTaken) - Count($otherStudentTaken)) < 6)){
            $score = $this->jaccardIndex($coursesTaken,$otherStudentClasses);
            $scores[$otherStudent->getId()] = array($otherStudent,$score,$otherStudentClasses);
                //echo " (".$otherStudent->getId().",".$score.") ";
            //}
            
        }

        //arsort($scores);

        $likelyClasses = array();
        $MAX_CLASSES = 40;
        $source_major = $student->getMajor();
        foreach($scores as $first => $second){
            $otherStudent = $second[0];
            $score = $second[1];
            $classes = $second[2];
            $otherStudent_major = $otherStudent->getMajor();

            //echo $otherStudent_major.'<br>';
            //echo $score.'<br>';
            $sum = 0;
            if($score >= 0 /*and $otherStudent_major !=0*/){
                foreach($classes as $class){
                    if(!in_array($class,$coursesTaken)){//If this is a hashtable, don't think this matters
                        $target_major = $this->getClassMajorById($class);
                        //echo $score;
                        if(isset($likelyClasses[$class])){
                            //Weird function need to analyse this.

                            $likelyClasses[$class][1] += $score;
                            $likelyClasses[$class][2] += $this->majorSimilarity($source_major,$target_major);
                            $likelyClasses[$class][3] += $this->majorSimilarity($source_major,$otherStudent_major);//How to shrink amount of queries.
                            $likelyClasses[$class][0] += 1.0;
                            $sum++;
                            //$likelyClasses[$class] += $score*(1/log($this->courseFrequency($class)+5));//Multiply by classification modifier
                            //The more common a class is, the less it matters.
                        }else{
                            $newArray = array();

                            $newArray[1] = $score;
                            $newArray[2] = $this->majorSimilarity($source_major,$target_major);
                            $newArray[3] = $this->majorSimilarity($source_major,$otherStudent_major);//How to shrink amount of queries.
                            $newArray[0] = 1.0;
                            $likelyClasses[$class] = $newArray;
                            $sum++;
                            //$likelyClasses[$class] = $score*(1/log($this->courseFrequency($class)+5));
                            
                        }
                    }
                }
            }
        }
        $likelyClasses_weighted = array();

        foreach($likelyClasses as $id => $data){
            //echo "(".$this->getClassNameById($id)."[".$data[0].",".$data[1]/$data[0].",".$data[2]/$data[0].",".$data[3]/$data[0]."])";

            //Balancing out the huge courses with the small ones.
            //$likelyClasses_weighted[$id] = $data[1] * (1/log($data[0]+1,2));
            $likelyClasses_weighted[$id] = $data[0];

            //if($data[0] > 1)
            $net = array();

            //BASIC NEURAL NET HERE.
            $net[0] = 1;
            $net[1] = 1;
            $net[2] = 1;
            $net[3] = 1;


            if(count($coursesTaken) < 4){
                $net[0] = 5;
                $net[1] =.01;
                $net[2] = 20;
                $net[3] = 20;
                
                
            }else if(count($coursesTaken) >=4 and count($coursesTaken)<=8){
                $net[0] = 1;
                $net[1] = 15;
                $net[2] = 2;
                $net[3] = 10;
            }
            //echo ($data[0])." ";
            $likelyClasses_weighted[$id] = (($net[1]*$data[1]+$net[2]*$data[2]+$net[3]*$data[3]+$net[0]*$data[0])/($net[1]+$net[2]+$net[3]+$net[0]))/(1+log($data[0],10));
            //echo $likelyClasses_weighted[$id]."<br>";
        }

        arsort($likelyClasses_weighted);

        $end = microtime(true);
        //echo '<br><br><br><br><br><br><br><br><br><h3>END :'.$end-$start.'</h3>';
        return $likelyClasses_weighted;


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
        //Doesn't actually end up adding time.
        
        
        $statement = "SELECT Count FROM courseFrequency WHERE course_id =".$course_id;
        $result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);

        if(mysqli_fetch_array($result)==null){
            return 1;
        }
        return mysqli_fetch_array($result)[0];
    }

    function cmp2($a, $b)
        {
            if ($a['count'] == $b['count']) {
                return 0;
            }
            return ($a['count']< $b['count']) ? 1 : -1;
        }
    /*
    Get an array of the tags associated with a particular course.
    Draw visual of array here:
    */
    function courseTags($course_id){
        
        /*find a way to limit total amount of outgoing tags or some other mechanic.*/
        if(!$this->testConnection('tags')){
            return;
        }

        $query = new Query('tags');
        $result = $query->select('*',true,'','',false);

        $tagResults = array();

        foreach($result as $row){

            $tag_id = $row['id'];
            $tag_name = $row['name'];

            if(!isset($tagResults[$tag_id])){
                $tagResults[$tag_id]['tagName'] = $tag_name;
                $tagResults[$tag_id]['tagId'] = $tag_id;
                $tagResults[$tag_id]['count'] = $this->courseTagFrequency($course_id,$tag_id);
            }
        }
        usort($tagResults,array($this,'cmp2'));



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

    function courseTagFrequency($course_id, $tag_id){
        if(!$this->testConnection('tagaction')){
            return;
        }
        
        $query = new Query('tagaction');
        $result = $query->select('*',array(array('course_id','=',$course_id),array('tag_id','=',$tag_id)),'','',false);


        return mysqli_num_rows($result);
    }

    function ratingPercentage($course_id, $slider_id){

        //Preprocessing the array takes half the time.
        if(isset($this->RatingsList[$course_id][$slider_id][0])){
            
            return $this->RatingsList[$course_id][$slider_id][0];
        }else{
            return 0.0;
        }
    }

    //returns an array of true false ratings.
    //Returns a single array, which contains whether the plurality of the voters
    //thought a given class was free, option, or required.
    function requirement($course_id){
        $outputRating = array();
        $query = new Query('sliders');
        $result = $query->select('*',true,'','',false);

        $ratingResults = array();

        foreach($result as $row){
            $slider_id = $row['id'];
            $slider_name = $row['name'];
            $slider_type = $row['type'];
            //echo "<h1> - ";
            if(!isset($ratingResults[$slider_id]) and ($slider_type == "advised")){
                $ratingResults[$slider_id]['slider_name'] = $slider_name;
                $ratingResults[$slider_id]['slider_id'] = $slider_id;  
                //if at 0.5 because of default reasons, then this will mean everything is option.
                $ratingResults[$slider_id]['count'] = $this->ratingPercentage($course_id,$slider_id);
                $ratingResults[$slider_id]['slider_type'] = $slider_type;
                //echo " ".$slider_id."->".$ratingResults[$slider_id]['count'];
            }
            //echo "<br>";
        }

        $ratingResultsArray = array();
        $maxPercent = -1;
        foreach($ratingResults as $key => $value){
            if($value['count'] >= $maxPercent){
                //echo "\n".$key." ".$value['count'];
                $maxPercent = $value['count'];
                $ratingResult = array('percentage' => $value['count'],
                                                'slider_id' => $value['slider_id'],
                                                'slider_name' => $value['slider_name'],
                                                'slider_type' => $value['slider_type']);
            }
        }
    
        ##get plurality of vote towards Free, option or required.
        return $ratingResult;
    }
    
    //Returns an array of the 3 bar ratings.

    function rating($course_id){

        $outputRating = array();
        $query = new Query('sliders');
        $result = $query->select('*',true,'','',false);

        $ratingResults = array();

        foreach($result as $row){
            $slider_id = $row['id'];
            $slider_name = $row['name'];
            $slider_type = $row['type'];
            
            if(!isset($ratingResults[$slider_id]) and ($slider_type == "preference")){
                $ratingResults[$slider_id]['slider_name'] = $slider_name;
                $ratingResults[$slider_id]['slider_id'] = $slider_id;
                $ratingResults[$slider_id]['count'] = $this->ratingPercentage($course_id,$slider_id);
                $ratingResults[$slider_id]['slider_type'] = $slider_type;
            }
        }

        $ratingResultsArray = array();
        foreach($ratingResults as $key => $value){
            array_push($ratingResultsArray,array('percentage' => 100*$value['count'],
                                                'slider_id' => $value['slider_id'],
                                                'slider_name' => $value['slider_name'],
                                                'slider_type' => $value['slider_type']));

        }
        return $ratingResultsArray;
    }

    //Takes in an associative array of courses
    //$coursesSemester : an array of course_id's
    //takes the average of the 
    //Returns : a rating between 0 and 1 in floating point rep.
    //always divides by 5,
    //May be interesting to compare difficulty to previous semesters and adjust based on whether you can take it...
    function semesterDifficulty($coursesSemester){
        //return Count($coursesSemester)/10.0;
        if(Count($coursesSemester) == 0){
            return 0.01;
        }
        //This system is currently not the best. must improve it somewhat.
        $sumDifficulty = 0.0;
        $sumCredits = 0.0;
        foreach($coursesSemester as $course_id){
            //0 because its easiness slider.
            //because 1 is easiest, and 0 hardest, we must flip this number by substracting by one.
            //Then, we must add .1 to all classes.
            $sumDifficulty += .1+(1-$this->ratingPercentage($course_id,1));//each class should have a base difficulty of 10%
            $sumCredits += $this->courseCredits($course_id);
        }

        $sumDifficulty /= 4;// max credits is 21. Need to adjust for classes with more/less credits.
        //$sumDifficulty += $sumCredits;
        if($sumDifficulty >= 1){
            return .99;
        }else if($sumDifficulty <= 0){
            return .01;
        }
        return $sumDifficulty;
    }


    //Given a list of base courses courses we didn't know about, and courses that we predicted,
    //And gives a rating on how the courses suggested contain coursesExpected.
    //Returns a float number
    //
    function fitness($coursesExpected, $coursesSuggested){
        
        $totalFitness = 0.0;

        //adds 1/the rank of the course in the list.
        foreach($coursesExpected as $course){
            $totalFitness += 1/(array_search($course,$coursesSuggested));
        }

        return $totalFitness;
    }



    function updateMajorRelations(){
        //echo "<h4>updating</h4>";
        
        /*
        //This generates the initial similarity graph between all departments.
        $query = new Query('departments');
        $result = $query->select('*',true,'','',false);

        $result2 = $query->select('*',true,'','',false);

        foreach($result as $source){
            foreach($result2 as $target){
                $searchArray = array(array('source_id',$source['id']),array('target_id',$target['id']));

                $action = new MajorRelations();

                
                //$action->set('id', $source['id']);
                $action->set('source_id', $source['id']);
                $action->set('target_id', $target['id']);
                
                
                $action->save();
            }
        }*/
        
        //set major relations table back to 0.
        $query = new Query('majorrelations');
        $query->update(array(array('count',0)),array(array('id','!=',0)));
        $count = 0;
        $studentsExamined = 0;
        //echo "fuck";
        foreach($this->StudentList as $student){
            $source_id = $student->getMajor();

            
            $classes = $student->getTaken();
            $studentsExamined +=1;
            
            foreach($classes as $course){


                $result = new Course();
                $result->findById($course);// 35555 in courses has dept id 134 for exmple
                $target_id = $result->get('department_id');
                
                //echo "source:".$source_id." ".$target_id;
                //figure out better filtering mechanism, like try catch block.

                if(is_numeric($target_id) && is_numeric($source_id) && $target_id>64 && $target_id<129 &&$source_id>64 && $source_id<129){

                    $MajorRelation = new MajorRelations();
                    $MajorRelation->findByXs(array(array('source_id',$source_id),array('target_id',$target_id)));
                    
                    $amount = $MajorRelation->get('count');
                    $MajorRelation->set('count',$amount+1);
                    $MajorRelation->save();
                    $count+=1;


                }
                        
                //$query->update(array(array('count',$count)),array(array('source_id','=',$source_id),array('target_id','=',$target_id)));
                       
                
            }

        }

        // now get similarity ratings. simple. If 1/15 classes people take in cs is economics,
        // the rating is 1/15.
        //echo "start";

        $statement =  "UPDATE ";
        $statement .= "MajorRelations m1 ";
        $statement .= "INNER JOIN ";
        $statement .= "(";
        $statement .= "SELECT id,source_id,target_id,count,SUM(count) t_sum ";
        $statement .= "FROM MajorRelations ";
        $statement .= "group by source_id ";
        $statement .= ") m2 on m1.source_id = m2.source_id ";
        $statement .= "SET m1.score = m1.count/m2.t_sum";
        //echo $statement;
        $result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);

        return array($statement,$count,$studentsExamined);

    }

    function tf($word, $blob){
        return $blob->getCountWord($word)/$blob->size();
    }
    function n_containing($word, $courseList){
        if(isset($this->containingCache[$word])){
            return $this->containingCache[$word];
        }else{
            $count = 0;
            foreach($courseList as $course){
                $count += $course->getBlob()->getCountWord($word);
            }
            $this->containingCache[$word] = $count;
            return $count;
        }
        
    }
    function idf($word,$courseList){
        if(isset($this->idfCache[$word])){
            return $this->idfCache[$word];
        }else{
            $total = log(Count($courseList) / (1 + $this->n_containing($word, $courseList)));
            $this->idfCache[$word] = $total;
            return $total;
        }
    }
    function tfidf($word, $blob, $courseList){
        return $this->tf($word, $blob) * $this->idf($word, $courseList);
    }

    function cmpScores($a, $b)
    {
        if ($a[1] == $b[1]) {
            return 0;
        }
        return ($a[1]< $b[1]) ? 1 : -1;
    }

    function arraySimilarity($array1,$array2){
        $total = 0;
        foreach($array1 as $word => $score){
            if(isset($array2[$word])) {
                $total += $array2[$word]*$score;
            }
        }
        return $total;
    }

    function similarCourses($course_id,$numSuggestions){
        $course_obj = $this->courseList[$course_id];
        $blob = $course_obj->getBlob();
        $blobSet = $blob->getSet();

        $output = '';

        $Scores = array();
        $Scores2 = array();
        foreach($blobSet as $word){
            $output .= 'word: '.$word.' '.$this->tfidf($word,$blob,$this->courseList);///." ".$this->tfidf($word,$blob,$this->courseList)."<br>";
            
            $s = $this->tfidf($word,$blob,$this->courseList);
            array_push($Scores,array($word,$s));
            $Scores2[$word] = $s;

        }
        //Can run score against onself! works.
        $finalScore = $this->arraySimilarity($Scores2,$Scores2);
        

        /*
        Now do yourself versus all other classes, ranking top 10 and removing duplicates
        */
        $mostSimilarClasses = array();
        foreach($this->courseList as $id => $course){
            $course_obj = $this->courseList[$id];
            $blob = $course_obj->getBlob();
            $blobSet = $blob->getSet();
            $ScoresOther = array();
            foreach($blobSet as $word){
                $output .= 'word: '.$word.' '.$this->tfidf($word,$blob,$this->courseList);///." ".$this->tfidf($word,$blob,$this->courseList)."<br>";
                
                $s = $this->tfidf($word,$blob,$this->courseList);
                $ScoresOther[$word] = $s;

            }
            $finalScore = $this->arraySimilarity($Scores2,$ScoresOther);

            if($course_obj->id != $course_id and $course_obj->description != ""){
                //echo "(".$course_obj->description.")";
                $mostSimilarClasses[$course_obj->id] = $finalScore;//array($course_obj,$finalScore);
            }
            
            //echo $course_obj->name." ".$finalScore;
        }
        $mostSimilarClasses[$course_id] = 1000.0;
        
        //echo "Total Score: ".$this->arraySimilarity($Scores2,$Scores2);
        //usort($tagResults,array($this,'cmp2'));
        usort($Scores,array($this,'cmpScores'));
        arsort($mostSimilarClasses);
        //usort($mostSimilarClasses,array($this,'cmpScores'));
        $arrOut = array();
        $i = 0;
        foreach($mostSimilarClasses as $key => $value){
            if($i >= min(Count($mostSimilarClasses),$numSuggestions)){
                break;
            }else{
                $arrOut[$key] = $value;
            }
            $i+=1;
        }
        
        //$mostSimilarClasses = array_slice($mostSimilarClasses,0,$num);
        
        return array($finalScore,$arrOut);

    }
}
?>