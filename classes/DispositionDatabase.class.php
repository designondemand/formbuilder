<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbDispositionDatabase extends fbFieldBase {

	function fbDispositionDatabase(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'DispositionDatabase';
		$this->IsDisposition = true;
		$this->SpecialInput = true;
		$this->DisplayInForm = false;
	}

	function StatusInfo()
	{
		 return '';
	}
	
	function SetValue($val)
	{
		$this->Value = $val;
	}
	
	function SetResponseId($resp_id)
	{
		$this->SetOption('response_id',$resp_id);
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		return array();
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$this->HiddenDispositionFields($mainArray, $advArray);
	}

    // Write To the Database
	function DisposeForm()
	{
		$form = $this->form_ptr;
		//$mod = $this->form_ptr->module_ptr;
		$form->StoreResponse($this->GetOption('response_id',-1));
		return array(true,'');	   
	}

	function Validate()
	{
		return array(true, '');
	}

}

?>
