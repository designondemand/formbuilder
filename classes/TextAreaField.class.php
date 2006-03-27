<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyrigth (c)2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbTextAreaField extends fbFieldBase {

	function fbTextAreaField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
		$mod = $form_ptr->module_ptr;
		$this->Type = 'TextAreaField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            );

	}

	function GetFieldInput($id, &$params, $returnid)
	{            
	   $mod = $this->form_ptr->module_ptr;
       return $mod->CreateTextArea($this->GetOption('wysiwyg','0') == '1'?true:false,
       		$id, htmlspecialchars($this->Value, ENT_QUOTES),
       		'_'.$this->Id);            
	}


	function StatusInfo()
	{
		$ret = '';
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret = array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 if ($this->GetOption('wysiwyg','0') == '1')
		 	{
		 	$ret .= ' wysiwyg';
		 	}
		 else
		 	{
		 	$ret .= ' non-wysiwyg';
		 	}
		 return $ret;
	}


	function PrePopulateAdminForm($formDescriptor)
	{
	   $mod = $this->form_ptr->module_ptr;
		return array(
			'main'=>
				array(array($mod->Lang('title_use_wysiwyg'),
            		$mod->CreateInputCheckbox($formDescriptor, 'opt_wysiwyg',
            		'1',$this->GetOption('wysiwyg','0'))))
         	);
	}


	function Validate()
	{
		$result = true;
		$message = '';
		$mod = $this->form_ptr->module_ptr;
		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
