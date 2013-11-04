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
namespace ikarus\system\style;

use ikarus\data\DatabaseObject;
use ikarus\system\event\style\CssCodeBuildEvent;
use ikarus\system\event\style\CssCodeBuiltEvent;
use ikarus\system\event\style\CssEventArguments;
use ikarus\system\event\style\StyleEventArguments;
use ikarus\system\Ikarus;
use ikarus\util\StringUtil;

/**
 * Represents a style
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 * @todo                      Add methods
 */
class Style extends DatabaseObject {

	/**
	 * Contains a list of all css definitions in this style
	 * @var                        ikarus\system\database\DatabaseResultList
	 */
	protected $cssDefinitions = array ();

	/**
	 * @see ikarus\data\DatabaseObject::$identifierField
	 */
	protected static $identifierField = 'styleID';

	/**
	 * @see ikarus\data\DatabaseObject::$tableName
	 */
	protected static $tableName = 'ikarus1_style';

	/**
	 * Builds a minified version of style's css code
	 * @return                        string
	 * @api
	 */
	public function buildCssCode () {
		$mediaQueries = array ();
		$css = '';

		// fire event
		$event = new CssCodeBuildEvent(new StyleEventArguments($this));
		Ikarus::getEventManager ()->fire ($event);

		// cancellable event
		if ($event->isCancelled ()) return $event->getReplacement ();

		// raw processing
		foreach ($this->cssDefinitions as $definition) {
			// stop if css definition is disabled
			if (!$definition->isEnabled) continue;

			// save to media query
			if (!isset($mediaQueries[$definition->cssMediaQuery])) $mediaQueries[$definition->cssMediaQuery] = array ();
			$mediaQueries[$definition->cssMediaQuery][] = $definition;
		}

		// process media queries
		foreach ($mediaQueries as $mediaQuery => $definitions) {
			// add media query
			$css .= '@media ' . $mediaQuery . "{\n\t";

			// loop through definitions
			foreach ($definitions as $definition) {
				// add comment
				$css .= '/* ' . $definition->definitionComment . " */\n\t";

				// add selector
				$css .= $definition->cssSelector . '{';

				// add content (without newlines)
				$css .= StringUtil::replace ("\n", '', $definition->cssCode);

				// add ending brace
				$css .= "}\n\t";
			}

			// add close brace
			$css .= '}';
		}

		// fire event
		Ikarus::getEventManager ()->fire (new CssCodeBuiltEvent(new CssEventArguments($this, $css)));

		// return built css
		return $css;
	}

	/**
	 * Loads the css definition cache
	 * @return                        void
	 * @internal                        This method gets called by it's parent style manager.
	 */
	public function loadCache () {
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ('style-' . $this->styleID, 'style-' . $this->styleID, 'ikarus\\system\\cache\\builder\\CacheBuilderStyle');
		$this->cssDefinitions = Ikarus::getCacheManager ()->getDefaultAdapter ()->get ('style-' . $this->styleID);
	}
}

?>