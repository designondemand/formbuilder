<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbPageBreakField extends fbFieldBase {

	function fbPageBreakField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'PageBreakField';
		$this->DisplayInForm = false;
		$this->Required = false;
		//$this->ValidationTypes = array($mod->Lang('validation_none')=>'none');
		$this->ValidationTypes = array();
		$this->NonRequirableField = true;
	}

	function GetFieldInput($id, &$params, $return_id)
	{
	}


	function StatusInfo()
	{
		return '';
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		return array();
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = $this->form_ptr->module_ptr;
		// remove the "required" field
		$reqIndex = -1;
		for ($i=0;$i<count($mainArray);$i++)
			{
			if ($mainArray[$i]->title == $mod->Lang('title_field_required'))
				{
				$reqIndex = $i;
				}
			}
		if ($reqIndex != -1)
			{
			array_splice($mainArray, $reqIndex,1);
			}
		// remove the "hide name" field
		$hideIndex = -1;
		for ($i=0;$i<count($advArray);$i++)
			{
			if ($advArray[$i]->title == $mod->Lang('title_hide_label'))
				{
				$advArray[$i]->title = $mod->Lang('tab_advanced');
				$advArray[$i]->input = $mod->Lang('title_no_advanced_options');
				}
			}
	}


	function Validate()
	{
		return array(true,'');
	}
	
	function AdminValidate()
	{
		return array(true,'');
	}
}

?>
