# Ikarus Framework Code Guidelines Version 1.0
These are the guidelines for each file of Ikarus itself. To get your modification merged you have to cooperate with these guidelines. Additionally you could adapt these guidelines for your extensions.

## File-Header
Each file must contain the copyright notice at the top of the file. As example:
```php
<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\util;
```

## Braces
All `{ }` braces have to be on the same line as your statement. As example:
```php
<?php
// ...
switch ($answerToLifeTheUniverseAndEverything) {
	case 42:
		doThis();
		break;
	default:
		echo 'You're a liar!';
		exit;
		break;
}
// ...
```
Additionally you should forget them if you're just executing simple tasks. As example:
```php
<?php
// ...
if ($answerToLifeTheUniverseAndEverything == 42) doThis();
// ...
```
But you have to use newlines if there's an else statement or something like that:
```php
// ...
if ($answerToLifeTheUniverseAndEverything == 42)
	doThis();
else {
	echo 'You're a liar!';
	exit;
}
// ...
if ($answerToLifeTheUniverseAndEverything == 42)
	doThis();
else
	doThat();
// ...
```

## Why the fuck do you use `break;` after `exit;` or `return xyz`?
Well, we just want to write complete code. In this case we please you to ignore the fact that writing `break;` after a return or exit statement is totally senseless.
As example we would use the following code:
```php
<?php
// ...
switch($answerToLifeTheUniverseAndEverything) {
	case 21:
		echo 'This is just the half answer'
		exit;
		break;
	case 42:
		// yada yada yada
		break;
}
// ...
```

## Defining empty methods / classes
Please don't use newlines there. As example:
```php
<?php
// ...
class Senseless extends NormalClass { }
// ...
```

## String-Literals
Please don't use doublequotes (`"`) as long as you don't need them (You'll need them for newlines and other escape sequences). Additionally you'll have to use the following syntax to add variables to your string:
```php
<?php
// ...
$string1 = 'Blah';
$string2 = 'Wah wah wah ... '.$string1.' ... wah wah wah';
// ...
```
* * *
Currently this is all. Please contact us or just change it if you find some crazy code guideline which is not listed here.