// This file is only here as an example of Object-Oriented Javascript

/* Dashboard class */
function Calendar(){
	this.access = "html body div#calenderAccess";
	this.container = "html body div#calendar";
	this.optionsBar = "html body div#calendar div#optionsBar";
	this.pastWeek1 = "html body div#calendar div.pastWeek1 div.innerMinimizedWeek";
	this.pastWeek1Outer = "html body div#calendar div.pastWeek1";
	this.currentWeekTitles = "html body div#calendar div.currentWeekTitles";
	this.currentWeek = "html body div#calendar div.currentWeek";
	this.futureWeek1 = "html body div#calendar div.futureWeek1 div.innerMinimizedWeek";
	this.futureWeek1Outer = "html body div#calendar div.futureWeek1";
	this.futureWeek2 = "html body div#calendar div.futureWeek2 div.innerMinimizedWeek";
	this.futureWeek2Outer = "html body div#calendar div.futureWeek2";
	this.currentDayInput = "html body div#calendar input#currentWeekStart";
	this.backButton = "html body div#calendar div#pastDayButton";
	this.nextButton = "html body div#calendar div#nextDayButton";
	
	this.open = openCal;
	this.close = closeCal;
	this.initCalendar = initCalendar;
	this.adjustSize = adjustSize;
	this.tasksWidth = tasksWidth;
	this.standardWidth = standardWidth;
	
	// Just give another way to access the same methods (for updating the positions of the "accesses")
	this.adjustAccessesTasksOpen = function(){tasks.adjustAccessesTasksOpen();};
	this.adjustAccessesCalendarOpen = function(){tasks.adjustAccessesCalendarOpen();};
	this.adjustAccessesBothOpen = function(){tasks.adjustAccessesBothOpen();};
	this.adjustAccessesBothNeither = function(){tasks.adjustAccessesBothNeither();};
	
	this.getCurrentTime = getCurrentTime;
	this.setCurrentTime = setCurrentTime;
	// Functions that return html data
	this.makeDay = makeDay;
	this.makeDayTitle = makeDayTitle;
	this.makeMiniDay = makeMiniDay;
	this.makeWeek = makeWeek;
	this.makeWeekTitles = makeWeekTitles;
	this.makeMiniWeek = makeMiniWeek;
	// Functions that populate the calendar
	this.setTime = setTime;
	this.backwardDay = backwardDay;
	this.forwardDay = forwardDay;
	this.backwardWeek = backwardWeek;
	this.forwardWeek = forwardWeek;
	this.forward2Weeks = forward2Weeks;
	// Functions for asthetic animations
	this.initWeekAnimation = initWeekAnimation;
	//this.initBackwardWeekAnimation = initBackwardWeekAnimation;
	this.backwardWeekAnimation = backwardWeekAnimation;
	//this.initForwardWeekAnimation = initForwardWeekAnimation;
	this.forwardWeekAnimation = forwardWeekAnimation;
	//this.initForward2WeeksAnimation = initForward2WeeksAnimation;
	this.forward2WeeksAnimation = forward2WeeksAnimation;
	// Constants
	this.DAY = 86400;
	this.WEEK = 604800;
	// Helper Functions
	this.pasteIntoElement = pasteIntoElement;
}

function openCal(){
	var self = this;
	// Slide calendar up (open)
	$(this.container).children("div").hide();
	$(this.container).css('opacity', '0').show().animate({
		opacity: 1
	}, { duration: 1200, queue: false, easing: 'easeOutCubic', complete: function(){
		$(this).children("div").fadeIn(400);
	}});
	$(this.container).css('opacity', '0').show().animate({ // Animate calendar
		'top': '100px',
		'height': (parseInt($(window).height())-67) + 'px'
	}, { duration: 1200, queue: false });
	// Run calendar init function (things that need to be run once the calendar loads in)
	this.initCalendar();
	// Temprarly stop scrolling of body
	$("html body").css("overflow", "hidden");
}

