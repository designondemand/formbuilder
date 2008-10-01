<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbFromEmailAddressAgainField extends fbFieldBase {

	function fbFromEmailAddressAgainField(&$form_ptr, &$params)
	{
		$this->fbFieldBase($form_ptr, $params);
		$mod = &$form_ptr->module_ptr;
		$this->Type = 'FromEmailAddressAgainField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
			$mod->Lang('validation_email_address')=>'email',
			);
		$this->modifiesOtherFields = false;
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		$js = $this->GetOption('javascript','');

		return $mod->CreateInputText($id, 'fbrp__'.$this->Id,
			htmlspecialchars($this->Value, ENT_QUOTES),
			25,128,$js);
	}

	function StatusInfo()
	{
		$mod = &$this->form_ptr->module_ptr;
		return $mod->Lang('title_field_id') . ': ' . $this->GetOption('field_to_validate','');
	}
	
	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$flds = &$this->form_ptr->GetFields();
		$opts = array();
		foreach ($flds as $tf)
			{
			$opts[$tf->GetName()]=$tf->GetName();
			}
		$main = array(
			array(
				$mod->Lang('title_field_to_validate'),
			$mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_field_to_validate', $opts, -1, $this->GetOption('field_to_validate'))
			)
		);

		return array('main'=>$main);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = &$this->form_ptr->module_ptr;
		// remove the "javascript" field
		$hideIndex = -1;
		for ($i=0;$i<count($advArray);$i++)
		{
			if ($advArray[$i]->title == $mod->Lang('title_field_javascript'))
			{
				$hideIndex = $i;
			}
		}
		if ($hideIndex != -1)
		{
			array_splice($advArray, $hideIndex,1);
		}
		if (count($advArray) == 0)
		{
			$advArray[0]->title = $mod->Lang('tab_advanced');
			$advArray[0]->input = $mod->Lang('title_no_advanced_options');
		}
	}

	function Validate()
	{
		$this->validated = true;
		$this->validationErrorText = '';
		$mod = &$this->form_ptr->module_ptr;

		$field_to_validate = $this->GetOption('field_to_validate','');

		if ($field_to_validate != '')
		{
			foreach ($this->form_ptr->Fields as $one_field)
			{
				if ($one_field->Name == $field_to_validate)
				{
					$value = $one_field->GetValue();
					if ($value != $this->Value)
					{
						$this->validated = false;
						$this->validationErrorText = $mod->Lang('email_address_does_not_match', $field_to_validate);
					}
				}
			}
		}
		return array($this->validated, $this->validationErrorText);
	}
}

?>
