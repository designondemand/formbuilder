<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffCheckboxInput extends ffInput {

	function ffCheckboxInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'CheckboxInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_checkbox');
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_must_check')=>'checked'
            );

		if (ffUtilityFunctions::def($params['box_name']) && ffUtilityFunctions::def($params['box_value']))
		  {
		  $this->AddOption('checkbox', $params['box_name'], $params['box_value']);
          }
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		$boxOpt = $this->GetOptionByKind('checkbox');
        echo CMSModule::CreateInputCheckbox($id, $this->Alias, $boxOpt[0]->OptionId, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
        echo $boxOpt[0]->Name;
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
			}
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			$boxOpt = $this->GetOptionById($this->Value);
			return $boxOpt[0]->Value;
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}


	function StatusInfo()
	{
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
	}


	function RenderAdminForm($formDescriptor)
	{
		$boxOpt = $this->GetOptionByKind('checkbox');
		return array($this->mod_globals->Lang('title_checkbox_name').':'=>CMSModule::CreateInputText($formDescriptor, 'box_name',
                ffUtilityFunctions::def($boxOpt[0]->Name)?$this->NerfHTML($boxOpt[0]->Name):'',25),
			$this->mod_globals->Lang('title_submitted_value').':'=>CMSModule::CreateInputText($formDescriptor, 'box_value',
                ffUtilityFunctions::def($boxOpt[0]->Value)?$this->NerfHTML($boxOpt[0]->Value):'',25)
		);
	}


	function Validate()
	{
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'checked':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $opt = $this->GetOptionByKind('checkbox');
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('you_must_check1').
                            $opt[0]->Name.$this->mod_globals->Lang('you_must_check2');
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
