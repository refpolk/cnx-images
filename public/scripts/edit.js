$(function() {

	$("#file").change(function () {
	
	    var reader = new FileReader();

	    reader.onload = function (e) {
	        $("#thumbnail img").attr("src", e.target.result);
			$("#zoom-dialog img").attr("src", e.target.result);
			$("#thumbnail").show();
	    };

	    reader.readAsDataURL(this.files[0]);
		$("#filename").val(this.files[0].name);
	});
	
	$("#delete-dialog").dialog({
	    autoOpen: false,
        resizable: false,
        width:'auto',
        modal: true,
        buttons: {
          "Remove": function() {

		    $("#thumbnail img").attr("src", "");
			$("#zoom-dialog img").attr("src", "");			
			$("#thumbnail").hide();
			$("#filename").val("");
			$("#file").val("");
			
			$(this).dialog("close");
          },
          Cancel: function() {
            $(this).dialog("close");
          }
        }
	});

	$("#delete").click(function () {
		
		$("#delete-dialog").dialog("open");	
		return false;
	});
	
	$("#zoom-dialog").dialog({
	    autoOpen: false,
        resizable: false,
        width:'auto'
	});
	
	$(".zoom").click(function (e) {
		
	    $("#zoom-dialog").dialog("open");
		return false;
	});
	
	if ($("#thumbnail img").attr("src") === "") {
		
		$("#thumbnail").hide();
	}
	
	// Edit / Readonly mode
	
	$('button[name="edit"], button[name="cancel"]').hide();

	if ($('body').attr('data-mode') === 'Add') {
	
		$("input[name='title']").focus();
	}
				
	if ($('body').attr('data-mode') === 'Edit') {

		$('.edit-only').hide();

		$('input[type="text"],input[type="radio"],textarea')
			.attr('disabled', 'disabled')
			.attr('style', 'background-color:#f2f2f2;');

		$('button[name="edit"]').show();
	}
	
	$('button[name="edit"]').click(function() {

		$('.edit-only').show();

		$('input[type="text"],input[type="radio"],textarea')
			.attr('disabled', false)
			.attr('style', 'background-color:#fff;');

		$('button[name="edit"]').hide();
		$('button[name="cancel"]').show();
		
		$("input[name='title']").focus();		
		
		return false;
	});
	
	$('button[name="cancel"]').click(function() {
		
		window.location.assign(window.location)
		
		return false;
	});
});