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


/** PHPWord_Media */
require_once PHPWORD_BASE_PATH . 'PHPWord/Media.php';

/** PHPWord_TOC */
require_once PHPWORD_BASE_PATH . 'PHPWord/TOC.php';

/** PHPWord_Section_Settings */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Settings.php';

/** PHPWord_Section_Text */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Text.php';

/** PHPWord_Section_Text */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Link.php';

/** PHPWord_Section_TextBreak */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/TextBreak.php';

/** PHPWord_Section_PageBreak */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/PageBreak.php';

/** PHPWord_Section_Table */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Table.php';

/** PHPWord_Section_ListItem */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/ListItem.php';

/** PHPWord_Section_Image */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Image.php';

/** PHPWord_Section_MemoryImage */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/MemoryImage.php';

/** PHPWord_Section_Object */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Object.php';

/** PHPWord_Section_Title */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Title.php';

/** PHPWord_Section_Header */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Header.php';

/** PHPWord_Section_Footer */
require_once PHPWORD_BASE_PATH . 'PHPWord/Section/Footer.php';


/**
 * PHPWord_Section
 *
 * @category   PHPWord
 * @package    PHPWord_Section
 * @copyright  Copyright (c) 2010 PHPWord
 */
class PHPWord_Section {
	
	/**
	 * Section count
	 * 
	 * @var int
	 */
	private $_sectionCount;
	
	/**
	 * Section settings
	 * 
	 * @var PHPWord_Section_Settings
	 */
	private $_settings;
	
	/**
	 * Section Element Collection
	 * 
	 * @var array
	 */
	private $_elementCollection = array();
	
	/**
	 * Section Header
	 * 
	 * @var PHPWord_Section_Header
	 */
	private $_header = null;
	
	/**
	 * Section Footer
	 * 
	 * @var PHPWord_Section_Footer
	 */
	private $_footer = null;
	
	
	/**
	 * Create a new Section
	 * 
	 * @param int $sectionCount
	 * @param mixed $settings
	 */
	public function __construct($sectionCount, $settings = null) {
		$this->_sectionCount = $sectionCount;
		$this->_settings = new PHPWord_Section_Settings();
		
		if(!is_null($settings) && is_array($settings)) {
			foreach($settings as $key => $value) {
				if(substr($key, 0, 1) != '_') {
					$key = '_'.$key;
				}
				$this->_settings->setSettingValue($key, $value);
			}
		}
	}
	
	/**
	 * Get Section Settings
	 * 
	 * @return PHPWord_Section_Settings
	 */
	public function getSettings() {
		return $this->_settings;
	}
	
	/**
	 * Add a Text Element
	 * 
	 * @param string $text
	 * @param mixed $style
	 * @return PHPWord_Section_Text
	 */
	public function addText($text, $style = null, $inline = false) {
		$givenText = utf8_encode($text);
		$text = new PHPWord_Section_Text($givenText, $style);
		$this->_elementCollection[] = $text;
		return $text;
		/*
		if(!$inline) {
			
		} else {
			$text = $this->_getLastTextElement();
			if(!$text) {
				$text = new PHPWord_Section_Text($givenText, $style);
				$this->_elementCollection[] = $text;
			} else {
				
			}
		}
		*/
	}
	
	/**
	 * Add a Link Element
	 * 
	 * @param string $linkSrc
	 * @param string $linkName
	 * @param mixed $style
	 * @return PHPWord_Section_Link
	 */
	public function addLink($linkSrc, $linkName = null, $style = null) {
		$linkSrc = utf8_encode($linkSrc);
		if(!is_null($linkName)) {
			$linkName = utf8_encode($linkName);
		}
		
		$link = new PHPWord_Section_Link($linkSrc, $linkName, $style);
		$rID = PHPWord_Media::addSectionLinkElement($linkSrc);
		$link->setRelationId($rID);
		
		$this->_elementCollection[] = $link;
		return $link;
	}
	
	/**
	 * Add a TextBreak Element
	 * 
	 * @param int $count
	 */
	public function addTextBreak($count = 1) {
		for($i=1; $i<=$count; $i++) {
			$this->_elementCollection[] = new PHPWord_Section_TextBreak();
		}
	}
	
	/**
	 * Add a PageBreak Element
	 */
	public function addPageBreak() {
		$this->_elementCollection[] = new PHPWord_Section_PageBreak();
	}
	
	/**
	 * Add a Table Element
	 * 
	 * @param mixed $style
	 * @return PHPWord_Section_Table
	 */
	public function addTable($style = null) {
		$table = new PHPWord_Section_Table('section', $this->_sectionCount, $style);
		$this->_elementCollection[] = $table;
		return $table;
	}
	
	/**
	 * Add a ListItem Element
	 * 
	 * @param string $text
	 * @param int $depth
	 * @param mixed $styleText
	 * @param mixed $styleList
	 * @return PHPWord_Section_ListItem
	 */
	public function addListItem($text, $depth = 0, $styleText = null, $styleList = null) {
		$text = utf8_encode($text);
		$listItem = new PHPWord_Section_ListItem($text, $depth, $styleText, $styleList);
		$this->_elementCollection[] = $listItem;
		return $listItem;
	}
	
