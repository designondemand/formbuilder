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
		$this->DisplayType = $mod->Lang('field_type_text_input');
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            $mod->Lang('validation_numeric')=>'numeric',
            $mod->Lang('validation_integer')=>'integer',
            $mod->Lang('validation_email_address')=>'email',
            $mod->Lang('validation_regex_match')=>'regex_match',
            $mod->Lang('validation_regex_nomatch')=>'regex_nomatch'
            );

	}
/*
	function WriteToPublicForm($id, &$params, $return_id)
	{
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		echo CMSModule::CreateInputText($id, $this->Alias, $this->NerfHTML($this->Value),
            $this->Length<$this->DisplayLength?$this->Length:$this->DisplayLength,
            $this->Length,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

*/
	function StatusInfo()
	{
		$ret = $this->form_ptr->module_ptr->Lang('abbreviation_length',$this->GetOption('length','80'));
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}


	function RenderAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		return array('main'=>
			array($mod->Lang('title_maximum_length')=>
            		$mod->CreateInputText($formDescriptor, 'opt_length',
            		$this->GetOption('length','80'),25,25)),
            'adv'=>array($mod->Lang('title_field_regex')=>
               array($mod->CreateInputText($formDescriptor, 'opt_regex',
            		$this->GetOption('regex'),25,255),$mod->Lang('title_regex_help'))));
	}

/*
	function Validate()
	{
		$result = true;
		$message = '';
		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'nonempty':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_enter_a_value').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  	   case 'numeric':
                  if (ffUtilityFunctions::def($this->Value) &&
                      ! preg_match("/^([\d\.\,])+$/i", $this->Value))
                      {
                      $result = false;
                      $message = $this->mod_globals->Lang('please_enter_a_number').' "'.$this->Name.'"';
                      }
		  	       break;
		  	   case 'integer':
                  if (ffUtilityFunctions::def($this->Value) &&
                      intval($this->Value) != $this->Value)
                    {
                    $result = false;
                    $message = $this->mod_globals->Lang('please_enter_an_integer').' "'.$this->Name.'"';
                    }
		  	       break;
		  	   case 'email':
                  if (ffUtilityFunctions::def($this->Value) &&
                      ! preg_match("/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i", $this->Value))
                    {
                    $result = false;
                    $message = $this->mod_globals->Lang('please_enter_an_email').' "'.$this->Name.'"';
                    }
		  	       break;
		  }
		if ($this->Length > 0 && strlen($this->Value) > $this->Length)
			{
			$result = false;
			$message = $this->mod_globals->Lang('please_enter_no_longer').$this->Length." ".
                $this->mod_globals->Lang('characters') . "!";
			}
		return array($result, $message);
	}
*/
}

?>
