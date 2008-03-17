<?php
/* 
   FormBuilder. Copyright (c) 2005-2008 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2008 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionFormBrowser extends fbFieldBase {

	var $approvedBy;

	function fbDispositionFormBrowser(&$form_ptr, &$params)
	{
      $this->fbFieldBase($form_ptr, $params);
      $mod = &$form_ptr->module_ptr;
		$this->Type = 'DispositionFormBrowser';
		$this->IsDisposition = true;
		$this->NonRequirableField = true;
		$this->DisplayInForm = true;
		$this->DisplayInSubmission = false;
		$this->HideLabel = 1;
		$this->NeedsDiv = 0;
		$this->approvedBy = '';
		$this->sortable = false;
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->Value === false)
			{
			return '';
			}
		return $mod->CreateInputHidden($id, 'fbrp__'.$this->Id,	
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
	
	function DecodeReqId($theVal)
	{
		$tmp = base64_decode($theVal);
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

		$decval = base64_decode($val);
   
		if ($val === false)
			{
			// no value set, so we'll leave value as false
			}
		elseif (strpos($decval,'_') === false)
			{
			// unencrypted value, coming in from previous response
			$this->Value = $val;
			}
		else
			{
			// encrypted value coming in from a form, so we'll update.
			$this->Value = $this->DecodeReqId($val);
			}
	}
	
	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$form = &$this->form_ptr;
		$fields = &$form->GetFields();
		$fieldlist = array($mod->Lang('none')=>'-1');
		$main = array();
		$adv = array();
		for ($i=0;$i<count($fields);$i++)
			{
			if ($fields[$i]->DisplayInSubmission())
				{
				$fieldlist[$fields[$i]->GetName()] = $fields[$i]->GetId();
				}
			}
		for ($i=1;$i<6;$i++)
			{
			if ($this->GetOption('sortfield'.$i,'-1') == '-1')
				{
				array_push($main,
					array($mod->Lang('title_sortable_field',array($i)),
						$mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_sortfield'.$i, $fieldlist, -1,
					$this->GetOption('sortfield'.$i,-1))
					));
				}
			else
				{
				$fname = array_search($this->GetOption('sortfield'.$i),$fieldlist);
				array_push($main,
					array($mod->Lang('title_sortable_field',array($i)),
						$mod->CreateInputHidden($formDescriptor, 'fbrp_opt_sortfield'.$i, $this->GetOption('sortfield'.$i)).
						$mod->Lang('value_set',array($fname))
					));
				}
			}
		return array('main'=>$main,'adv'=>$adv);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$this->HiddenDispositionFields($mainArray, $advArray);
	}

    // Write To the Database
	function DisposeForm($returnid)
	{
		$form = &$this->form_ptr;
		$form->StoreResponseXML(($this->Value?$this->Value:-1),
			$this->approvedBy,
			$this->getSortFieldVal(1),
			$this->getSortFieldVal(2),
			$this->getSortFieldVal(3),
			$this->getSortFieldVal(4),
			$this->getSortFieldVal(5)
			);
		return array(true,'');	   
	}
	
	function getSortFieldVal($sortFieldNumber)
	{
		$form = &$this->form_ptr;
		$val = "";
		if ($this->GetOption('sortfield'.$sortFieldNumber,'-1') != '-1')
			{
			$afield = &$form->GetFieldById($this->GetOption('sortfield'.$sortFieldNumber));
			$val = $afield->GetHumanReadableValue();
			}
		if (strlen($val) > 80)
			{
			$val = substr($val,0,80);
			}
		return $val;
	}

}

?>
