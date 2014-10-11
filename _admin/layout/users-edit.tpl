<?php
	$section = $bone->section();
	$current = $section->user($_GET['id']);
?>
<form name="edit_user" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="inputWrapper">
		<label for="email">E-Mail:</label>
		<input type="text" name="email" placeholder="E-Mail..." value="<?php echo $current->email; ?>" />
	</div>
	<div class="inputWrapper halfLeftInput">
		<label for="email">Password:</label>
		<input type="password" name="password" placeholder="Password..." />
	</div>
	<div class="inputWrapper halfRightInput">
		<label for="email">Verify Password:</label>
		<input type="password" name="passwordverify" placeholder="Verify Password..." />
	</div>

	<div class="inputWrapper halfLeftInput">
		<label for="email">Nickname:</label>
		<input type="text" name="nickname" placeholder="Nickname..." value="<?php echo $current->nickname; ?>" />
	</div>
	<div class="inputWrapper halfRightInput">
		<label for="access">Access:</label>
		<select name="access">
			<?php
				foreach ($section->levels() as $id => $level) {
					echo "<option value=\"".$id."\"";
					if($current->access == $id) { echo " selected=\"selected\""; }
					echo ">".$level->name."</option>";
				}
			?>
		</select>
	</div>
	<div class="clear"></div>

	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Save" />
		<div class="clear"></div>
	</div>
</form>