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
namespace ikarus\system\template\basic;
use ikarus\system\exception\request\NotImplementedException;
use ikarus\system\template\CompilerException;
use ikarus\system\template\ContextException;
use ikarus\system\template\ICompiler;
use ikarus\system\template\IGlobalContext;
use ikarus\system\template\ITemplate;
use ikarus\system\template\ITemplateContext;

/**
 * Class BasicGlobalContext
 * @author			Johannes Donath
 * @copyright			Copyright (C) 2013 Evil-Co <http://www.evil-co.com>
 * @package			ikarus\system\template\basic
 * @category			Ikarus Framework
 * @license			GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version			2.0.0-0001
 */
abstract class BasicGlobalContext implements IGlobalContext {

	/**
	 * Stores all context variables.
	 * @var array
	 */
	protected $variables = array ();

	/**
	 * @param ITemplate        $template
	 * @param ICompiler        $compiler
	 * @param ITemplateContext $parent
	 * @param bool             $sandboxed
	 */
	public function __construct (ITemplate $template, ICompiler $compiler, ITemplateContext $parent = null, $sandboxed = false) {
		// Note: We're ignoring all arguments here due to the fact that we're the global context.
	}

	/**
	 * Assigns a new variable to this context.
	 * @param string $name
	 * @param mixed  $value
	 * @return void
	 */
	public function assignVariable ($name, $value = null) {
		$this->setVariable($name, $value, true);
	}

	/**
	 * Compiles a template.
	 * @return void
	 * @throws CompilerException
	 * @throws \ikarus\system\exception\request\NotImplementedException
	 */
	public function compile () {
		throw new NotImplementedException ("Cannot call compile () on a global context.");
	}

	/**
	 * Creates a child context.
	 * @param ITemplate $template
	 * @param bool      $sandboxed
	 * @return ITemplateContext
	 * @throws \ikarus\system\exception\request\NotImplementedException
	 */
	public function createChildContext (ITemplate $template, $sandboxed = false) {
		throw new NotImplementedException ("Cannot call createChildContext () on a basic context.");
	}

	/**
	 * @param bool $returnContent
	 * @return mixed
	 * @throws \ikarus\system\exception\request\NotImplementedException
	 */
	public function execute ($returnContent = true) {
		throw new NotImplementedException ("Cannot call execute () on a global context.");
	}

	/**
	 * Returns the parent context (if any).
	 * @return ITemplateContext
	 * @throws ContextException
	 */
	public function getParent () {
		throw new ContextException ('Cannot get parent of global context.');
	}

	/**
	 * @param string $name
	 * @param mixed  $defaultValue
	 * @param bool   $disableBubble
	 * @return mixed
	 * @throws ContextException
	 */
	public function getVariable ($name, $defaultValue = null, $disableBubble = false) {
		// verify variable
		if (!array_key_exists ($name, $this->variables) && $defaultValue == null)
			throw new ContextException ('Could not find a variable with name "%s" in context tree.', $name);

		// return default value (if any)
		else if (!array_key_exists ($name, $this->variables))
			return $defaultValue;

		// return actual value
		return $this->variables[$name];
	}

	/**
	 * Checks whether the context has a parent context.
	 * @return boolean
	 */
	public function hasParent () {
		return false;
	}

	/**
	 * Checks whether a variable exists.
	 * @param      $name
	 * @param bool $disableBubble
	 * @return boolean
	 */
	public function hasVariable ($name, $disableBubble = false) {
		return (array_key_exists($name, $this->variables));
	}

	/**
	 * Sets a variable's content.
	 * @param      $name
	 * @param      $value
	 * @param bool $disableBubble
	 * @return void
	 */
	public function setVariable ($name, $value = null, $disableBubble = false) {
		// Note: Bubbling is completely ignored in global contexts.

		// check for arrays
		if (is_array ($name)) {
			foreach ($name as $_key => $_val) {
				$this->assignVariable ($_key, $_val);
			}

			// stop code execution here
			return;
		}

		$this->variables[$name] = $value;
	}

	/**
	 * @return string
	 */
	public function __invoke () {
		return $this->execute(true);
	}
}
?> 