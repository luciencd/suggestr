<?php

function numMonthtToWord($num){
	switch($num){
		case 0:
			return 'Jan';
		case 1:
			return 'Feb';
		case 2:
			return 'Mar';
		case 3:
			return 'Apr';
		case 4:
			return 'May';
		case 5:
			return 'Jun';
		case 6:
			return 'Jul';
		case 7:
			return 'Aug';
		case 8:
			return 'Sep';
		case 9:
			return 'Oct';
		case 10:
			return 'Nov';
		case 11:
			return 'Dec';
	}
}

function wordMonthtToNum($num){
	$num = ucfirst(strtolower($num));
	switch($num){
		case 'Jan':
			return 0;
		case 'Feb':
			return 1;
		case 'Mar':
			return 2;
		case 'Apr':
			return 3;
		case 'May':
			return 4;
		case 'Jun':
			return 5;
		case 'Jul':
			return 6;
		case 'Aug':
			return 7;
		case 'Sep':
			return 8;
		case 'Oct':
			return 9;
		case 'Nov':
			return 10;
		case 'Dec':
			return 11;
	}
}

function numMonthtToFullWord($num){
	switch($num){
		case 0:
			return 'January';
		case 1:
			return 'February';
		case 2:
			return 'March';
		case 3:
			return 'April';
		case 4:
			return 'May';
		case 5:
			return 'June';
		case 6:
			return 'July';
		case 7:
			return 'August';
		case 8:
			return 'September';
		case 9:
			return 'October';
		case 10:
			return 'November';
		case 11:
			return 'December';
	}
}

function fullWordMonthtToNum($num){
	$num = ucfirst(strtolower($num));
	switch($num){
		case 'January':
			return 0;
		case 'February':
			return 1;
		case 'March':
			return 2;
		case 'April':
			return 3;
		case 'May':
			return 4;
		case 'June':
			return 5;
		case 'July':
			return 6;
		case 'August':
			return 7;
		case 'September':
			return 8;
		case 'October':
			return 9;
		case 'November':
			return 10;
		case 'December':
			return 11;
	}
}

function numWeekToWord($num){
	switch($num){
		case 0:
			return 'Mon';
		case 1:
			return 'Tue';
		case 2:
			return 'Wed';
		case 3:
			return 'Thu';
		case 4:
			return 'Fri';
		case 5:
			return 'Sat';
		case 6:
			return 'Sun';
	}
}

function numWeekToFullWord($num){
	switch($num){
		case 0:
			return 'Monday';
		case 1:
			return 'Tuesday';
		case 2:
			return 'Wednesday';
		case 3:
			return 'Thursday';
		case 4:
			return 'Friday';
		case 5:
			return 'Saturday';
		case 6:
			return 'Sunday';
	}
}
function fullWordToNumWeek($num){
	$num = ucfirst(strtolower($num));
	switch($num){
		case 'Monday':
			return 1;
		case 'Tuesday':
			return 2;
		case 'Wednesday':
			return 3;
		case 'Thursday':
			return 4;
		case 'Friday':
			return 5;
		case 'Saturday':
			return 6;
		case 'Sunday':
			return 0;
	}
}

function numDaysInMonth($month, $year){
	switch($month){
		case 0:
		case 2:
		case 4:
		case 6:
		case 7:
		case 9:
		case 11:
			return 31;
		case 3:
		case 5:
		case 8:
		case 10:
			return 30;
		case 1:
			if($year%4==0&&($year%100!=0||$year%400==0)){
				return 29; // Leap year...
			}else{
				return 28;
			}
	}
}

function ordinalToNum($ord){
	$ord = ucfirst(strtolower($ord));
	switch($ord){
		case '1st':
			return 1;
		case '2nd':
			return 2;
		case '3rd':
			return 3;
		case '4th':
			return 4;
		case '5th':
			return 5;
	}
}

function numToOrdinal($ord){
	switch($ord){
		case 1:
			return '1st';
		case 2:
			return '2nd';
		case 3:
			return '3rd';
		case 4:
			return '4th';
		case 5:
			return '5th';
	}
}

function isMonthYearStringValid($str){
	$arr = explode(' ', $str);
	if(count($arr)!=2)
		return false;
	if(!is_numeric($arr[0])||strlen($arr[0])!=4)
		return false;
	if(!in_array($arr[1], array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')))
		return false;
	return true;
}

function getYearFromString($str){
	// ALWAYS run isMonthYearStringValid() on the data first
	$arr = explode(' ', $str);
	return $arr[0];
}

function getMonthNumFromString($str){
	// ALWAYS run isMonthYearStringValid() on the data first
	$arr = explode(' ', $str);
	return wordMonthtToNum($arr[1]);
}

function isDayStringValid($str, $month, $year){
	$arr = explode(', ', $str);
	if(count($arr)!=2)
		return false;
	if(!is_numeric($arr[0]))
		return false;
	if($arr[0]<=0||$arr[0]>numDaysInMonth($month, $year))
		return false;
	return true;
}

function getDayNumFromString($str){
	// ALWAYS run isDayStringValid() on the data first
	$arr = explode(', ', $str);
	return $arr[0];
}

function secondsSinceMidnight($hour,$minute,$amPm){
	if($hour==12&&strtolower($amPm)=='am')
		$hour = 0;
	if(strtolower($amPm)=='pm')
		$hour += 12;
	if(!is_numeric($hour)||$hour<0||$hour>=24||!is_numeric($minute)||$minute<0||$minute>=60||!in_array(strtolower($amPm), array('am','pm')))
		return false;
	$total = $hour*3600;
	$total += $minute*60;
	return $total;
}

function secondsSinceStart($startHour,$startMinute,$startAmPm,$endHour,$endMinute,$endAmPm){
	// This function assumes that the start and end times cannot be greater than to 24 hours apart 
	$start = secondsSinceMidnight($startHour,$startMinute,$startAmPm);
	$end = secondsSinceMidnight($endHour,$endMinute,$endAmPm);
	if($start==false||$end==false){
		return false;
	}else if($end>$start){
		return $end-$start;
	}else if($end<$start){
		return $end+(86400-$start);
	}else{ // equal (assumes exactly 24 hours in length instead of zero seconds)
		return 86400;
	}
}

?>