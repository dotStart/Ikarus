<!DOCTYPE html>
<html>
	<head>
		<title>Ikarus Framework Error Report Reader</title>
	</head>
	<body>
	<?php
		if (!count($_POST) or !isset($_POST['report'])) {
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<textarea name="report" cols="40" rows="20"></textarea><br />
				<input type="submit" name="submit" value="submit" />
			</form>
			<?php
		} else {
			// get contents
			$content = trim($_REQUEST['report']);
			
			// remove start and end tags
			if (preg_match('~-------- REPORT BEGIN --------~i', $content)) {
				$contentSplit = preg_split('~-------- REPORT (BEGIN|END) --------~i', $content);
				if (!isset($contentSplit[1])) die("Invalid report! Try again!");
				$content = $contentSplit[1];
			}
			
			// replace newlines
			$content = str_replace("\n", '', $content);
			
			error_reporting(0);
			
			// try to decode
			$data = unserialize(base64_decode($content));
			
			if (!is_array($data)) die("Invalid report! Try again!");
			
			echo '<pre>';
			foreach($data as $key => $value) echo htmlentities($key.' => '.var_export($value, true))."\n";
			echo '</pre>';
			
		}
	?>
	</body>
</html>