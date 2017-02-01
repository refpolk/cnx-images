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

	$("#delete").click(function () {
	
	    $("#thumbnail img").attr("src", "");
		$("#thumbnail").hide();
		$("#filename").val("");
		$("#file").val("")
	
		return false;
	});
	
	$(".zoom").click(function () {
	
  		$("#dialog").dialog({
        	resizable: false,
        	width:'auto'
  		});
	
		return false;
	});

	$("input[name='title']").focus();
	
	$("#dialog").hide();
	
	if ($("#thumbnail img").attr("src") == "") {
		$("#thumbnail").hide();
	}

});