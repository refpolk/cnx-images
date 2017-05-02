$(function() {

	$("#file").change(function () {
	
	    var reader = new FileReader();

	    reader.onload = function (e) {
	        $("#thumbnail img").attr("src", e.target.result);
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
          "Delete": function() {

		    $("#thumbnail img").attr("src", "");
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

	$("input[name='title']").focus();
	
	if ($("#thumbnail img").attr("src") == "") {
		
		$("#thumbnail").hide();
	}
	
	/*
	$('input, textarea').attr('disabled', true);
	$('input, textarea').attr('style', 'background-color:#ddd;');
	$('.edit-only').hide();
	*/

		$('.edit-only').hide();

		$('input[type="text"],input[type="radio"],textarea')
			.attr('disabled', 'disabled')
			.attr('style', 'background-color:#ddd;');
			
		//$('input[type="text"],input[type="radio"],textarea').attr('style', 'background-color:#ddd;');

		$('input[name="edit"]').show();
	}
	
	$('input[name="edit"]').click(function() {

		$('.edit-only').show();

		$('input[type="text"],input[type="radio"],textarea')
			.attr('disabled', false)
			.attr('style', 'background-color:#fff;');
		
		//$('input[type="text"],input[type="radio"],textarea').attr('style', 'background-color:#fff;');

		$('input[name="edit"]').hide();
		$('input[name="cancel"]').show();		
		
		return false;
	});
	
	$('input[name="cancel"]').click(function() {
		
		//window.location.reload();
		window.location.assign(window.location)
		
		return false;
	});
});