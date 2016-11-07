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
class PHPWord_Section_Link {
	
	/**
	 * Link source
	 * 
	 * @var string
	 */
	private $_linkSrc;
	
	/**
	 * Link name
	 * 
	 * @var string
	 */
	private $_linkName;
	
	/**
	 * Link Relation ID
	 * 
	 * @var string
	 */
	private $_rId;
	
	/**
	 * Link style
	 * 
	 * @var PHPWord_Style_Font
	 */
	private $_style;
	
	/**
	 * Create a new Link Element
	 * 
	 * @var string $linkSrc
	 * @var string $linkName
	 * @var mixed $style
	 */
	public function __construct($linkSrc, $linkName = null, $style = null) {
		$this->_linkSrc = $linkSrc;
		$this->_linkName = $linkName;
		
		if(!is_null($style)) {
			if(is_array($style)) {
				$this->_style = new PHPWord_Style_Font();
				
				foreach($style as $key => $value) {
					if(substr($key, 0, 1) != '_') {
						$key = '_'.$key;
					}
					$this->_style->setStyleValue($key, $value);
				}
			} else {
				$this->_style = $style;
			}
		}
		
		return $this;
	}
	
	/**
	 * Get Link Relation ID
	 * 
	 * @return int
	 */
	public function getRelationId() {
		return $this->_rId;
	}
	
	/**
	 * Set Link Relation ID
	 * 
	 * @param int $rId
	 */
	public function setRelationId($rId) {
		$this->_rId = $rId;
	}
	
	/**
	 * Get Link source
	 * 
	 * @return string
	 */
	public function getLinkSrc() {
		return $this->_linkSrc;
	}
	
	/**
	 * Get Link name
	 * 
	 * @return string
	 */
	public function getLinkName() {
		return $this->_linkName;
	}
	
	/**
	 * Get Text style
	 * 
	 * @return PHPWord_Style_Font
	 */
	public function getStyle() {
		return $this->_style;
	}
}
?>