	/**
	 * Add a OLE-Object Element
	 * 
	 * @param string $src
	 * @param mixed $style
	 * @return PHPWord_Section_Object
	 */
	public function addObject($src, $style = null) {
		$object = new PHPWord_Section_Object($src, $style);
		
		if(!is_null($object->getSource())) {
			$inf = pathinfo($src);
			$ext = $inf['extension'];
			if(strlen($ext) == 4 && strtolower(substr($ext, -1)) == 'x') {
				$ext = substr($ext, 0, -1);
			}
			
			$iconSrc = PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/';
			if(!file_exists($iconSrc.'_'.$ext.'.png')) {
				$iconSrc = $iconSrc.'_default.png';
			} else {
				$iconSrc .= '_'.$ext.'.png';
			}
			
			$rIDimg = PHPWord_Media::addSectionMediaElement($iconSrc, 'image');
			$data = PHPWord_Media::addSectionMediaElement($src, 'oleObject');
			$rID = $data[0];
			$objectId = $data[1];
			
			$object->setRelationId($rID);
			$object->setObjectId($objectId);
			$object->setImageRelationId($rIDimg);
			
			$this->_elementCollection[] = $object;
			return $object;
		} else {
			trigger_error('Source does not exist or unsupported object type.');
		}
	}
	
	/**
	 * Add a Image Element
	 * 
	 * @param string $src
	 * @param mixed $style
	 * @return PHPWord_Section_Image
	 */
	public function addImage($src, $style = null) {
		$image = new PHPWord_Section_Image($src, $style);
		
		if(!is_null($image->getSource())) {
			$rID = PHPWord_Media::addSectionMediaElement($src, 'image');
			$image->setRelationId($rID);
			
			$this->_elementCollection[] = $image;
			return $image;
		} else {
			trigger_error('Source does not exist or unsupported image type.');
		}
	}
	
	/**
	 * Add a by PHP created Image Element
	 * 
	 * @param string $link
	 * @param mixed $style
	 * @return PHPWord_Section_MemoryImage
	 */
	public function addMemoryImage($link, $style = null) {
		$memoryImage = new PHPWord_Section_MemoryImage($link, $style);
		if(!is_null($memoryImage->getSource())) {
			$rID = PHPWord_Media::addSectionMediaElement($link, 'image', $memoryImage);
			$memoryImage->setRelationId($rID);
			
			$this->_elementCollection[] = $memoryImage;
			return $memoryImage;
		} else {
			trigger_error('Unsupported image type.');
		}
	}
	
	/**
	 * Add a Table-of-Contents Element
	 * 
	 * @param mixed $styleFont
	 * @param mixed $styleTOC
	 * @return PHPWord_TOC
	 */
	public function addTOC($styleFont = null, $styleTOC = null) {
		$toc = new PHPWord_TOC($styleFont, $styleTOC);
		$this->_elementCollection[] = $toc;
		return $toc;
	}
	
	/**
	 * Add a Title Element
	 * 
	 * @param string $text
	 * @param int $depth
	 * @return PHPWord_Section_Title
	 */
	public function addTitle($text, $depth = 1) {
		$text = utf8_encode($text);
		$styles = PHPWord_Style::getStyles();
		if(array_key_exists('Heading_'.$depth, $styles)) {
			$style = 'Heading'.$depth;
		} else {
			$style = null;
		}
		
		$title = new PHPWord_Section_Title($text, $depth, $style);
		
		$data = PHPWord_TOC::addTitle($text, $depth);
		$anchor = $data[0];
		$bookmarkId = $data[1];
		
		$title->setAnchor($anchor);
		$title->setBookmarkId($bookmarkId);
		
		$this->_elementCollection[] = $title;
		return $title;
	}
	
	/**
	 * Get all Elements
	 * 
	 * @return array
	 */
	public function getElements() {
		return $this->_elementCollection;
	}
	
	/**
	 * Create a new Header
	 * 
	 * @return PHPWord_Section_Header
	 */
	public function createHeader() {
		$header = new PHPWord_Section_Header($this->_sectionCount);
		$this->_header = $header;
		return $header;
	}
	
	/**
	 * Get Header
	 * 
	 * @return PHPWord_Section_Header
	 */
	public function getHeader() {
		return $this->_header;
	}
	
	/**
	 * Create a new Footer
	 * 
	 * @return PHPWord_Section_Footer
	 */
	public function createFooter() {
		$footer = new PHPWord_Section_Footer($this->_sectionCount);
		$this->_footer = $footer;
		return $footer;
	}
	
	/**
	 * Get Footer
	 * 
	 * @return PHPWord_Section_Footer
	 */
	public function getFooter() {
		return $this->_footer;
	}
	
	/**
	 * Find the last text element in elementCollection
	 * 
	 * @return PHPWord_Section_Text
	 */
	private function _getLastTextElement() {
		$count = count($this->_elementCollection);
		
		for($i = $count-1; $i>=0; $i++) {
			if($this->_elementCollection[$i] instanceof PHPWord_Section_Text) {
				return $this->_elementCollection[$i];
			}
		}
		
		return false;
	}
}
?>