function closeCal(){
	// Slide calendar down (close)
	$(this.container).animate({ // Animate calendar
		'top': '100%'
	}, { duration: 1200, queue: false, complete: function(){
		$(this).fadeOut(400);
	}});
	// Re-enable scrolling of body
	$("html body").css("overflow", "auto");
}

function initCalendar(){
	// Slide the current week view the 9am
	$(this.currentWeek).scrollTop(Math.round(parseInt($(this.container).height())*.72));
	$(this.currentWeek).scrollbar({
		disableBodyScroll: true
	});
	// Set the current time of the calendar
	this.setTime(this.getCurrentTime());
}

function adjustSize(){
	$(this.container).css('top', '100px').css('height', (parseInt($(window).height())-67) + 'px');
	calendar.initCalendar();
}


function tasksWidth(){
	// Animate width of the calendar so that it can be open at the same time as the tasks area.
	$(this.container).animate({
		'margin-left': '300px'
	}, { duration: 1200, queue: false });
	$(this.backButton).animate({
		'margin-left': '300px'
	}, { duration: 1200, queue: false });
}

function standardWidth(){
	// Animate width of the calendar so that it takes up the full browser window
	$(this.container).animate({
		'margin-left': '0px'
	}, { duration: 1200, queue: false });
	$(this.backButton).animate({
		'margin-left': '0px'
	}, { duration: 1200, queue: false });
}

function getCurrentTime(){
	return parseInt($(this.currentDayInput).val());
}

function setCurrentTime(time){
	$(this.currentDayInput).val(parseInt(time));
}

function makeDay(time, elem, how, displayNone, callback){
	if(displayNone!=true){
		displayNone = 0;
	}else{
		displayNone = 1;
	}
	$.ajax({
		url: "/a.php?p=Global_MakeDay",
		method: "POST",
		data: {day: time, displayNone: displayNone},
		success: function(d){
			pasteIntoElement(String(getAjaxData(d)), elem, how);
			if(callback!=undefined){
				callback();
			}
		},
		async: false
	});
}

function makeDayTitle(time, elem, how, displayNone, callback){
	if(displayNone!=true){
		displayNone = 0;
	}else{
		displayNone = 1;
	}
	$.ajax({
		url: "/a.php?p=Global_MakeDayTitle",
		method: "POST",
		data: {day: time, displayNone: displayNone},
		success: function(d){
			pasteIntoElement(String(getAjaxData(d)), elem, how);
			if(callback!=undefined){
				callback();
			}
		},
		async: false
	});
}

function makeMiniDay(time, elem, how, displayNone, callback){
	if(displayNone!=true){
		displayNone = 0;
	}else{
		displayNone = 1;
	}
	$.ajax({
		url: "/a.php?p=Global_MakeMiniDay",
		method: "POST",
		data: {day: time, displayNone: displayNone},
		success: function(d){
			pasteIntoElement(String(getAjaxData(d)), elem, how);
			if(callback!=undefined){
				callback();
			}
		},
		async: false
	});
}

function makeWeek(time, elem, how){
	return this.makeDay(time+(this.DAY*6), elem, how)
	     + this.makeDay(time+(this.DAY*5), elem, how)
	     + this.makeDay(time+(this.DAY*4), elem, how)
		 + this.makeDay(time+(this.DAY*3), elem, how)
		 + this.makeDay(time+(this.DAY*2), elem, how)
		 + this.makeDay(time+this.DAY, elem, how)
		 + this.makeDay(time, elem, how);
}

function makeWeekTitles(time, elem, how){
	return this.makeDayTitle(time+(this.DAY*6), elem, how)
	     + this.makeDayTitle(time+(this.DAY*5), elem, how)
	     + this.makeDayTitle(time+(this.DAY*4), elem, how)
		 + this.makeDayTitle(time+(this.DAY*3), elem, how)
		 + this.makeDayTitle(time+(this.DAY*2), elem, how)
		 + this.makeDayTitle(time+this.DAY, elem, how)
		 + this.makeDayTitle(time, elem, how);
}

