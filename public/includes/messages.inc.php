<div class="messages">
	<?php if ($message != '') { ?>
	<div class="alert alert-success" role="alert">
	<p class="message"><?php echo $message;?></p>
	</div>
	<?php } ?>
	<?php if ($warning != '') { ?>
	<div class="alert alert-warning" role="alert">
	<p class="warning"><?php echo $warning;?></p>
	</div>
	<?php } ?>
	<?php if ($error != '') { ?>
	<div class="alert alert-error" role="alert">	
	<p class="error"><?php echo $error;?></p>
	</div>
	<?php } ?>
</div>