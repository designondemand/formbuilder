<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbStaticTextField extends fbFieldBase {

	function fbStaticTextField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'StaticTextField';
		$this->DisplayInForm = true;
		$this->NonRequirableField = true;
		$this->ValidationTypes = array(
            );

	}

	function GetFieldInput($id, &$params, $returnid)
	{
		return $this->GetOption('text','');
	}

	function StatusInfo()
	{
		 return $this->form_ptr->module_ptr->Lang('text_length',strlen($this->GetOption('text','')));
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_text'),
            		$mod->CreateTextArea(false, $formDescriptor,  htmlspecialchars($this->GetOption('text',''), ENT_QUOTES), 'opt_text','pageheadtags'))
		);
		$adv = array(
		);
		return array('main'=>$main,'adv'=>$adv);
	}

}

?>
