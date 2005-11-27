<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffEmailFromInput extends ffInput {

	function ffEmailFromInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'EmailFromInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_email_from');
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_email_address')=>'email'
            );
		if ($this->Length == -1)
		  {
          $this->Length = 255;
          }
        $this->ValidationType = 'email';

	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		echo CMSModule::CreateInputText($id, $this->Alias.'[]', ffUtilityFunctions::def($this->Value)?$this->NerfHTML($this->Value[0]):'',
            25,
            $this->Length,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_name"':''). '&lt;'.CMSModule::CreateInputText($id, $this->Alias.'[]', ffUtilityFunctions::def($this->Value)?$this->NerfHTML($this->Value[1]):'',
            25,
            $this->Length,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_email"':'').'&gt;';
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}


	function StatusInfo()
	{
		return '';
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			$this->mod_globals->FromName = $this->Value[0];
			$this->mod_globals->FromAddress = $this->Value[1];
			return $this->Value[0].' <'.$this->Value[1].'>';
			}
		else
			{
			return '';
			}
	}



	function RenderAdminForm($formDescriptor)
	{
		return array();
	}


	function Validate()
	{
		$result = true;
		$message = '';
		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'email':
                  if (ffUtilityFunctions::def($this->Value) &&
                      ! preg_match("/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i", $this->Value[1]))
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

}

?>
