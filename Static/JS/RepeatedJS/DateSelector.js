// This remains as an example of a repeated element's object in Javascript

function DateSelector(){
	this.className; // This is the name of the class that is the parent of the whole dateSelector dialog and must be set in init().
	// These are set in init()...
	this.leftCalendarArea;
	this.currentMonthStartInput;
	this.intervalMonthStart;
	this.intervalStart;
	this.intervalEnd;
	this.monthName;
	this.calendarView;
	this.rightSelectorArea;
	this.startDate;
	this.startTime;
	this.endDate;
	this.endTime;
	
	// Optional settings
	this.startDateOnly = false;
	this.noTime = false;
	
	this.initalized = false;
	this.init = initDateSelector;
	
	this.moveTimeForwardMonth = moveTimeForwardMonth;
	this.moveTimeBackwardMonth = moveTimeBackwardMonth;
	this.setCalendarTime = setCalendarTime;
	
	this.setCalendarInterval = setCalendarInterval;
	this.setIntervalStartTime = setIntervalStartTime;
	this.setIntervalEndTime = setIntervalEndTime;
	this.clearEndInterval = clearEndInterval;
	this.clearInterval = clearInterval;
	
	//this.initDateSelectionArea = initDateSelectionArea;
	this.setStartOfDateSelectionArea = setStartOfDateSelectionArea;
	this.setEndOfDateSelectionArea = setEndOfDateSelectionArea;
	
	this.restrictDays = restrictDays;
	this.unrestrictAllDays = unrestrictAllDays;
	this.onlyRestrictPastDays = onlyRestrictPastDays;
}

