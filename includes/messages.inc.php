<div class="messages">
	<?php if ($message != '') { ?>
	<p class="message"><?php echo $message;?></p>
	<?php } ?>
	<?php if ($warning != '') { ?>
	<p class="warning"><?php echo $warning;?></p>
	<?php } ?>
	<?php if ($error != '') { ?>
	<p class="error"><?php echo $error;?></p>
	<?php } ?>
</div>