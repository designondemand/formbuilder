<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffPageBreak extends ffInput {

	function ffPageBreak(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'PageBreak';
		$this->DisplayType = $this->mod_globals->Lang('field_type_page_break');
		$this->Required = false;
		$this->DisplayInForm = false;
		$this->ValidationTypes = array($this->mod_globals->Lang('validation_none')=>'none');
		$this->specialInput = true;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
	}


	function StatusInfo()
	{
		return '';
	}


	function RenderAdminForm($formDescriptor)
	{
		return array();
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
