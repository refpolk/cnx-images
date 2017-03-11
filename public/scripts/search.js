$(function() {
	
	$(".zoom").click(function (e) {
	
  		$(e.target).parent('.zoom').next('.dialog').dialog({
        	resizable: false,
        	width:'auto'
  		});
	
		return false;
	});
		
	$("input[name='q']").focus();
	
	$(".dialog").hide();
	
});