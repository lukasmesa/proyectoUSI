<?php
/**
 * PHPWord
 *
 * Copyright (c) 2010 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 010 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    Beta 0.6.1, 13.05.2010
 */


/** PHPWord_Style_Font */
require_once PHPWORD_BASE_PATH . 'PHPWord/Style/Font.php';


/**
 * PHPWord_Section_Text
 *
 * @category   PHPWord
 * @package    PHPWord_Section
 * @copyright  Copyright (c) 2010 PHPWord
 */
class PHPWord_Section_Text {
	
	/**
	 * Text content
	 * 
	 * @var string
	 */
	private $_text;
	
	/**
	 * Text style
	 * 
	 * @var PHPWord_Style_Font
	 */
	private $_style;
	
	
	/**
	 * Create a new Text Element
	 * 
	 * @var string $text
	 * @var mixed $style
	 */
	public function __construct($text = null, $style = null) {
		if(!is_null($style)) {
			if(is_array($style)) {
				$this->_style = new PHPWord_Style_Font('text');
				
				foreach($style as $key => $value) {
					if(substr($key, 0, 1) != '_') {
						$key = '_'.$key;
					}
					$this->_style->setStyleValue($key, $value);
				}
			} else {
				$this->_style = $style;
			}
		} else {
			$this->_style = new PHPWord_Style_Font();
		}
		
		$this->_text = $text;
		
		return $this;
	}
	
	/**
	 * Get Text style
	 * 
	 * @return PHPWord_Style_Font
	 */
	public function getStyle() {
		return $this->_style;
	}
	
	/**
	 * Get Text content
	 * 
	 * @return string
	 */
	public function getText() {
		return $this->_text;
	}
}
?>