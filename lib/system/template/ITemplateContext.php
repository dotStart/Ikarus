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
 * Class ITemplateContext
 * @author			Johannes Donath
 * @copyright			Copyright (C) 2013 Evil-Co <http://www.evil-co.com>
 * @package			ikarus\system\template
 * @category			Ikarus Framework
 * @license			GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version			2.0.0-0001
 */
interface ITemplateContext {

	/**
	 * @param ITemplate        $template
	 * @param ICompiler        $compiler
	 * @param ITemplateContext $parent
	 * @param bool             $sandboxed
	 */
	public function __construct (ITemplate $template, ICompiler $compiler, ITemplateContext $parent = null, $sandboxed = false);

	/**
	 * Assigns a new variable to this context.
	 * @param string $name
	 * @param mixed  $value
	 * @return void
	 */
	public function assignVariable ($name, $value = null);

	/**
	 * Compiles a template.
	 * @return void
	 * @throws CompilerException
	 */
	public function compile ();

	/**
	 * Creates a child context.
	 * @param ITemplate $template
	 * @param bool      $sandboxed
	 * @return ITemplateContext
	 */
	public function createChildContext (ITemplate $template, $sandboxed = false);

	/**
	 * @param bool $returnContent
	 * @return mixed
	 */
	public function execute ($returnContent = true);

	/**
	 * @param string $name
	 * @param mixed  $defaultValue
	 * @param bool   $disableBubble
	 * @return mixed
	 * @throws ContextException
	 */
	public function getVariable ($name, $defaultValue = null, $disableBubble = false);

	/**
	 * Checks whether a variable exists.
	 * @param      $name
	 * @param bool $disableBubble
	 * @return boolean
	 */
	public function hasVariable ($name, $disableBubble = false);

	/**
	 * Sets a variable's content.
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	public function setVariable ($name, $value);

	/**
	 * @return string
	 */
	public function __invoke ();
}
?>