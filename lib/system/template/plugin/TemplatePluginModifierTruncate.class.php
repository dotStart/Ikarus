<?php
// cp imports
require_once(IKARUS_DIR.'lib/system/template/TemplatePluginModifier.class.php');
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

/**
 * The 'truncate' modifier truncates a string.
 *
 * Usage:
 * {$foo|truncate:35:'...'}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginModifierTruncate implements TemplatePluginModifier {
	/**
	 * @see TemplatePluginModifier::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {
		// default values
		$length = 80;
		$etc = '...';
		$breakWords = false;

		// get values
		$string = $tagArgs[0];
		if (isset($tagArgs[1])) $length = intval($tagArgs[1]);
		if (isset($tagArgs[2])) $etc = $tagArgs[2];
		if (isset($tagArgs[3])) $breakWords = $tagArgs[3];

		// execute plugin
		if ($length == 0) {
			return '';
		}

		if (StringUtil::length($string) > $length) {
			$length -= StringUtil::length($etc);

			if (!$breakWords) {
				$string = preg_replace('/\s+?(\S+)?$/', '', StringUtil::substring($string, 0, $length + 1));
			}

			return StringUtil::substring($string, 0, $length).$etc;
		}
		else {
   			return $string;
		}
	}
}
?>