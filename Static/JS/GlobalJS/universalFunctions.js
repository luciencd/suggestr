$(function(){
	jQuery.fn.animateAuto = function(prop, speed, callback){
	    var elem, height, width;
	    return this.each(function(i, el){
	        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo(el.parent());
	        height = elem.css("height"),
	        width = elem.css("width"),
	        elem.remove();
	        
	        if(prop === "height")
	            el.animate({"height":height}, speed, callback);
	        else if(prop === "width")
	            el.animate({"width":width}, speed, callback);  
	        else if(prop === "both")
	            el.animate({"width":width,"height":height}, speed, callback);
	    });  
	}

	jQuery.fn.outerHTML = function(s) {
	    return s
	        ? this.before(s).remove()
	        : jQuery("<p>").append(this.eq(0).clone()).html();
	};
});

function getQueryVariable(variable){
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for(var i=0;i<vars.length;i++){
    	var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
	}
	return(false);
}

function loadStyleSheet(href){
	if(!$("link[href='" + href + "']").length){ // Check if stylesheet was already loaded
		$('<link href="' + href + '" rel="stylesheet">').appendTo("head"); // Add stylesheet
	}
}

/* ---- UNIVERSAL TEXT INPUT (placeholder) ---- */
function initInputPlaceholders(){
	// Note: see global.js for the event handlers
	$("input").each(function(){
		if($(this).val()==''&&($(this).attr("type")=='text'||$(this).attr("type")=='password')&&typeof $(this).attr("placeholder")!==typeof undefined&&$(this).attr("placeholder")!==false){
			$(this).val($(this).attr("placeholder"));
			$(this).data('placeholder', $(this).attr("placeholder"));
			$(this).removeAttr('placeholder');
		}
	});
	$("textarea").each(function(){
		if($(this).val()==''&&typeof $(this).attr("placeholder")!==typeof undefined&&$(this).attr("placeholder")!==false){
			$(this).val($(this).attr("placeholder"));
			$(this).data('placeholder', $(this).attr("placeholder"));
			$(this).removeAttr('placeholder');
		}
	});
}

// For setting the cursor position
$.fn.selectRange = function(start, end) {
    if(!end) end = start; 
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

// For requiring js files while avoiding duplication
function requireJS(path){
	var dataName = path.replace(/\W/g, '');
	if(!$.hasData(document.body)||($.hasData(document.body)&&$.data(document.body, dataName)!=1)){
		$.data(document.body, dataName, 1);
		$("html head").append('<script type="text/javascript" src="' + path + '"></script>');
	}
}

// Get Success Status from Ajax JSON String
function getAjaxStatus(json){
	var obj = $.parseJSON(json);
	return obj.success;
}

// Get Failure Reason from Ajax JSON String
function getAjaxFailureReason(json){
	if(getAjaxStatus(json)==true){
		return false;
	}else{
		var obj = $.parseJSON(json);
		return obj.reason;
	}
}

// Get Data from Ajax JSON String
function getAjaxData(json){
	if(getAjaxStatus(json)==false){
		return false;
	}else{
		var obj = $.parseJSON(json);
		return obj.data;
	}
}