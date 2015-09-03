
$(document).ready(function(){
    $("body").on('click', '.btn.btn-lg.btn.primary.btn-block', function() {
        //var $inputs = $form.find("input, select, button, textarea");
        var $form = $(this).parent();
        var $inputs = $form.find("input, select, button, textarea");

        var $year = $(this).parent().find("#inputYear").val();
        var $major = $(this).parent().find("#inputMajor").val();

        //alert($year+$major);
        // GET data from the initial form.
        $inputs.prop("disabled", true);

        request = $.ajax({
            url: "../bootstrap-3.3.5-dist/resources/session.php",
            type: "post",
            data: {"year" : $year,
                    "major" : $major}
        });

        request.done(function (response, textStatus, jqXHR){
            console.log("Hooray, it worked!");
            //alert("TEST");
            var answer = JSON.parse(response);
            //alert(answer["sql"]);
        });


        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
        });
        $(".col-md-3").remove();
        for(i=0;i<4;i++){
            //alert("stop")
            $.get("../bootstrap-3.3.5-dist/resources/course.php", function( my_var ) {
                //alert("test");
                var $html_data = $(my_var);

                request = $.ajax({
                    url: "../bootstrap-3.3.5-dist/resources/getCourse.php",
                    type: "post"
                });

                request.done(function (response, textStatus, jqXHR){
                    console.log("Hooray, it worked!");
                    //alert("b4 error");
                    var answer = JSON.parse(response);
                    //alert("after error");
                    //alert(answer['getid']+"|"+answer['getname']);
                    $html_data.find('.department').html(answer['getdept']);
                    $html_data.find('.courseNumber').html(answer['getnumber']);
                    $html_data.find('.courseId').html(answer['getid']);
                
                    $html_data.find('.courseName').html(answer['getname']);
                    $html_data.find('.courseRating').html(answer['getrating']);
                });
                //alert("done");


                // Callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus, errorThrown){
                    // Log the error to the console
                    console.error(
                        "The following error occurred: "+
                        textStatus, errorThrown
                    );
                });


                // Prevent default posting of form
                
                $(".row").append($html_data);
                
            });    
        }
    });
    
});
$(document).ready(function(){



});

$(document).ready(function(){

	$("body").on('click', '.btn.btn-lg.btn-success', function() {
        var $form = $(this).parent();
        var $inputs = $form.find("input, select, button, textarea");
        var $courseId = $(this).parent().parent().parent().find('.courseId').text();

		$( this ).parent().parent().parent().css("background-color", "#787878");
        $button = $(this);
        $inputs.prop("disabled", true);

        request = $.ajax({
            url: "../bootstrap-3.3.5-dist/resources/yes.php",
            type: "post",
            data: {"courseId" : $courseId}
        });

        request.done(function (response, textStatus, jqXHR){
            console.log("Hooray, it worked!");
            var answer = JSON.parse(response);
            $button.parent().parent().parent().find('.courseRating').html(answer['getrating']);

        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
        });


        // Prevent default posting of form
        event.preventDefault();
	});
	
});

$(document).ready(function(){
	$("body").on('click', '.btn.btn-lg.btn-primary', function() {//TOOK
		//Need to send course number that you said you took to AJAX so the database can update.
        var $form = $(this).parent();
        var $inputs = $form.find("input, select, button, textarea");
        var $courseId = $(this).parent().parent().parent().find('.courseId').text();
        //alert("COURSEID:"+courseId);

        $button = $(this);
        $inputs.prop("disabled", true);
        $( this ).parent().parent().parent().parent().fadeOut();//Change class in the meantime
        $( this ).parent().parent().parent().css("background-color", "#eee");
        $( this ).parent().parent().parent().parent().fadeIn();
        


        request = $.ajax({
            url: "../bootstrap-3.3.5-dist/resources/took.php",
            type: "post",
            data: {"courseId" : $courseId}
        });
        //alert(request.data);
        //alert("Ajax sent");
        request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
            console.log("Hooray, it worked!");
            var answer = JSON.parse(response);
            //alert(answer);
            //alert(answer['getid']);
            //alert($(this).parent().parent().parent().find('.courseId').text());
            $button.parent().parent().parent().find('.department').html(answer['getdept']);
            $button.parent().parent().parent().find('.courseNumber').html(answer['getnumber']);
            $button.parent().parent().parent().find('.courseId').html(answer['getid']);
            $button.parent().parent().parent().find('.courseName').html(answer['getname']);
            $button.parent().parent().parent().find('.courseRating').html(answer['getrating']);

        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
        });


        // Prevent default posting of form
        event.preventDefault();


        //AJAX from course id number

	});
	
});


$(document).ready(function(){
	$("body").on('click', '.btn.btn-lg.btn-danger', function() {
		var $form = $(this).parent();
        var $inputs = $form.find("input, select, button, textarea");
        var $courseId = $(this).parent().parent().parent().find('.courseId').text();

        
        $button = $(this);
        $inputs.prop("disabled", true);
                //alert("COURSEID:"+courseId);
        $( this ).parent().parent().parent().parent().fadeOut();//Change class in the meantime
        $( this ).parent().parent().parent().css("background-color", "#eee");
        $( this ).parent().parent().parent().parent().fadeIn();


        request = $.ajax({
            url: "../bootstrap-3.3.5-dist/resources/no.php",
            type: "post",
            data: {"courseId" : $courseId}
        });
        //alert(request.data);
        //alert("Ajax sent");
        request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
            console.log("Hooray, it worked!");
            var answer = JSON.parse(response);
            //alert(answer);
            //alert(answer['getid']);
            //alert($(this).parent().parent().parent().find('.courseId').text());
            $button.parent().parent().parent().find('.department').html(answer['getdept']);
            $button.parent().parent().parent().find('.courseNumber').html(answer['getnumber']);
            $button.parent().parent().parent().find('.courseId').html(answer['getid']);
        
            $button.parent().parent().parent().find('.courseName').html(answer['getname']);
            $button.parent().parent().parent().find('.courseRating').html(answer['getrating']);
        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
        });


        // Prevent default posting of form
        event.preventDefault();
	});
});

//$(".col-md-3") function(){
	//use ajax to grab the class_name and class_id to put on the labels.

//}
