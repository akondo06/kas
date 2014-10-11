<?php
	$section = $bone->section();
	$current = $section->page($_GET['id']);
?>
<form name="edit_page" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="title">Title:</label>
		<input type="text" name="title" placeholder="Title..." value="<?php echo $current->title; ?>" />
	</div>
	<div class="inputWrapper">
		<label for="slug">Slug:</label>
		<input type="text" name="slug" placeholder="Slug..." value="<?php echo $current->slug; ?>" />
	</div>
	<div class="inputWrapper">
		<label for="status">Status:</label>
		<select name="status">
			<option value="1"<?php if($current->status == 1) { echo " selected=\"selected\""; } ?>>Active</option>
			<option value="0"<?php if($current->status == 0) { echo "selected=\"selected\""; } ?>>Disabled</option>
		</select>
	</div>
	<div class="inputWrapper big">
		<label for="content">Content:</label>
		<textarea name="content" cols="30" rows="10"><?php echo $current->content; ?></textarea>
	</div>
	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Save" />
		<div class="clear"></div>
	</div>
</form>