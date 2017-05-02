<script src="scripts/jquery.min.js"></script>
<script src="scripts/jquery-ui.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script type="text/javascript">
$(document).ready(function () {
  	$(".nav li").removeClass("active");
	$('a[href="' + this.location.pathname.replace('/', '') + '"]').parent().addClass('active');
	$('.navbar-right a[href$="' + this.location.pathname.split('-')[1] + '"]').parent().addClass('active')
});
</script>