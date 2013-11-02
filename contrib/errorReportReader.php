<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Ikarus Framework Error Report Reader</title>
</head>
<body>
<?php
if (!count ($_POST) or !isset($_POST['report'])) {
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<textarea name="report" cols="40" rows="20"></textarea><br/>
		<input type="submit" name="submit" value="submit"/>
	</form>
<?php
} else {
	// get contents
	$content = trim ($_REQUEST['report']);

	// check for encrypted reports
	if (preg_match ('~-------- ENCRYPTED REPORT BEGIN --------~i', $content)) die("Encrypted reports are currently not supported!");

	// remove start and end tags
	if (preg_match ('~-------- REPORT BEGIN --------~i', $content)) {
		$contentSplit = preg_split ('~-------- REPORT (BEGIN|END) --------~i', $content);
		if (!isset($contentSplit[1])) die("Invalid report! Try again!");
		$content = $contentSplit[1];
	}

	// replace newlines
	$content = str_replace ("\n", '', $content);

	error_reporting (0);

	// try to decode
	$data = unserialize (base64_decode ($content));

	if (!is_array ($data)) die("Invalid report! Try again!");

	echo '<pre>';
	foreach ($data as $key => $value) echo htmlentities ($key . ' => ' . var_export ($value, true)) . "\n";
	echo '</pre>';

}
?>
</body>
</html>