function makeMiniWeek(time, elem, how, callback){
	return this.makeMiniDay(time+(this.DAY*6), elem, how)
	     + this.makeMiniDay(time+(this.DAY*5), elem, how)
	     + this.makeMiniDay(time+(this.DAY*4), elem, how)
		 + this.makeMiniDay(time+(this.DAY*3), elem, how)
		 + this.makeMiniDay(time+(this.DAY*2), elem, how)
		 + this.makeMiniDay(time+this.DAY, elem, how)
		 + this.makeMiniDay(time, elem, how, false, callback); // Pass the callback at the last call...
}

function setTime(time, callback){ // Time represents the first day of the current week
	$(this.pastWeek1Outer).last().children("div.innerMinimizedWeek").children("div.miniDayView").remove(); // Remove existing days
	this.makeMiniWeek(time-this.WEEK, $(this.pastWeek1Outer).last().children("div.innerMinimizedWeek"), 'prepend');
	$(this.currentWeekTitles).last().children("div").remove(); // Remove existing day titles
	this.makeWeekTitles(time, $(this.currentWeekTitles).last(), 'prepend');
	$(this.currentWeek).last().children("div.dayView").remove(); // Remove existing days
	this.makeWeek(time, $(this.currentWeek).last(), 'prepend');
	$(this.futureWeek1Outer).last().children("div.innerMinimizedWeek").children("div.miniDayView").remove(); // Remove existing days
	this.makeMiniWeek(time+this.WEEK, $(this.futureWeek1Outer).last().children("div.innerMinimizedWeek"), 'prepend');
	$(this.futureWeek2Outer).last().children("div.innerMinimizedWeek").children("div.miniDayView").remove(); // Remove existing days
	this.makeMiniWeek(time+(this.WEEK*2), $(this.futureWeek2Outer).last().children("div.innerMinimizedWeek"), 'prepend', callback);
	this.setCurrentTime(time);
	// The scroll the current week in most cases (all cases after initial load)
	$(this.currentWeek).animate({ scrollTop: parseInt($(this.currentWeek).height()) });
	// The scroll the current week in 1 second after (special case for firs time [load])
	setTimeout(function(){
		$(this.currentWeek).animate({ scrollTop: parseInt($(this.currentWeek).height()) });
	}, 1000);
}

