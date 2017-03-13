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

});