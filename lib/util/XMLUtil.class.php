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

/**
 * Provides utils for XML files parsed with DOM
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class XMLUtil {
	
	/**
	 * Converts a DOMDocument or DOMElement to array
	 * @param			DOMElement			$rootElement
	 * @return			array
	 */
	public static function convertToArray($rootElement) {
		if ($rootElement instanceof DOMDocument) return static::convertToArray($rootElement->documentElement);
		
		// create result object
		$array = array();

		// handle attributes
		if ($root->hasAttributes()) {
			$attributes = $root->attributes;
			foreach ($attributes as $attribute) $array[$attribute->name] = $attribute->value;
		}
		
		// get children
		$children = $root->childNodes;
	
		// handle nodes with text content
		if ($children->length == 1) {
			// get content
			$child = $children->item(0);
			
			// check for correct node type
			if ($child->nodeType == XML_TEXT_NODE) {
				// save value
				$array['value'] = $child->nodeValue;
	
				// return text content if there is no other content to return
				if (count($array) == 1)
					return $array['value'];
				else
					return $array;
			}
		}
	
		// create group array
		$group = array();
	
		// loop through children
		foreach($children as $child)
			// no other children with this node name found
			if (!isset($result[$child->nodeName]))
				$array[$child->nodeName] = static::convertToArray($child);
			// other children with this node name found -> group them
			else {
				// copy existing elements to group (if any to copy)
				if (!isset($group[$child->nodeName])) {
					$otherChild = $array[$child->nodeName];
					$array[$child->nodeName] = array($otherChild);
					$group[$child->nodeName] = 1;
				}
	
				// append node to result
				$array[$child->nodeName][] = static::convertToArray($child);
			}
	
		return $array; 
	}
	
	/**
	 * Converts an array into an XML document.
	 * @param			mixed[]			$data
	 * @param			string			$root			Defaults to 'data'
	 * @param			string			$doc
	 * @return			DOMDocument
	 */
	public static function convertFromArray($data, $root = null, $doc = null) {
		// create root element
		if ($root === null) {
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement(($rootElement === null ? 'data' : $rootElement));
			$doc->appendChild($root);
		}
		
		foreach($data as $key => $value) {
			$element = $doc->createElement($key);
			if (is_array($value))
				static::convertFromArray($value, $element, $doc);
			else {
				$value = $element->createCDATASection((string) $value);
				$element->appendChild($value);
			}
			
			$root->appendChild($element);
		}
		
		return $doc;
	}
}
?>