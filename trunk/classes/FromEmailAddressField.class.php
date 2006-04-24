<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbFromEmailAddressField extends fbFieldBase {

	function fbFromEmailAddressField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'FromEmailAddressField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_email_address')=>'email',
            );
        $this->ValidationType = 'email';
	   $this->modifiesOtherFields = true;
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->CreateInputText($id, '_'.$this->Id,
			htmlspecialchars($this->Value, ENT_QUOTES),
           25,128);
	}
	
	function ModifyOtherFields()
	{
		$mod = $this->form_ptr->module_ptr;
		$others = $this->form_ptr->GetFields();
		
		for($i=0;$i<count($others);$i++)
			{
			$replVal = '';
			if ($others[$i]->IsDisposition() && is_subclass_of($others[$i],'fbDispositionEmailBase'))
				{
				$others[$i]->SetOption('email_from_address',$this->Value);
				}
			}
		
	}

	function StatusInfo()
	{
		return '';
	}

	function Validate()
	{
		$result = true;
		$message = '';
		$mod = $this->form_ptr->module_ptr;
		switch ($this->ValidationType)
		  {
		  	   case 'email':
                  if ($this->Value !== false &&
                      ! preg_match($mod->email_regex, $this->Value))
                    {
                    $result = false;
                    $message = $mod->Lang('please_enter_an_email',$this->Name);
                    }
		  	       break;
		  }
		return array($result, $message);
	}
}

?>
