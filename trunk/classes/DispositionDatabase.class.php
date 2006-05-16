<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionDatabase extends fbFieldBase {

	var $approvedBy;
	var $DDInitialized;
	
	function fbDispositionDatabase(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'DispositionDatabase';
		$this->IsDisposition = true;
		$this->NonRequirableField = true;
		$this->DisplayInForm = true;
		$this->DisplayInSubmission = false;
		$this->HideLabel = 1;
		$this->CodedValue = -1;
		$this->approvedBy = '';
		$this->DDInitialized = 1;
error_log('Disposition Database Init!');
		error_log('On init '.$this->DispositionIsPermitted()?'permitted':'not permitted');
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->CreateInputHidden($id, '_'.$this->Id,	
			$this->EncodeReqId($this->Value));
	}

	function SetApprovalName($name)
	{
		$this->approvedBy = $name;
	}

	function StatusInfo()
	{
		 return '';
	}
	
	function DecodeReqId()
	{
		$tmp = base64_decode($this->EncodedValue);
		$tmp2 = str_replace(session_id(),'',$tmp);
		if (substr($tmp2,0,1) == '_')
			{
			return substr($tmp2,1);
			}
		else
			{
			return -1;
			}
	}
	
	function EncodeReqId($req_id)
	{
		return base64_encode(session_id().'_'.$req_id);
	}
	
	
	function SetValue($val)
	{

error_log('Disposition Database Set Value. incoming: '.$val);
		$decval = base64_decode($val);

error_log($this->DispositionIsPermitted()?'permitted':'not permitted');
error_log('DDInitialized '.$this->DDInitialized);
/* the second part of this next clause is required for the user approval process,
   but breaks  response reload... */
   
		if ($val === false || ! $this->DispositionIsPermitted())
			{
error_log('neeeeeh?');
			// no value set, so we'll leave value as false
			}
		elseif (strpos($decval,'_') === false)
			{
			// unencrypted value, coming in from previous response
			$this->Value = $val;
error_log('nodecval');
			}
		else
			{
			// encrypted value coming in from a form, so we'll update.
			$this->EncodedValue = $val;
			$this->Value = $this->DecodeReqId();
error_log('encr;');
			}
error_log('outgoing: '.$this->Value);
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
	function DisposeForm($returnid)
	{
		$form = $this->form_ptr;
		error_log($this->Value);
		$form->StoreResponse(($this->Value?$this->Value:-1),$this->approvedBy);
		return array(true,'');	   
	}

	function Validate()
	{
		return array(true, '');
	}

}

?>
