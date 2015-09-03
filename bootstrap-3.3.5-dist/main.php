

<?php    
    // load up your config file
    //header("Location: main.php");
    //Make a priority queue of most likely classes to take. 
    //When you press yes and took, priority goes to 0.
    //Whem you press no, priority goes to 50%
  
    
    

    require_once("resources/API.php");
    require_once("resources/user.php");

    //$current_user = new user();
    //echo "<h1>".$_COOKIE["user"]."</h1>";
    //echo "<h1>".$_COOKIE["priority"]."</h1>";

    

    
    


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    



    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body role = "document">


    <div class="container theme-showcase" role="main">

      <div class="jumbotron">
        <div class = "suggestr">
          <h1>SUGGESTR</h1>
        </div>
      </div>

      <div class = "container">
        <form class = "description">
          <h2 class = "form-signin-heading">Who are you?</h2>

          <!--<input type="text" id="inputMajor" name = "major" class = "form-control" placeholder = "major(Computer Science)" required autofocus>
          -->
          <?php require_once("resources/DropdownMajor.php"); ?>
          <label for="inputMajor" class="sr-only">Major</label>
          
          
          <!--<input type="text" id="inputYear" name = "year" class="form-control" placeholder = "year(Freshman)" required>-->
          <?php require_once("resources/DropdownYear.php"); ?>
          <label for="inputYear" class="sr-only">Year</label>
          

          <button class="btn btn-lg btn primary btn-block" type="submit">Start Session</button>
        </form>

      </div>

      <div class = "row" style ="text-align:center">
          
      </div>
      <?php
        //echo "<h1> TEST </h1>";

        $current_user = new user($conn,$_COOKIE["user"]);
        //$current_user->print_this();
        //DEBUGGER!!!

        
        //echo "<h1> priority GET:  ".$current_user->getPriority($conn)."</h1>";
        //echo "<h1>id".$current_user->request_course_id($conn)."</h1>";
        //echo "<h1>course".$current_user->request_course_name($conn,$current_user->request_course_id($conn))."</h1>";
        //echo "<h1>DAWW AW".$current_user->request_course_id($conn)."</h1>";
        $current_user->get_array_similar($conn);
        /*
        $selected_courses = $current_user->get_array_similar($conn);
        echo "<ul>";
        while($potential_course = mysqli_fetch_row($selected_courses)){
            echo "<li>".$potential_course[1]." : ".request_course_name($conn,$potential_course[0])."</li>";
        }
        echo "</ul>";*/
      ?>
    </div>

      <footer class = "footer">
        <div class = "container" >

            <p class = "text-muted">Machine Learning driven app for students who don't know what classes to take.
            Developed by Lucien Christie-Dervaux.</p>

        </div>
      </footer>

    <script type = "text/javascript" src = "js/jquery-2.1.4.js"></script>    
    

    <script src="js/bootstrap.min.js"></script>
    <script type = "text/javascript" src = "js/main.js"></script>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    
  </body>
</html>