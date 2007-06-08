<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbCheckboxField extends fbFieldBase {

	function fbCheckboxField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type =  'CheckboxField';
		$this->DisplayInForm = true;
		$this->NonRequirableField = true;
		$this->Required = false;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            $mod->Lang('validation_must_check')=>'checked'
            );
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		$label = '';
		if (strlen($this->GetOption('label','')) > 0)
			{
			$label = '&nbsp;<label for="'.$id.'_'.$this->Id.'">'.$this->GetOption('label').'</label>';
			}
		if ($this->Value === false && $this->GetOption('is_checked','0')=='1')
			{
			$this->Value = 't';
			}
		return $mod->CreateInputCheckbox($id, '_'.$this->Id, 't',$this->Value,' id="'.$id.'_'.$this->Id.'"').$label;
	}

	function GetHumanReadableValue()
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->Value === false)
			{
			return $this->GetOption('unchecked_value',$mod->Lang('value_unchecked'));
			}
		else
			{
			return $this->GetOption('checked_value',$mod->Lang('value_checked'));
			}	
	}


	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = $this->form_ptr->module_ptr;
		// remove the "required" field, since this can only be done via validation
		$reqIndex = -1;
		for ($i=0;$i<count($mainArray);$i++)
			{
			if ($mainArray[$i]->title == $mod->Lang('title_field_required'))
				{
				$reqIndex = $i;
				}
			}
		if ($reqIndex != -1)
			{
			array_splice($mainArray, $reqIndex,1);
			}
	}



	function StatusInfo()
	{
		if (strlen($this->ValidationType)>0)
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_checkbox_label'),
            		$mod->CreateInputText($formDescriptor, 'opt_label',
            		$this->GetOption('label',''),25,255)),
            array($mod->Lang('title_checked_value'),
            		$mod->CreateInputText($formDescriptor, 'opt_checked_value',
            		$this->GetOption('checked_value',$mod->Lang('value_checked')),25,255)),
            array($mod->Lang('title_unchecked_value'),
            		$mod->CreateInputText($formDescriptor, 'opt_unchecked_value',
            		$this->GetOption('unchecked_value',$mod->Lang('value_unchecked')),25,255)),
			array($mod->Lang('title_default_set'),
				$mod->CreateInputCheckbox($formDescriptor, 'opt_is_checked', '1', $this->GetOption('is_checked','0')))
				);
		$adv = array(
		);
		return array('main'=>$main,'adv'=>$adv);
	}

	function Validate()
	{
		$mod = &$this->form_ptr->module_ptr;
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'checked':
		  	       if ($this->Value === false)
		  	           {
		  	           $result = false;
		  	           $message = $mod->Lang('you_must_check',$this->GetOption('label',''));
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