function backwardDay(){
	// Note: this function assumes that setTime() has already been run at least once
	var self = this;
	this.makeMiniDay(this.getCurrentTime()-(8*this.DAY), this.pastWeek1, 'prepend', true, function(){
		$(self.pastWeek1).children("div.miniDayView:first").css("margin-left", (-1*parseInt($(self.pastWeek1).children("div.miniDayView:first").width())) + 'px').show().animate({
			'margin-left': '0px'
		}, { async: false, duration: 400, complete: function(){
			$(self.pastWeek1).children("div.miniDayView:last").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeDayTitle(this.getCurrentTime()-this.DAY, this.currentWeekTitles, 'prepend', true, function(){
		$(self.currentWeekTitles).children("div:first").css("margin-left", (-1*parseInt($(self.currentWeekTitles).children("div:first").width())) + 'px').show().animate({
			'margin-left': '0px'
		}, { async: false, duration: 400, complete: function(){
			$(self.currentWeekTitles).children("div:last").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeDay(this.getCurrentTime()-this.DAY, this.currentWeek, 'prepend', true, function(){
		$(self.currentWeek).children("div.dayView:first").css("margin-left", (-1*parseInt($(self.currentWeek).children("div.dayView:first").width())) + 'px').show().animate({
			'margin-left': '0px'
		}, { async: false, duration: 400, complete: function(){
			$(self.currentWeek).children("div.dayView:last").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeMiniDay(this.getCurrentTime()+(6*this.DAY), this.futureWeek1, 'prepend', true, function(){
		$(self.futureWeek1).children("div.miniDayView:first").css("margin-left", (-1*parseInt($(self.futureWeek1).children("div.miniDayView:first").width())) + 'px').show().animate({
			'margin-left': '0px'
		}, { async: false, duration: 400, complete: function(){
			$(self.futureWeek1).children("div.miniDayView:last").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeMiniDay(this.getCurrentTime()+(13*this.DAY), this.futureWeek2, 'prepend', true, function(){
		$(self.futureWeek2).children("div.miniDayView:first").css("margin-left", (-1*parseInt($(self.futureWeek2).children("div.miniDayView:first").width())) + 'px').show().animate({
			'margin-left': '0px'
		}, { async: false, duration: 400, complete: function(){
			$(self.futureWeek2).children("div.miniDayView:last").remove(); // Remove old day at the beginning
		} });
	});
	
	this.setCurrentTime(this.getCurrentTime()-this.DAY);
}

function forwardDay(){
	// Note: this function assumes that setTime() has already been run at least once
	var self = this;
	this.makeMiniDay(this.getCurrentTime(), $(this.pastWeek1).children("div.miniDayView:last"), 'after', false, function(){
		$(self.pastWeek1).children("div.miniDayView:first").animate({
			'margin-left': (-1*parseInt($(self.pastWeek1).children("div.miniDayView:first").width())) + 'px'
		}, { async: false, duration: 400, complete: function(){
			$(self.pastWeek1).children("div.miniDayView:first").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeDayTitle(this.getCurrentTime()+this.WEEK, $(this.currentWeekTitles).children("div:last"), 'after', false, function(){
		$(self.currentWeekTitles).children("div:first").animate({
			'margin-left': (-1*parseInt($(self.currentWeekTitles).children("div:first").width())) + 'px'
		}, { async: false, duration: 400, complete: function(){
			$(self.currentWeekTitles).children("div:first").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeDay(this.getCurrentTime()+this.WEEK, $(this.currentWeek).children("div.dayView:last"), 'after', false, function(){
		$(self.currentWeek).children("div.dayView:first").animate({
			'margin-left': (-1*parseInt($(self.currentWeek).children("div.dayView:first").width())) + 'px'
		}, { async: false, duration: 400, complete: function(){
			$(self.currentWeek).children("div.dayView:first").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeMiniDay(this.getCurrentTime()+(2*this.WEEK), $(this.futureWeek1).children("div.miniDayView:last"), 'after', false, function(){
		$(self.futureWeek1).children("div.miniDayView:first").animate({
			'margin-left': (-1*parseInt($(self.futureWeek1).children("div.miniDayView:first").width())) + 'px'
		}, { async: false, duration: 400, complete: function(){
			$(self.futureWeek1).children("div.miniDayView:first").remove(); // Remove old day at the beginning
		} });
	});
	
	this.makeMiniDay(this.getCurrentTime()+(3*this.WEEK), $(this.futureWeek2).children("div.miniDayView:last"), 'after', false, function(){
		$(self.futureWeek2).children("div.miniDayView:first").animate({
			'margin-left': (-1*parseInt($(self.futureWeek2).children("div.miniDayView:first").width())) + 'px'
		}, { async: false, duration: 400, complete: function(){
			$(self.futureWeek2).children("div.miniDayView:first").remove(); // Remove old day at the beginning
		} });
	});
	
	this.setCurrentTime(this.getCurrentTime()+this.DAY);
}

function backwardWeek(callback){
	this.setTime(this.getCurrentTime()-this.WEEK, callback);
}

function forwardWeek(callback){
	this.setTime(this.getCurrentTime()+this.WEEK, callback);
}

function forward2Weeks(callback){
	this.setTime(this.getCurrentTime()+(this.WEEK*2), callback);
}

function initWeekAnimation(callback){
	var optionsBarHeight = parseInt($(this.optionsBar).height());
	var pastWeekHeight = parseInt($(this.container).children("div.pastWeek1").height());
	var currentWeekTitlesHeight = parseInt($(this.currentWeekTitles).height());
	var currentWeekHeight = parseInt($(this.currentWeek).height());
	var futureWeek1Height = parseInt($(this.container).children("div.futureWeek1").height());
	var borderTotalHeight = 2;
	// futureWeek2 to currentWeek
	$(this.futureWeek2Outer).before($(this.futureWeek2Outer).clone());
	$(this.futureWeek2Outer).first().css('z-index','1000').css('position', 'absolute').css('top', parseInt(optionsBarHeight+pastWeekHeight+currentWeekTitlesHeight+currentWeekHeight+futureWeek1Height+borderTotalHeight) + 'px');
	// futureWeek1 to pastWeek1
	$(this.futureWeek1Outer).before($(this.futureWeek1Outer).clone());
	$(this.futureWeek1Outer).first().css('z-index','1100').css('position', 'absolute').css('top', parseInt(optionsBarHeight+pastWeekHeight+currentWeekTitlesHeight+currentWeekHeight+borderTotalHeight) + 'px');
	// Make fake element to cover current week
	$(this.currentWeek).before($(this.currentWeek).clone());
	$(this.currentWeek).first().css('z-index','1100').css('position', 'absolute').css('top', parseInt(optionsBarHeight+pastWeekHeight+currentWeekTitlesHeight) + 'px');
	// Make fake element to cover currentWeekTitles
	$(this.currentWeekTitles).before($(this.currentWeekTitles).clone());
	$(this.currentWeekTitles).first().css('z-index','1100').css('position', 'absolute').css('top', parseInt(optionsBarHeight+pastWeekHeight) + 'px');
	// Make fake element to cover past week
	$(this.pastWeek1Outer).before($(this.pastWeek1Outer).clone());
	$(this.pastWeek1Outer).first().css('z-index','1100').css('position', 'absolute').css('top', parseInt(optionsBarHeight) + 'px');
	// Remove the timesKey on the right-hand side of the current week area
	$(this.currentWeek).first().children("div#timesKey").remove();
	$(this.currentWeek).first().children("div#pastDayButton").remove();
	$(this.currentWeek).first().children("div#nextDayButton").remove();
	// Callback function
	if(callback!=undefined){
		callback();
	}
}

function backwardWeekAnimation(callback){
	var self = this;
	var optionsBarHeight = parseInt($(this.optionsBar).height());
	var pastWeekHeight = parseInt($(this.container).children("div.pastWeek1").height());
	var currentWeekTitlesHeight = parseInt($(this.currentWeekTitles).height());
	var currentWeekHeight = parseInt($(this.currentWeek).height());
	var futureWeek1Height = parseInt($(this.container).children("div.futureWeek1").height());
	var borderTotalHeight = 2;
	// Transition futureWeek1 to futureWeek2
	$(this.futureWeek1Outer).first().animate({
		'margin-top': (futureWeek1Height) + 'px'
	}, { async: false, duration: 400, complete: function(){
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
		// Remove fake past week2 cover
		$(self.futureWeek2Outer).first().remove();
	}});
	// Transition currentWeek to futureWeek1
	$(this.currentWeek).first().animate({
		'margin-top': (currentWeekHeight+borderTotalHeight+parseInt(.2*futureWeek1Height)) + 'px',
		'height': parseInt($(this.futureWeek1Outer).last().children("div.innerMinimizedWeek").height()) + 'px'
	}, { async: false, duration: 400, complete: function(){
		// Fade out animated cover to reveal actual
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
	}});
	// Transition pastWeek1 to currentWeek
	$(this.pastWeek1Outer).first().animate({
		'margin-top': (currentWeekTitlesHeight+pastWeekHeight) + 'px',
		'height': currentWeekHeight + 'px'
	}, { async: false, duration: 400, complete: function(){
		// Fade out animated cover to reveal actual
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
		$(self.currentWeekTitles).first().fadeOut(400, function(){
			$(this).remove();
			// Callback function
			if(callback!=undefined){
				callback();
			}
		});
		
	}});
	$(this.pastWeek1Outer).first().children("div.innerMinimizedWeek").children("div.miniDayView").animate({
		'color': '#F3F4F4'
	});
}

function forwardWeekAnimation(callback){
	var self = this;
	var optionsBarHeight = parseInt($(this.optionsBar).height());
	var pastWeekHeight = parseInt($(this.container).children("div.pastWeek1").height());
	var currentWeekTitlesHeight = parseInt($(this.currentWeekTitles).height());
	var currentWeekHeight = parseInt($(this.currentWeek).height());
	var futureWeek1Height = parseInt($(this.container).children("div.futureWeek1").height());
	var borderTotalHeight = 2;
	// Transition futureWeek2 to futureWeek1
	$(this.futureWeek2Outer).first().animate({
		'margin-top': (-1*futureWeek1Height) + 'px'
	}, { async: false, duration: 400, complete: function(){
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
	}});
	// Transition futureWeek1 to currentWeek
	$(this.futureWeek1Outer).first().animate({
		'margin-top': (-1*(currentWeekHeight+borderTotalHeight)) + 'px',
		'height': currentWeekHeight
	}, { async: false, duration: 400, complete: function(){
		// Remove fake current week cover
		$(self.currentWeek).first().remove();
		// Fade out animated cover to reveal actual
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
	}});
	$(this.futureWeek1Outer).first().children("div.innerMinimizedWeek").children("div.miniDayView").animate({ // Animate inner children num color
		'color': 'white'
	}, { async: false, duration: 400 });
	$(this.futureWeek1Outer).first().children("div.innerMinimizedWeekGap").animate({ // Animate the mini gap to zero
		'height': '0px'
	}, { async: false, duration: 400 });
	$(this.futureWeek1Outer).first().children("div.innerMinimizedWeek").animate({ // Animate the inner area to 100% (since gap is gone)
		'height': currentWeekHeight + 'px'
	}, { async: false, duration: 400 });
	// Transition currentWeekTitles to PastWeek1
	$(this.currentWeekTitles).first().animate({
		'margin-top': (-1*pastWeekHeight) + 'px',
		'height': pastWeekHeight + 'px',
		'background-color': 'white'
	}, { async: false, duration: 400, complete: function(){
		// Remove fake past week cover
		$(self.pastWeek1Outer).first().remove();
		// Fade out animated cover to reveal actual
		$(this).fadeOut(400, function(){
			$(this).remove();
			// Callback function
			if(callback!=undefined){
				callback();
			}
		});
	}});
	$(this.currentWeekTitles).first().children("div").children("span").animate({ // Animate the text color of the inner current week titles
		'color': 'white',
		'border-right-width': '1px',
		'border-right-color': '#D7D8D9',
		'border-right-style': 'solid'
	}, { async: false, duration: 400 });
}

function forward2WeeksAnimation(callback){
	var self = this;
	var optionsBarHeight = parseInt($(this.optionsBar).height());
	var pastWeekHeight = parseInt($(this.container).children("div.pastWeek1").height());
	var currentWeekTitlesHeight = parseInt($(this.currentWeekTitles).height());
	var currentWeekHeight = parseInt($(this.currentWeek).height());
	var futureWeek1Height = parseInt($(this.container).children("div.futureWeek1").height());
	var borderTotalHeight = 2;
	// Transition futureWeek2 to currentWeek
	$(this.futureWeek2Outer).first().animate({
		'margin-top': (-1*(futureWeek1Height+currentWeekHeight+borderTotalHeight)) + 'px',
		'height': currentWeekHeight
	}, { async: false, duration: 400, complete: function(){
		$(this).fadeOut(400, function(){
			$(this).remove();
		});
	}});
	$(this.futureWeek2Outer).first().children("div.innerMinimizedWeek").children("div.miniDayView").animate({ // Animate inner children num color
		'color': 'white'
	}, { async: false, duration: 400 });
	$(this.futureWeek2Outer).first().children("div.innerMinimizedWeekGap").animate({ // Animate the mini gap to zero
		'height': '0px'
	}, { async: false, duration: 400 });
	$(this.futureWeek2Outer).first().children("div.innerMinimizedWeek").animate({ // Animate the inner area to 100% (since gap is gone)
		'height': currentWeekHeight + 'px'
	}, { async: false, duration: 400 });
	// Transition futureWeek1 to PastWeek1
	$(this.futureWeek1Outer).first().animate({
		'margin-top': (-1*(pastWeekHeight+currentWeekHeight+currentWeekTitlesHeight+borderTotalHeight)) + 'px',
		'height': pastWeekHeight + 'px',
		'background-color': 'white'
	}, { async: false, duration: 400, complete: function(){
		// Remove fake past week cover
		$(self.currentWeekTitles).first().fadeOut(400, function(){
			$(this).remove();
		});
		// Fade out animated cover to reveal actual
		$(this).fadeOut(400, function(){
			$(this).remove();
			// Callback function
			if(callback!=undefined){
				callback();
			}
		});
		// Remove other fake covers
		$(self.currentWeek).first().remove();
		$(self.pastWeek1Outer).first().remove();
	}});
	$(this.futureWeek1Outer).first().children("div.innerMinimizedWeekGap").animate({ // Animate the mini gap to zero
		'height': '0px'
	}, { async: false, duration: 400 });
	$(this.futureWeek1Outer).first().children("div.innerMinimizedWeek").animate({ // Animate the inner area to 100% (since gap is gone)
		'height': currentWeekHeight + 'px'
	}, { async: false, duration: 400 });
}

function pasteIntoElement(data, elem, how){
	switch(how){
		case 'prepend':
			$(elem).prepend(data);
			break;
		case 'after':
			$(elem).after(data);
			break;
	}
}

var animating = false; // Used to disallow animations from happening at the same time
/* Dashboard Event Handlers */
$(function(){
	$(window).on('resize', function(){
		// Resize the calendar when someone changes the window size
		calendar.adjustSize();
	});
	$(document).on('click', "div#" + $(calendar.backButton).attr("id"), function(){
		if(animating){
			return;
		}else{
			animating = true;
		}
		// Move backwards by one day
		calendar.backwardDay();
		setTimeout(function(){ // This timeout to completely ensure that no bugs occur with the events overlapping with rapid-clicks.
			animating = false;
		}, 500);
	});
	$(document).on('click', "div#" + $(calendar.nextButton).attr("id"), function(){
		if(animating){
			return;
		}else{
			animating = true;
		}
		// Move forwards by one day
		calendar.forwardDay();
		setTimeout(function(){ // This timeout to completely ensure that no bugs occur with the events overlapping with rapid-clicks.
			animating = false;
		}, 500);
	});
	$(document).on('click', "div.pastWeek1", function(){
		if(animating){
			return;
		}else{
			animating = true;
		}
		// Move backwards by one week
		calendar.initWeekAnimation(function(){
			calendar.backwardWeek(function(){
				calendar.backwardWeekAnimation(function(){
					setTimeout(function(){ // This timeout to completely ensure that no bugs occur with the events overlapping with rapid-clicks.
						animating = false;
					}, 500);
				});
			});
		});
	});
	
	$(document).on('click', "div.futureWeek1", function(){
		if(animating){
			return;
		}else{
			animating = true;
		}
		// Move backwards by one week
		calendar.initWeekAnimation(function(){
			calendar.forwardWeek(function(){
				calendar.forwardWeekAnimation(function(){
					setTimeout(function(){ // This timeout to completely ensure that no bugs occur with the events overlapping with rapid-clicks.
						animating = false;
					}, 500);
				});
			});
		});
	});
	$(document).on('click', "div.futureWeek2", function(){
		if(animating){
			return;
		}else{
			animating = true;
		}
		// Move backwards by two weeks
		calendar.initWeekAnimation(function(){
			calendar.forward2Weeks(function(){
				calendar.forward2WeeksAnimation(function(){
					setTimeout(function(){ // This timeout to completely ensure that no bugs occur with the events overlapping with rapid-clicks.
						animating = false;
					}, 500);
				});
			});
		});
	});
});