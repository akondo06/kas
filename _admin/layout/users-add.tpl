<?php
$section = $bone->section();

$default = array("email" => "", "password" => "", "nickname" => "", "access" => 0);
$default = (object) $default;

if($_POST) {
	$default = (object) $_POST;
}

?>
<form name="add_user" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="email">E-Mail:</label>
		<input type="text" name="email" placeholder="E-Mail..." <?php if($default->email) { echo " value=\"".$default->email."\""; } ?> />
	</div>
	<div class="inputWrapper halfLeftInput">
		<label for="email">Password:</label>
		<input type="password" name="password" placeholder="Password..." <?php if($default->password) { echo " value=\"".$default->password."\""; } ?> />
	</div>
	<div class="inputWrapper halfRightInput">
		<label for="email">Verify Password:</label>
		<input type="password" name="passwordverify" placeholder="Verify Password..." />
	</div>

	<div class="inputWrapper halfLeftInput">
		<label for="email">Nickname:</label>
		<input type="text" name="nickname" placeholder="Nickname..." <?php if($default->nickname) { echo " value=\"".$default->nickname."\""; } ?> />
	</div>
	<div class="inputWrapper halfRightInput">
		<label for="access">Access:</label>
		<select name="access">
			<?php
				foreach ($section->levels() as $id => $level) {
					echo "<option value=\"".$id."\"";
					if($default->access == $id) { echo " selected=\"selected\""; }
					echo ">".$level->name."</option>";
				}
			?>
		</select>
	</div>
	<div class="clear"></div>

	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Submit" />
		<div class="clear"></div>
	</div>
</form>