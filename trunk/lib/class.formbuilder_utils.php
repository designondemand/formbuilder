<?php
/*
 * FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
 * More info at http://dev.cmsmadesimple.org/projects/formbuilder
 *
 * A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
 * This project's homepage is: http://www.cmsmadesimple.org
 */

class formbuilder_utils {

	private function __construct() {}

	static final public function create_input_text($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $type='text', $required=false, $n=null)
	{
		$id = cms_htmlentities($id);
		$name = cms_htmlentities($name);
		$cssid = $name;
		if(intval($n))
		{
			$cssid .= '_' . intval($n);
		}
		$value = htmlspecialchars($value);
		$size = cms_htmlentities($size);
		$maxlength = cms_htmlentities($maxlength);

		$text = '<input type="'.$type.'" class="cms_'.$type.'" name="'.$id.$name.'" id="'.$cssid.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		if ($required)
		{
			$text .= ' required="required"';
		}
		$text .= " />\n";
		return $text;
	}

	static final public function create_input_checkbox($id, $name, $value='', $selectedvalue='', $addttext='')
	{
		$id = cms_htmlentities($id);
		$name = cms_htmlentities($name);
		$value = cms_htmlentities($value);
		$selectedvalue = cms_htmlentities($selectedvalue);

		$text = '<input type="checkbox" class="cms_checkbox" name="'.$id.$name.'" id="'.$name.'" value="'.$value.'"';
		if ($selectedvalue == $value)
		{
			$text .= ' ' . 'checked="checked"';
		}
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= " />\n";
		return $text;
	}
}