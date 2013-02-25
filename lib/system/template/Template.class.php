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
namespace ikarus\system\template;
use ikarus\system\event\GenericEventArguments;
use ikarus\system\event\IdentifierEventArguments;
use ikarus\system\event\PropertyStoreEventArguments;
use ikarus\system\event\template\AssignVariableEvent;
use ikarus\system\event\template\CompilerEventArguments;
use ikarus\system\event\template\CreateTemplateContextEvent;
use ikarus\system\event\template\InitEvent;
use ikarus\system\event\template\InitFinishedEvent;
use ikarus\system\event\template\ParserEventArguments;
use ikarus\system\event\template\SetCompilerEvent;
use ikarus\system\event\template\SetParserEvent;
use ikarus\system\event\template\SetTemplateDirectoryEvent;
use ikarus\system\event\template\SetVariablesEvent;
use ikarus\system\event\template\TemplateContextArguments;
use ikarus\system\event\template\TemplateEventArguments;
use ikarus\system\event\template\VariableEventArguments;
use ikarus\system\Ikarus;

/**
 * Allows easy access to the central template parser.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 * @todo		Add missing use flags.
 */
class Template {
	
	/**
	 * Stores the directory suffix used for compiled templates.
	 * @var			string
	 */
	const TEMPLATE_OUTPUT_SUFFIX = 'compiled/';
	
	/**
	 * Stores an instance of the current template compiler.
	 * @var				ITemplateCompiler
	 */
	protected $compiler = null;
	
	/**
	 * Stores the current parser instance.
	 * @var				ITemplateParser
	 */
	protected $parser = null;
	
	/**
	 * Stores the Ikarus path to the template directory.
	 * @var				string
	 */
	protected $templateDirectory = '';
	
	/**
	 * Stores a list of variables.
	 * @var				VariableContext[]
	 */
	protected $variables = array();
	
	/**
	 * Constructs the object.
	 * @param			ITemplateCompiler			$compiler
	 * @param			string					$templateDirectory
	 */
	public function __construct($compiler = null, $templateDirectory = null) {
		if ($compiler === null) $this->compiler = new DefaultTemplateCompiler($this);
		if ($templateDirectory === null) $templateDirectory = Ikarus::getPath().'templates/';
		
		// fire event
		$event = new InitEvent(new TemplateEventArguments($this));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// store data
		$this->setCompiler($compiler);
		$this->setTemplateDirecotry($templateDirectory);
		
		// fire event
		Ikarus::getEventManager()->fire(new InitFinishedEvent(new TemplateEventArguments($this)));
	}
	
	/**
	 * Assigns a variable.
	 * @param			string			$name
	 * @param			mixed			$value
	 * @param			VariableContext		$context
	 * @return			void
	 */
	public function assignVariable($name, $value, VariableContext $context = null) {
		// get default context
		if ($context === null) $context = $this->variables['default'];
		
		// fire event
		$event = new AssignVariableEvent(new VariableEventArguments($name, $value, $context));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// set variable
		if ($context->has($name))
			$context->set($name, $value);
		else
			$context->add($name, $value);
	}
	
	/**
	 * Assigns a list of variables.
	 * @param			mixed[]			$variables
	 * @return			void
	 */
	public function assignVariables($variables, VariableContext $context = null) {
		foreach($variables as $key => $value) {
			$this->assignVariable($key, $value, $context);
		}
	}
	
	/**
	 * Creates a new template context.
	 * @param			TemplateFile			$template
	 * @param			VariableContext			$variables
	 * @return			\ikarus\system\template\TemplateContext
	 */
	public function createTemplateContext(TemplateFile $template, $variables = array(), ITemplateParser $parser = null) {
		// add default variable context
		if (empty($variables)) $variables[] = $this->variables['default'];
		if ($parser === null) $parser = $this->parser;
		
		// create context
		$context = new TemplateContext($template, $parser, $variables);
		
		// fire event
		Ikarus::getEventManager()->fire(new CreateTemplateContextEvent(new TemplateContextEventArguments($context)));
		
		// return context
		return $context;
	}
	
	/**
	 * Returns the current template compiler.
	 * @return			\ikarus\system\template\ITemplateCompiler
	 */
	public function getCompiler() {
		return $this->compiler;
	}
	
	/**
	 * Returns the current parser.
	 * @return			\ikarus\system\template\ITemplateParser
	 */
	public function getParser() {
		return $this->parser;
	}
	
	/**
	 * Returns the current template directory.
	 * @return			string
	 */
	public function getTemplateDirectory() {
		return $this->templateDirectory;
	}
	
	/**
	 * Returns the variable list.
	 * @return			\ikarus\system\template\mixed[]
	 */
	public function getVariables() {
		return $this->variables;
	}
	
	/**
	 * Sets a new compiler.
	 * @param			ITemplateCompiler			$compiler
	 * @return			void
	 */
	public function setCompiler(ITemplateCompiler $compiler) {
		// fire event
		$event = new SetCompilerEvent(new CompilerEventArguments($compiler));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// store
		$this->compiler = $compiler;
	}
	
	/**
	 * Sets a new parser.
	 * @param			ITemplateParser			$parser
	 * @return			void
	 */
	public function setParser(ITemplateParser $parser) {
		// fire event
		$event = new SetParserEvent(new ParserEventArguments($parser));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// store
		$this->parser = $parser;
	}
	
	/**
	 * Sets a new template directory.
	 * @param			string				$directory
	 * @return			void
	 */
	public function setTemplateDirectory($directory) {
		// fire event
		$event = new SetTemplateDirectoryEvent(new IdentifierEventArguments($directory));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// validation
		if (!Ikarus::getFilesystemManager()->getDefaultAdapter()->isDirectory($directory)) throw new InvalidTemplateDirectoryException('Cannot use directory "%s": No such directory');
		if (!Ikarus::getFilesystemManager()->getDefaultAdapter()->isReadable($directory) or Ikarus::getFilesystemManager()->getDefaultAdapter()->isWritable($directory)) throw new InvalidTemplateDirectoryException('Cannot use directory "%s": Directory is not readable or writeable');
		
		// store
		$this->templateDirectory = $directory;
	}
	
	/**
	 * Sets a new variable array.
	 * @param			VariableContext[9		$variables
	 * @return			void
	 */
	public function setVariables($variables) {
		// fire event
		$event = new SetVariablesEvent(new PropertyStoreEventArguments($variables));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable event
		if ($event->isCancelled()) return;
		
		// store
		$this->variables = $variables;
	}
}
?>