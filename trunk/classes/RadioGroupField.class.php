<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbRadioGroupField extends fbFieldBase {

	var $optionCount;
	var $optionAdd;
	
	function fbRadioGroupField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'RadioGroupField';
		$this->DisplayInForm = true;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->NonRequirableField = true;
		$this->ValidationTypes = array(
            );
        $this->optionAdd = 0;
        $this->hasMultipleFormComponents = true;
	}

	function countBoxes()
	{
			$tmp = &$this->GetOptionRef('button_name');
			if (is_array($tmp))
				{
	        	$this->optionCount = count($tmp);
	        	}
	        elseif ($tmp !== false)
	        	{
	        	$this->optionCount = 1;
	        	}
	        else
	        	{
	        	$this->optionCount = 0;
	        	}
	}

    function StatusInfo()
	{
        $mod = $this->form_ptr->module_ptr;
		$this->countBoxes();
		$ret = $mod->Lang('options',$this->optionCount);
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}

	function GetOptionAddButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('add_options');
	}

	function GetOptionDeleteButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('delete_options');
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		$names = &$this->GetOptionRef('button_name');
		$fieldDisp = array();
		for ($i=0;$i<count($names);$i++)
			{
			$label = '';
			$thisBox = new stdClass();
			if (strlen($names[$i]) > 0)
				{
				$thisBox->name = '<label for="'.$id.'_'.$this->Id.'">'.$names[$i].'</label>';
				}
			$check_val = false;
			if ($this->Value !== false)
				{
				$check_val = $this->FindArrayValue($i);
				}
			$thisBox->input = '<input type="radio" name="'.$id.'_'.$this->Id.'" value="'.$i.'"';
			if ($check_val)
				{
				$thisBox->input .= ' checked="checked"';
				}
			$thisBox->input .= ' />';
			array_push($fieldDisp, $thisBox);
			}			
		return $fieldDisp;
	}

	function GetHumanReadableValue()
	{
		$form = $this->form_ptr;
		if ($this->HasValue())
			{
			return $this->GetOptionElement('button_checked',$this->Value);
			}
		else
			{
			return $mod->Lang('unspecified');
			}	
	}


	function DoOptionAdd(&$params)
	{
		$this->optionAdd = 2;
	}

	function DoOptionDelete(&$params)
	{
		$delcount = 0;
		foreach ($params as $thisKey=>$thisVal)
			{
			if (substr($thisKey,0,4) == 'del_')
				{
				$this->RemoveOptionElement('button_name', $thisVal - $delcount);
				$this->RemoveOptionElement('button_checked', $thisVal - $delcount);
				$delcount++;
				}
			}
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;

		$this->countBoxes();
		if ($this->optionAdd > 0)
			{
			$this->optionCount += $this->optionAdd;
			$this->optionAdd = 0;
			}
		$boxes = '<table class="module_fb_table"><tr><th>'.$mod->Lang('title_checkbox_label').'</th><th>'.
			$mod->Lang('title_checked_value').'</th><th>'.
			$mod->Lang('title_delete').'</th></tr>';


		for ($i=0;$i<($this->optionCount>1?$this->optionCount:1);$i++)
			{
			$boxes .= '<tr><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_button_name[]',$this->GetOptionElement('button_name',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_button_checked[]',$this->GetOptionElement('button_checked',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputCheckbox($formDescriptor, 'del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$boxes .= '</table>';
		$main = array(
			array($mod->Lang('title_radiogroup_details'),$boxes)
		);
		$adv = array();
		return array('main'=>$main,'adv'=>$adv);
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


	function PostAdminSubmitCleanup()
	{
		$names = &$this->GetOptionRef('button_name');
		$checked = &$this->GetOptionRef('button_checked');
		for ($i=0;$i<count($names);$i++)
			{
			if ($names[$i] == '' && $checked[$i] == '' )
				{
				$this->RemoveOptionElement('button_name', $i);
				$this->RemoveOptionElement('button_checked', $i);
				$i--;
				}
			}
		$this->countBoxes();
	}
}

?>
