// This file remains as an example of dynamically creating an Ajax based multipage website.

/* ON PAGE LOAD */
/*$(function(){
	/* FOR REACTING TO DYNAMICALLY CHANGED URLS *//*
	History.Adapter.bind(window,'statechange',function(e){
		/* For closing the 'events' area *//*
		var State = History.getState();
		var url = State.url.replace('http://', '').replace('localhost:8888', '');
		if(url=='/'||url==''){
			url = '/user/';
		}
		$.post('/Static/PHP/getPage.php', {url: url}, function(r){
			//$("html body div#dynamicPageContent").slideUp(800, function(){
				$("html body div#dynamicPageContent").html(r);
				//$("html body div#dynamicPageContent").slideDown(800, function(){
					initTextareaAutoResize();
				//});
			//});
		});
		// Reload the dashboard
		//dashboard.createDashboard(); // Needs to include HUGE type and ID, etc...
	});
	/* FOR MAKING ALL LINKS DYNAMICALLY RELOAD *//*
	$(document).on('click', "html body a.link", function(e){ // Link click
		e.preventDefault();
		History.pushState({link: $(this).attr("href")}, 'Lotsa', $(this).attr("href"));
		return false;
	});
});*/