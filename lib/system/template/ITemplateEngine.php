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
namespace ikarus\system\template;

/**
 * Defines default procedures for template engines.
 * @author			Johannes Donath
 * @copyright			Copyright (C) 2013 Evil-Co <http://www.evil-co.com>
 * @package			ikarus\system\template
 * @category			Ikarus Framework
 * @license			GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version			2.0.0-0001
 */
interface ITemplateEngine {

	/**
	 * Assigns one or more variables to the global template context.
	 * @param      $name
	 * @param null $value
	 * @return mixed
	 */
	public function assignVariables ($name, $value = null);

	/**
	 * Creates a new template context.
	 * @param ITemplate         $template
	 * @param ITemplateContext $parent
	 * @param bool             $sandboxed
	 * @return ITemplateContext
	 */
	public function createTemplateContext (ITemplate $template, ITemplateContext $parent = null, $sandboxed = false);

	/**
	 * Creates a template object.
	 * @param $templateName
	 * @return ITemplate
	 */
	public function createTemplateObject ($templateName);

	/**
	 * Displays a specific template.
	 * @param $templateName
	 * @return void
	 */
	public function display ($templateName);
}
?> 