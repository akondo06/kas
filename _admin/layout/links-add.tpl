<?php
$section = $bone->section();

$default = array("anchor" => "", "description" => "", "url" => "", "contact" => "", "status" => 0);
$default = (object) $default;

if($_POST) {
	$default = (object) $_POST;
}

?>
<form name="add_link" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="anchor">Anchor:</label>
		<input type="text" name="anchor" placeholder="Anchor..." <?php if($default->anchor) { echo " value=\"".$default->anchor."\""; } ?> />
	</div>
	<div class="inputWrapper">
		<label for="description">Description:</label>
		<input type="text" name="description" placeholder="Description..." <?php if($default->description) { echo " value=\"".$default->description."\""; } ?> />
	</div>
	<div class="inputWrapper">
		<label for="url">URL:</label>
		<input type="text" name="url" placeholder="http://www.arcade.com/..." <?php if($default->url) { echo " value=\"".$default->url."\""; } ?> />
	</div>

	<div class="inputWrapper halfLeftInput">
		<label for="contact">Contact:</label>
		<input type="text" name="contact" placeholder="E-Mail..." <?php if($default->contact) { echo " value=\"".$default->contact."\""; } ?> />
	</div>	
	<div class="inputWrapper halfRightInput">
		<label for="status">Status:</label>
		<select name="status">
			<option value="1"<?php if($default->status == 1) { echo " selected=\"selected\""; } ?>>Active</option>
			<option value="0"<?php if($default->status == 0) { echo "selected=\"selected\""; } ?>>Inactive</option>
		</select>
	</div>
	<div class="clear"></div>

	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Submit" />
		<div class="clear"></div>
	</div>
</form>