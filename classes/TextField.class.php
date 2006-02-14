<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbTextField extends fbFieldBase {

	function fbTextField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'TextField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            $mod->Lang('validation_numeric')=>'numeric',
            $mod->Lang('validation_integer')=>'integer',
            $mod->Lang('validation_email_address')=>'email',
            $mod->Lang('validation_regex_match')=>'regex_match',
            $mod->Lang('validation_regex_nomatch')=>'regex_nomatch'
            );

	}


	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->CreateInputText($id, '_'.$this->Id,
			htmlspecialchars($this->Value, ENT_QUOTES),
            $this->GetOption('length')<25?$this->GetOption('length'):25,
            $this->GetOption('length'),
            $this->form_ptr->GetAttr('name_as_id','0')=='1'?'id="'.$this->Name.'"':'');
	}

	function StatusInfo()
	{
		$ret = $this->form_ptr->module_ptr->Lang('abbreviation_length',$this->GetOption('length','80'));
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_maximum_length'),
            		$mod->CreateInputText($formDescriptor, 'opt_length',
            		$this->GetOption('length','80'),25,25))
		);
		$adv = array(
			array($mod->Lang('title_field_regex'),
               array($mod->CreateInputText($formDescriptor, 'opt_regex',
            		$this->GetOption('regex'),25,255),$mod->Lang('title_regex_help')))	
		);
		return array('main'=>$main,'adv'=>$adv);
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
		  	   case 'numeric':
                  if ($this->Value === false ||
                      ! preg_match("/^([\d\.\,])+$/i", $this->Value))
                      {
                      $result = false;
                      $message = $mod->Lang('please_enter_a_number',$this->Name);
                      }
		  	       break;
		  	   case 'integer':
                  if ($this->Value === false ||
                  	! preg_match("/^([\d])+$/i", $this->Value) ||
                      intval($this->Value) != $this->Value)
                    {
                    $result = false;
                    $message = $mod->Lang('please_enter_an_integer',$this->Name);
                    }
		  	       break;
		  	   case 'email':
                  if ($this->Value === false ||
                      ! preg_match("/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i", $this->Value))
                    {
                    $result = false;
                    $message = $mod->Lang('please_enter_an_email',$this->Name);
                    }
		  	       break;
		  	   case 'regex_match':
                  if ($this->Value === false ||
                      ! preg_match($this->GetOption('regex','/.*/'), $this->Value))
                    {
                    $result = false;
                    $message = $mod->Lang('please_enter_valid',$this->Name);
                    }
		  	   	   break;
		  	   case 'regex_nomatch':
                  if ($this->Value === false ||
                       preg_match($this->GetOption('regex','/.*/'), $this->Value))
                    {
                    $result = false;
                    $message = $mod->Lang('please_enter_valid',$this->Name);
                    }
		  	   	   break;
		  }
		if ($this->GetOption('length',0) > 0 && strlen($this->Value) > $this->GetOption('length',0))
			{
			$result = false;
			$message = $mod->Lang('please_enter_no_longer',$this->GetOption('length',0));
			}
		return array($result, $message);
	}
}

?>
