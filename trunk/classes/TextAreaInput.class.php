<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffTextAreaInput extends ffInput {

	function ffTextAreaInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'TextAreaInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_text_area');
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_not_empty')=>'nonempty'
            );

	}

	function WriteToPublicForm($id, &$params, $return_id)
	{            
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
       echo CMSModule::CreateTextArea(false, $id, $this->NerfHTML($this->Value), $this->Alias, 'user',$this->mod_globals->UseIDAndName?$this->Alias:'');            
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}


	function StatusInfo()
	{
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return '';
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
		  	   case 'nonempty':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_enter_a_value').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
