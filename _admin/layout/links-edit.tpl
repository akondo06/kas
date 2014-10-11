<?php
	$section = $bone->section();
	$current = $section->link($_GET['id']);
?>
<form name="edit_link" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="anchor">Anchor:</label>
		<input type="text" name="anchor" placeholder="Anchor..." value="<?php echo $current->anchor; ?>" />
	</div>
	<div class="inputWrapper">
		<label for="description">Description:</label>
		<input type="text" name="description" placeholder="Description..." value="<?php echo $current->description; ?>" />
	</div>
	<div class="inputWrapper">
		<label for="url">URL:</label>
		<input type="text" name="url" placeholder="http://www.arcade.com/..." value="http://<?php echo $current->url; ?>" />
	</div>

	<div class="inputWrapper halfLeftInput">
		<label for="contact">Contact:</label>
		<input type="text" name="contact" placeholder="E-Mail..." value="<?php echo $current->contact; ?>" />
	</div>	
	<div class="inputWrapper halfRightInput">
		<label for="status">Status:</label>
		<select name="status">
			<option value="1"<?php if($current->status == 1) { echo " selected=\"selected\""; } ?>>Active</option>
			<option value="0"<?php if($current->status == 0) { echo "selected=\"selected\""; } ?>>Inactive</option>
		</select>
	</div>
	<div class="clear"></div>

	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Submit" />
		<div class="clear"></div>
	</div>
</form>