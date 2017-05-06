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
	
	$(".zoom").click(function (e) {
		
	    $('#zoom-dialog').modal('show');
		return false;
	});
	
	$(".remove").click(function (e) {

    	$('#remove-dialog')
        	.modal({ backdrop: 'static', keyboard: false })
        	.one('click', '#remove', function (e) {
				$("#thumbnail img").attr("src", "");
				$("#zoom-dialog img").attr("src", "");			
				$("#thumbnail").hide();
				$("#filename").val("");
				$("#file").val("");
        	});

		return false;
	});
		
	if ($("#thumbnail img").attr("src") === "") {
		
		$("#thumbnail").hide();
	}
	
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