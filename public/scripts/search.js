$(function() {
	
	$(".dialog").dialog({
	    autoOpen: false,
        resizable: false,
        width:'auto'
	});
	
	$(".zoom").click(function (e) {
	
	    var dialogId = $(this).data('dialog-id');
		
	    $(dialogId).dialog("open");
	
		return false;
	});
		
	$("input[name='q']").focus();
	
});