function initDateSelector(className, startDateOnly, noTime){
	// Set the optional parameter values
	if(typeof startDateOnly != 'undefined' && startDateOnly != false){ // Only display a start date/time for this instance
		this.startDateOnly = true;
	}
	if(typeof noTime != 'undefined' && noTime!=false){ // Only display date, don't display time (minutes, hours) for this instance
		this.noTime = true;
	}
	// Because we need to be able to access this inside children functions
	var self = this;
	this.className = className;
	// **** SET ALL CLASS ELEMENT PATHS BASED ON CLASSNAME ****
	this.leftCalendarArea = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea";
	this.currentMonthStartInput = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea input.currentMonthStart";
	this.intervalMonthStart = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea input.intervalMonthStart";
	this.intervalStart = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea input.intervalStart";
	this.intervalEnd = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea input.intervalEnd";
	this.monthName = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea div.monthSelect div.monthName";
	this.calendarView = "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea div.calendarView";
	this.rightSelectorArea = "div." + this.className + " div.calendarSelectorComponent div.rightSelectorArea";
	this.startDate = "div." + this.className + " div.calendarSelectorComponent div.rightSelectorArea div.startDate";
	this.startTime = "div." + this.className + " div.calendarSelectorComponent div.rightSelectorArea div.startTime";
	this.endDate = "div." + this.className + " div.calendarSelectorComponent div.rightSelectorArea div.endDate";
	this.endTime = "div." + this.className + " div.calendarSelectorComponent div.rightSelectorArea div.endTime";
	// Update input names to reflect the className passed
	$(this.leftCalendarArea).children("input.intervalMonthStart").attr("name", this.className + "_IntervalMonthStart");
	$(this.leftCalendarArea).children("input.intervalStart").attr("name", this.className + "_IntervalStart");
	$(this.leftCalendarArea).children("input.intervalEnd").attr("name", this.className + "_IntervalEnd");
	// Required initializer calls
	$(this.currentMonthStartInput).val((new Date()).getTime());
	this.setCalendarTime();
	this.restrictDays();
	// **** EVENT HANDLERS ****
	// To avoid re-initalization of event handlers
	if(this.initalized){
		return;
	}else{
		this.initalized = true;
		// When someone clicks one of the arrows
		$(document).on('click', "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea div.monthSelect div#leftArrow", function(){
			self.moveTimeBackwardMonth();
			self.setCalendarTime();
			self.setCalendarInterval();
			if($("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.daySelection:first').hasClass('startDate')||self.startDateOnly){ // End date set or we only want a start date
				self.onlyRestrictPastDays();
			}else{
				self.restrictDays();
			}
		});
		$(document).on('click', "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea div.monthSelect div#rightArrow", function(){
			self.moveTimeForwardMonth();
			self.setCalendarTime();
			self.setCalendarInterval();
			if($("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.daySelection:first').hasClass('startDate')||self.startDateOnly){ // End date set or we only want a start date
				self.onlyRestrictPastDays();
			}else{
				self.restrictDays();
			}
		});
		// When someone clicks on a day
		$(document).on('click', "div." + this.className + " div.calendarSelectorComponent div.leftCalendarArea div.calendarView div.dayReal", function(){
			if(!$(this).hasClass("noDayClick")){
				if($("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.daySelection:first').hasClass('startDate')||self.startDateOnly){ // Start date
					self.clearInterval();
					self.setIntervalStartTime(parseInt($(this).text()));
					self.setStartOfDateSelectionArea(parseInt($(this).text()));
					if(!self.startDateOnly){
						self.setEndOfDateSelectionArea(parseInt($(this).text()));
					}
					self.restrictDays();
					if(!self.startDateOnly){
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.daySelection').removeClass('daySelection');
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.endDate').addClass('daySelection');
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.endTime').addClass('daySelection');
					}
				}else if(!self.startDateOnly){ // End date (only if this isn't case where we only have a start day)
					var visibleDate = new Date(parseInt($(self.currentMonthStartInput).val()));
					var stateDate = new Date(parseInt($(self.intervalMonthStart).val()));
					if((parseInt(visibleDate.getMonth())==
					    parseInt(stateDate.getMonth())&&
					    parseInt($(this).text())>=
					    parseInt($(self.intervalStart).val()))||( // Only allow it if it's not before the start date
					    parseInt(visibleDate.getMonth())==
					    parseInt(stateDate.getMonth())+1&&
					    parseInt($(this).text())<
					    parseInt($(self.intervalStart).val()))||(
					    parseInt(visibleDate.getMonth())==0&&
					    parseInt(stateDate.getMonth())==11&&
					   	parseInt($(this).text())<
					    parseInt($(self.intervalStart).val()))){
						self.clearEndInterval();
						self.setIntervalEndTime(parseInt($(this).text()));
						if(!self.startDateOnly){
							self.setEndOfDateSelectionArea(parseInt($(this).text()));
						}
						self.onlyRestrictPastDays();
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.daySelection').removeClass('daySelection');
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.startDate').addClass('daySelection');
						$("div." + self.className + ' div.calendarSelectorComponent div.rightSelectorArea div.startTime').addClass('daySelection');
					}
				}
			}
		});
	}
}

function moveTimeForwardMonth(){
	var currMonth = new Date(parseInt($(this.currentMonthStartInput).val()));
	if(currMonth.getMonth()==11){
		$(this.currentMonthStartInput).val((new Date(parseInt(currMonth.getFullYear())+1,
									 0,1,0,0,0,0)).getTime());
	}else{
		$(this.currentMonthStartInput).val((new Date(currMonth.getFullYear(),
									 parseInt(currMonth.getMonth())+1,
									 1,0,0,0,0)).getTime());
	}
}

function moveTimeBackwardMonth(){
	var currMonth = new Date(parseInt($(this.currentMonthStartInput).val()));
	if(currMonth.getMonth()==0){
		$(this.currentMonthStartInput).val((new Date(parseInt(currMonth.getFullYear())-1,
									 11,1,0,0,0,0)).getTime());
	}else{
		$(this.currentMonthStartInput).val((new Date(currMonth.getFullYear(),
									 parseInt(currMonth.getMonth())-1,
									 1,0,0,0,0)).getTime());
	}
}

function setCalendarTime(){
	var currMonth = new Date(parseInt($(this.currentMonthStartInput).val()));
	// Set the month year titles at the top
	$(this.monthName).text(numMonthtToWordFull(currMonth.getMonth()) + ' ' + currMonth.getFullYear());
	var dayOfWeek = 0;
	var totalDays = 0;
	// Make sure that we're getting exactly the first day of month
	if(currMonth.getDate()!=1){
		currMonth = new Date(currMonth.getFullYear(), currMonth.getMonth(), 1, 0, 0, 0, 0);
	}
	if(currMonth.getDay()==dayOfWeek){
		var monthStarted = true;
	}else{
		var monthStarted = false;
	}
	var str = "";
	var monthEnded = false;
	while(true){
		// Check if we should start the month
		if(currMonth.getDay()==dayOfWeek&&!monthStarted){
			monthStarted = true;
		}
		if(!monthStarted||monthEnded){ // Month hasn't started yet or it is over
			str += "<div class='day'></div>";
		}else{ // Output standard day
			str += "<div class='day dayReal clickable'>" + currMonth.getDate() + "</div>";
		}
		if(parseInt(currMonth.getDate())+1>numDaysInMonth(currMonth.getMonth(), currMonth.getFullYear())){
			monthEnded = true;
		}else{
			if(monthStarted&&!monthEnded){
				currMonth.setDate(parseInt(currMonth.getDate())+1);
			}
		}
		dayOfWeek++;
		totalDays++;
		if(dayOfWeek==7){
			if(monthEnded&&Math.ceil(totalDays/7)>=7){
				break;
			}
			dayOfWeek = 0;
		}
	}
	$(this.calendarView).html(str + '<div class="clear"></div>');
}

function setCalendarInterval(){
	var self = this;
	var startSeen = false;
	var visibleDate = new Date(parseInt($(this.currentMonthStartInput).val()));
	var visibleMonth = visibleDate.getMonth();
	var startDate = new Date(parseInt($(this.intervalMonthStart).val()));
	var startMonth = startDate.getMonth();
	var intervalStart = parseInt($(this.intervalStart).val());
	var intervalEnd = parseInt($(this.intervalEnd).val());
	if(visibleMonth==startMonth&&visibleDate.getFullYear()==startDate.getFullYear()){ // Starts and ends in same month
		if(intervalStart>intervalEnd){ // Therefore the interval goes into the next month
			$(this.calendarView).children("div.dayReal").each(function(){
				if(parseInt($(this).text().trim())==intervalStart){
					startSeen = true;
				}
				if(startSeen){
					$(this).addClass('selected');
				}
			});
		}else{
			$(this.calendarView).children("div.dayReal").each(function(){
				if(parseInt($(this).text().trim())==intervalStart){
					startSeen = true;
				}
				if(startSeen){
					$(this).addClass('selected');
				}
				if(parseInt($(this).text().trim())==intervalEnd||intervalEnd==''||isNaN(intervalEnd)||(self.startDateOnly&&startSeen)){ // The second part is if there is no end yet
					return false;
				}
			});
		}
	}else if(!self.startDateOnly){ // Doesn't start and end in the same month
		if((visibleMonth==parseInt(startMonth)+1&&visibleDate.getFullYear()==startDate.getFullYear())||
		   (visibleMonth==0&&startMonth==11&&startDate.getFullYear()==parseInt(visibleDate.getFullYear())-1)){ // Viewing end month
			if(intervalStart>intervalEnd){ // Therefore this interval goes into this month
				if(intervalEnd!=''&&!isNaN(intervalEnd)){ // Only if there is an end
					$(this.calendarView).children("div.dayReal").each(function(){
						$(this).addClass('selected');
						if(parseInt($(this).text().trim())==intervalEnd){
							return false;
						}
					});
				}
			}
		}else if(visibleMonth==parseInt(startMonth)-1&&visibleDate.getFullYear()==startDate.getFullYear()){ // Viewing start month
			if(parseInt($(this).text().trim())==intervalStart){
				startSeen = true;
			}
			if(startSeen){
				$(this).addClass('selected');
			}
			if(parseInt($(this).text().trim())==''||intervalEnd==''||isNaN(intervalEnd)){ // The second part is if there is no end yet
				return false;
			}
		}
	}
}

function setIntervalStartTime(day){
	// Day is an integer representing a day in the current month
	$(this.intervalMonthStart).val($(this.currentMonthStartInput).val());
	$(this.intervalStart).val(day);
	$(this.intervalEnd).val(day); // Because if it's all on one day, they may just click once
	$(this.calendarView).children("div.dayReal").each(function(){
		if($(this).text().trim()==day){
			$(this).addClass("selected");
			return false;
		}
	});
}

function setIntervalEndTime(day){
	// Day is an integer representing a day in the current month
	var startSeen = false;
	$(this.intervalEnd).val(day);
	
	var visibleDate = new Date(parseInt($(this.currentMonthStartInput).val()));
	var visibleMonth = visibleDate.getMonth();
	var startDate = new Date(parseInt($(this.intervalMonthStart).val()));
	var startMonth = startDate.getMonth();
	
	if(visibleMonth==startMonth){ // Starts and ends in same month
		$(this.calendarView).children("div.dayReal").each(function(){
			if($(this).hasClass("selected")){ // First one
				startSeen = true;
			}
			if(startSeen){ // All in interval
				$(this).addClass("selected");
			}
			if($(this).text().trim()==day){ // Stop looping since we are at the end of the interval
				return false;
			}
		});
	}else{
		if((visibleMonth>startMonth&&visibleDate.getFullYear()==startDate.getFullYear())||
		   (visibleMonth==0&&startMonth==11&&(visibleDate.getFullYear()==startDate.getFullYear()+1))){ // Viewing end month
			$(this.calendarView).children("div.dayReal").each(function(){
				$(this).addClass("selected");
				if($(this).text().trim()==day){ // Stop looping since we are at the end of the interval
					return false;
				}
			});
		}else{ // Viewing start month
			$(this.calendarView).children("div.dayReal").each(function(){
				if($(this).hasClass("selected")){ // First one
					startSeen = true;
				}
				if(startSeen){ // All in interval
					$(this).addClass("selected");
				}
			});
		}
	}
}

function clearEndInterval(){
	$(this.intervalEnd).val('');
	var startSeen = false;
	var start = parseInt($(this.intervalStart).val());
	$(this.calendarView).children("div.dayReal").each(function(){
		if(startSeen){
			$(this).removeClass('selected');
		}
		if(parseInt($(this).text())==start){
			startSeen = true;
		}
	});
}

function clearInterval(){
	$(this.intervalMonthStart).val('');
	$(this.intervalStart).val('');
	$(this.intervalEnd).val('');
	$(this.calendarView).children("div.dayReal").each(function(){
		$(this).removeClass('selected');
	});
}

/*
function initDateSelectionArea(){
	var d = new Date();
	$(this.startDate).text(numMonthtToWordFull(parseInt(d.getMonth())) + ' ' + d.getDate() + ', ' + d.getFullYear());
	$(this.endDate).text(numMonthtToWordFull(parseInt(d.getMonth())) + ' ' + d.getDate() + ', ' + d.getFullYear());
	this.setIntervalStartTime(d.getDate());
	this.setIntervalEndTime(d.getDate());
}
*/

function setStartOfDateSelectionArea(day){
	var dMonth = new Date(parseInt($(this.currentMonthStartInput).val()));
	$(this.startDate).text(numMonthtToWordFull(parseInt(dMonth.getMonth())) + ' ' + day + ', ' + dMonth.getFullYear());
}

function setEndOfDateSelectionArea(day){
	var dMonth = new Date(parseInt($(this.currentMonthStartInput).val()));
	$(this.endDate).text(numMonthtToWordFull(parseInt(dMonth.getMonth())) + ' ' + day + ', ' + dMonth.getFullYear());
}

function restrictDays(){
	this.unrestrictAllDays(); // Unrestrict all days before we start restricting days
	// Restrict all days in the past
	var visibleDate = new Date(parseInt($(this.currentMonthStartInput).val()));
	var currentDate = new Date();
	var startDate = new Date(parseInt($(this.intervalMonthStart).val()));
	var eventStart = $(this.intervalStart).val();
	if((parseInt(visibleDate.getMonth())<
		parseInt(currentDate.getMonth())&&
		parseInt(visibleDate.getFullYear())<=
		parseInt(currentDate.getFullYear()))||(
		parseInt(visibleDate.getFullYear())<
		parseInt(currentDate.getFullYear()))||( // Restrict all days in the past
	   	parseInt(visibleDate.getMonth())>
	   	parseInt(startDate.getMonth())+1&&
	   	parseInt(visibleDate.getFullYear())==
	   	parseInt(startDate.getFullYear()))||(
	   	(visibleDate.getMonth()!=0||
	   	startDate.getMonth()!=11)&&
	   	parseInt(visibleDate.getFullYear())==
	   	parseInt(startDate.getFullYear())+1)||
	   	parseInt(visibleDate.getFullYear())>
	   	parseInt(startDate.getFullYear())+1){ // Restrict all days that are more than a month after the start
		$(this.calendarView).children("div.dayReal").addClass('noDayClick');
	}else if(parseInt(visibleDate.getMonth())==
			 parseInt(currentDate.getMonth())&&
			 parseInt(visibleDate.getFullYear())==
			 parseInt(currentDate.getFullYear())){ // Restrict days this month that are before the current date
		$(this.calendarView).children("div.dayReal").each(function(){
			if(parseInt($(this).text())<parseInt(currentDate.getDate())){
				$(this).addClass('noDayClick');
			}
		});
	}else if((parseInt(visibleDate.getMonth())==
	   		 parseInt(startDate.getMonth())+1&&
	   		 parseInt(visibleDate.getFullYear())==
		     parseInt(startDate.getFullYear()))||
		     (parseInt(visibleDate.getFullYear())==
		   	 parseInt(startDate.getFullYear())+1&&
		   	 startDate.getMonth()==11&&
		   	 visibleDate.getMonth()==0)){
	 	// Restrict days that are after the starting date in the next month (i.e., if it starts on June 5, it must end by July 4).
		$(this.calendarView).children("div.dayReal").each(function(){
			if(parseInt($(this).text())>=parseInt(eventStart)){
				$(this).addClass('noDayClick');
			}
		});
	}
}

function onlyRestrictPastDays(){
	this.unrestrictAllDays(); // Unrestrict all days before we start restricting days
	// Restrict all days in the past
	var visibleDate = new Date(parseInt($(this.currentMonthStartInput).val()));
	var currentDate = new Date();
	var startDate = new Date(parseInt($(this.intervalMonthStart).val()));
	var eventStart = $(this.intervalStart).val();
	if((parseInt(visibleDate.getMonth())<
		parseInt(currentDate.getMonth())&&
		parseInt(visibleDate.getFullYear())<=
		parseInt(currentDate.getFullYear()))||(
		parseInt(visibleDate.getFullYear())<
		parseInt(currentDate.getFullYear()))){ // Restrict all days in the past
		$(this.calendarView).children("div.dayReal").addClass('noDayClick');
	}else if(parseInt(visibleDate.getMonth())==
			 parseInt(currentDate.getMonth())&&
			 parseInt(visibleDate.getFullYear())==
			 parseInt(currentDate.getFullYear())){ // Restrict days this month that are before the current date
		$(this.calendarView).children("div.dayReal").each(function(){
			if(parseInt($(this).text())<parseInt(currentDate.getDate())){
				$(this).addClass('noDayClick');
			}
		});
	}
}

function unrestrictAllDays(){
	$(this.calendarView).children("div.dayReal").each(function(){
		$(this).removeClass('noDayClick');
	});
}