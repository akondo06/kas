<?php
$default = array("location" => "", "status" => 1, "content" => "");
$default = (object) $default;

if($_POST) {
	$default = (object) $_POST;
}

?>
<form name="add_ad" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="location">Location:</label>
		<input type="text" name="location" placeholder="Location..." <?php if($default->location) { echo " value=\"".$default->location."\""; } ?> />
	</div>
	<div class="inputWrapper">
		<label for="status">Status:</label>
		<select name="status">
			<option value="1"<?php if($default->status == 1) { echo " selected=\"selected\""; } ?>>Active</option>
			<option value="0"<?php if($default->status == 0) { echo "selected=\"selected\""; } ?>>Inactive</option>
		</select>
	</div>
	<div class="inputWrapper">
		<label for="content">Content:</label>
		<textarea name="content" cols="30" rows="10"><?php if($default->content) { echo $default->content; } ?></textarea>
	</div>
	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Submit" />
		<div class="clear"></div>
	</div>
</form>