<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="stylesheet" type="text/css" href="layout/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,300" />
		<link rel="stylesheet" type="text/css" href="layout/css/common.css" />
		<title>KAS Login</title>
	</head>
	<body>
		<form action="index.php" method="post" class="loginform">
			<div class="logo"><img src="layout/img/logo2.png" alt="" /></div>
			<?php
				$message = $bone->message();
				if(is_array($message) && array_key_exists('type', $message) && array_key_exists('message', $message)) {
					echo "<div class=\"message ".$message['type']."\">".$message['message']."</div>";
				}
			?>
			<input type="text" name="email" placeholder="email..." />
			<input type="password" name="password" placeholder="password..." />
			<input type="submit" name="submit" value="Log in" />
			<div class="clear"></div>
		</form>
	</body>
</html>