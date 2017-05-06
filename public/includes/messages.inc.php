<div class="messages">
	<?php if ($message != '') { ?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<p><?php echo $message;?></p>
	</div>
	<?php } ?>
	<?php if ($warning != '') { ?>
	<div class="alert alert-warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<p><?php echo $warning;?></p>
	</div>
	<?php } ?>
	<?php if ($error != '') { ?>
	<div class="alert alert-danger alert-dismissible" role="alert">	
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<p><?php echo $error;?></p>
	</div>
	<?php } ?>
</div>