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

/* ---- UNIVERSAL SELECTOR ---- */
function initSelectors(){
	// Note: see global.js for the event handlers (for clicking on a selector, etc.)
	$("div.selector").each(function(){
		var widestDiv = 0;
		$(this).children("div.innerSelector").children("div").each(function(){
			if(parseInt($(this).width())>widestDiv){
				widestDiv = parseInt($(this).width());
			}
		});
		//$(this).width(widestDiv+2); // Set the parent's width
		var numOptions = $(this).children("div.innerSelector").children("div").length;
		$(this).children("div.innerSelector").children("div").each(function(i){
			//$(this).width(widestDiv); // Set each child's width
			if(i==0){ // First one (default selection)
				if($(this).siblings("div.selected").length<1){
					$(this).addClass('selected');
				}
			}
			if(i+1==numOptions){ // Last one
				$(this).css('border-bottom', '0px white solid'); // To remove border from the bottom of the last div
			}else{
				$(this).css('border-bottom', '1px gray solid'); // Ensure that there is indeed a border at the bottom otherwise
			}
		});
		// Add a decoy div
		if($(this).children("div.decoySelection").length<1){ // Only create it if it doesn't currently exist
			$(this).prepend("<div class='decoySelection'>" + $(this).children("div.innerSelector").children("div:first").html() + "</div>");
			$(this).children("div.decoySelection").width(widestDiv); // Set the decoy's width
		}
		$(this).children("div.innerSelector").hide();
		// Add a hidden input to record the value of the selector
		if($(this).children("input").length<1){ // Only create it if it doesn't currently exist
			$(this).prepend("<input type='hidden' name='" + $(this).attr("id") + "' value='" + $(this).children("div.innerSelector").children("div:first").html() + "' />");
		}
	});
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

// For checking if a string is a valid geographical coordinate
function isValidCoord(coord){
	var val = parseFloat(coord);
	if (val != NaN && val <= 90 && val >= -90)
		return true;
	else
		return false;
}

function initTextareaAutoResize(){
	autosize($("textarea"));
}

// For turning a text div into an editable field
function makeEditable(elem,useTextarea,isLocation){
	if($(elem).children("input.editType").length>0){
		var editType = $(elem).children("input.editType").val();
		$(elem).children("input.editType").remove();
		var hugId = $(elem).children("input.hugId").val();
		
		// For location if it relevant
		if(isLocation){
			var lat = $(elem).children("input#lat").val();
			var long = $(elem).children("input#long").val();
			useTextarea = false; // Since we NEVER want a textarea if we are dealing with an address
		}
		
		$(elem).children("input.hugId").remove();
		$(elem).children("div.editButton").remove(); // Remove the currently open button
		var text = $(elem).text().trim();
		if(useTextarea){
			$(elem).html('<div class="editButton">Done</div><textarea name="field">' + text + '</textarea><input type="hidden" name="editType" class="editType" value="' + editType + '" /><input type="hidden" name="hugId" class="hugId" value="' + hugId + '" />');
			initTextareaAutoResize();
		}else{
			$(elem).html('<div class="editButton">Done</div><input type="text" name="field" class="textBox" value="' + text + '" /><input type="hidden" name="editType" class="editType" value="' + editType + '" /><input type="hidden" name="hugId" class="hugId" value="' + hugId + '" />');
		}
		if(isLocation){ // need to add the lat and long inputs on the end of $(elem)
			$(elem).prepend('<div id="notAddress" class="validAddress"></div><input type="hidden" id="lat" name="lat" value="' + lat + '" /><input type="hidden" id="long" name="long" value="' + long + '" />');
		}
		$(elem).children("textarea, input.textBox").select();
		// Work around Chrome's little problem
		$(elem).children("textarea, input.textBox").mouseup(function() {
		    // Prevent further mouseup intervention
		    $(this).unbind("mouseup");
		    return false;
		});
	}
}

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

$(function(){
	// EXAMPLE OF AJAX CALL:
	/*$(document).on('click', 'html body div.editableText div.editButton', function(e){
		e.preventDefault();
		if($(this).siblings("textarea, input.textBox").length>0){
			$elem = $(this).siblings("textarea, input.textBox");
			$parent = $(this).parent('div.editableText');
			var hugId = $(this).siblings("input.hugId").val();
			var editType = $(this).siblings("input.editType").val();
			$.post('/a.php?p=Global_TextUpdater', {field: $elem.val(), editType: editType, hugId: hugId}, function(d){
				if(getAjaxStatus(d)){
					$parent.html($elem.val() + '<input type="hidden" name="editType" class="editType" value="' + editType + '" /><input type="hidden" name="hugId" class="hugId" value="' + hugId + '" />');
				}else{
					alertBar(getAjaxFailureReason(d));
					setTimeout(function(){
						closeAlertBar();
					}, 10000);
				}
			});
		}
		return false;
	});*/
});