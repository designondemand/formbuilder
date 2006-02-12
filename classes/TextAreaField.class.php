<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbTextAreaField extends fbFieldBase {

	function fbTextAreaField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
		$mod = $form_ptr->module_ptr;
		$this->Type = 'TextAreaField';
		$this->DisplayType = $mod->Lang('field_type_text_area');
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


	function RenderAdminForm($formDescriptor)
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
