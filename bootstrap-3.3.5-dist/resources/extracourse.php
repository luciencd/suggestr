<div class= "col-md-3">
  <div class = "course"><?php $course = request_course($conn);echo $course[1]; ?></div>
    <div class = "buttons">
      
      <form class = "yes" action="resources/yes.php" method="post">
        
        <input type="hidden" id="inputId" name = "id" class="form-control" value =<?php echo $id; ?>>
        <input type="hidden" id="classId" name = "class_id" class="form-control" value ="<?=$course[0]?>"/>
        <button  class = "btn btn-lg btn-success" type = "submit">Yes</button>
      </form>
      <form class = "took" action="resources/took.php" method="post">
        <input type="hidden" id="inputId" name = "id" class="form-control" value =<?php echo $id; ?>>
        <input type="hidden" id="classId" name = "class_id" class="form-control" value ="<?=$course[0]?>"/>
        <button class = "btn btn-lg btn-primary" type = "submit">Took</button>
      </form>
      <form class = "no" action="resources/no.php" method="post">
        <input type="hidden" id="inputId" name = "id" class="form-control" value =<?php echo $id; ?>>
        <input type="hidden" id="classId" name = "class_id" class="form-control" value ="<?=$course[0]?>"/>
        <button class = "btn btn-lg btn-danger" type = "submit">No</button>
      </form>
    </div>
  </div>
</div>