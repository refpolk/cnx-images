$(function() {
		
	$(".zoom").click(function (e) {
	
	    var dialogId = $(this).data('dialog-id');
		
	    $(dialogId).modal('show');
			
		return false;
	});
		
	$("input[name='q']").focus();
	
});