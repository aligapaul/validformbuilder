<?php
/***************************
 * ValidForm Builder - build valid and secure web forms quickly
 *
 * Copyright (c) 2009-2013 Neverwoods Internet Technology - http://neverwoods.com
 *
 * Felix Langfeldt <felix@neverwoods.com>
 * Robin van Baalen <robin@neverwoods.com>
 *
 * All rights reserved.
 *
 * This software is released under the GNU GPL v2 License <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 *
 * @package    ValidForm
 * @author     Felix Langfeldt <felix@neverwoods.com>, Robin van Baalen <robin@neverwoods.com>
 * @copyright  2009-2013 Neverwoods Internet Technology - http://neverwoods.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL v2
 * @link       http://validformbuilder.org
 ***************************/

require_once('class.vf_element.php');

/**
 * Password Class
 *
 * @package ValidForm
 * @author Felix Langfeldt
 */
class VF_Password extends VF_Element {

	public function toHtml($submitted = FALSE, $blnSimpleLayout = FALSE, $blnLabel = true, $blnDisplayError = true) {
		$strOutput 	= "";
		$blnError 	= ($submitted && !$this->__validator->validate() && $blnDisplayError) ? TRUE : FALSE;

		if (!$blnSimpleLayout) {

			//*** We asume that all dynamic fields greater than 0 are never required.
			if ($this->__validator->getRequired() && $intCount == 0) {
				$this->setMeta("class", "vf__required");
			} else {
				$this->setMeta("class", "vf__optional");
			}

			//*** Set custom meta.
			if ($blnError) $this->setMeta("class", "vf__error");
			if (!$blnLabel) $this->setMeta("class", "vf__nolabel");
			if (!empty($this->__hint)) $this->setMeta("class", "vf__hint");

			$this->setConditionalMeta($submitted);
			$strOutput = "<div{$this->__getMetaString()}>\n";

			if ($blnError) $strOutput .= "<p class=\"vf__error\">{$this->__validator->getError()}</p>";

			if ($blnLabel) {
				$strLabel = (!empty($this->__requiredstyle) && $this->__validator->getRequired()) ? sprintf($this->__requiredstyle, $this->__label) : $this->__label;
				if (!empty($this->__label)) $strOutput .= "<label for=\"{$this->__id}\"{$this->__getLabelMetaString()}>{$strLabel}</label>\n";
			}

		} else {
			if (!empty($this->__hint)) $this->setMeta("class", "vf__hint");
			if ($blnError) $this->setMeta("class", "vf__error");
			$this->setMeta("class", "vf__multifielditem");

			$strOutput = "<div{$this->__getMetaString()}\">\n";

			if ($blnError) {
				$strOutput .= "<p class=\"vf__error\">{$this->__validator->getError($intCount)}</p>";
			}
		}

		//*** Add maxlength attribute to the meta array. This is being read by the getMetaString method.
		if ($this->__validator->getMaxLength() > 0) {
			$this->__meta["maxlength"] = $this->__validator->getMaxLength();
		}

		$strOutput .= "<input type=\"password\" value=\"{$this->__getValue($submitted)}\" name=\"{$this->__name}\" id=\"{$this->__id}\"{$this->__getFieldMetaString()} />\n";

		if (!empty($this->__tip)) $strOutput .= "<small class=\"vf__tip\">{$this->__tip}</small>\n";
		$strOutput .= "</div>\n";

		return $strOutput;
	}

	public function toJS() {
		$strCheck = $this->__validator->getCheck();
		$strCheck = (empty($strCheck)) ? "''" : str_replace("'", "\\'", $strCheck);
		$strRequired = ($this->__validator->getRequired()) ? "true" : "false";;
		$intMaxLength = ($this->__validator->getMaxLength() > 0) ? $this->__validator->getMaxLength() : "null";
		$intMinLength = ($this->__validator->getMinLength() > 0) ? $this->__validator->getMinLength() : "null";

		$strOutput = "objForm.addElement('{$this->__id}', '{$this->__name}', {$strCheck}, {$strRequired}, {$intMaxLength}, {$intMinLength}, '" . addslashes($this->__validator->getFieldHint()) . "', '" . addslashes($this->__validator->getTypeError()) . "', '" . addslashes($this->__validator->getRequiredError()) . "', '" . addslashes($this->__validator->getHintError()) . "', '" . addslashes($this->__validator->getMinLengthError()) . "', '" . addslashes($this->__validator->getMaxLengthError()) . "');\n";

		$objMatchWith = $this->getValidator()->getMatchWith();
		if (is_object($objMatchWith)) {
			$strOutput .= "objForm.matchfields('" . $this->__id . "', '" . $objMatchWith->getId() . "', '" . $this->__validator->getMatchWithError() . "');\n";
		}

		if ($this->hasConditions() && (count($this->getConditions() > 0))) {
			foreach ($this->getConditions() as $objCondition) {
				$strOutput .= "objForm.addCondition(" . json_encode($objCondition->jsonSerialize()) . ");\n";
			}
		}

		return $strOutput;
	}

}

?>