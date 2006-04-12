<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbCheckboxGroupField extends fbFieldBase {

	var $boxCount;
	var $boxAdd;
	
	function fbCheckboxGroupField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'CheckboxGroupField';
		$this->DisplayInForm = true;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->NonRequirableField = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            $mod->Lang('validation_at_least_one')=>'checked'
            );
        $this->boxAdd = 0;
	}

	function countBoxes()
	{
			$tmp = &$this->GetOptionRef('box_name','');
			if (is_array($tmp))
				{
	        	$this->boxCount = count($tmp);
	        	}
	        elseif ($tmp != '')
	        	{
	        	$this->boxCount = 1;
	        	}
	        else
	        	{
	        	$this->boxCount = 0;
	        	}
	}

    function StatusInfo()
	{
        $mod = $this->form_ptr->module_ptr;
//		$boxCount= count($this->GetOption('boxes'));
		$this->countBoxes();
		$ret = $mod->Lang('boxes',$this->boxCount);
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}

	function GetOptionAddButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('add_checkboxes');
	}

	function GetOptionDeleteButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('delete_checkboxes');
	}



	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		$names = &$this->GetOptionRef('box_name');
		$fieldDisp = '';
		for ($i=0;$i<count($names);$i++)
			{
			$label = '';
			if (strlen($names[$i]) > 0)
				{
				$label = '&nbsp;<label for="'.$id.'_'.$this->Id.'[]">'.$names[$i].'</label>';
				}
			$fieldDisp .= $mod->CreateInputCheckbox($id, '_'.$this->Id.'[]', $i,
				$this->Value !== false?$this->FindArrayValue($i):'-1').$label;
			}			
		return $fieldDisp;
	}

/*	function SetStoredValue($valStr)
	{
		$form = $this->form_ptr;
error_log($form->GetAttr('list_delimiter',','));
		$vals = explode($form->GetAttr('list_delimiter',','),$valStr);
		foreach ($vals as $thisVal)
			{
			error_log($thisVal);
			$this->SetValue($thisVal);
			}
	}
*/

	function GetHumanReadableValue()
	{
		$form = $this->form_ptr;
		$names = &$this->GetOptionRef('box_name');
		$checked = &$this->GetOptionRef('box_checked');
		$unchecked = &$this->GetOptionRef('box_unchecked');
		$fieldRet = array();
		for ($i=0;$i<count($names);$i++)
			{
			if ($this->FindArrayValue($i) === false)
				{
				array_push($fieldRet, $unchecked[$i]);
				}
			else
				{
				array_push($fieldRet, $checked[$i]);
				}
			}
		return join($form->GetAttr('list_delimiter',','),$fieldRet);			
	}


	function DoOptionAdd(&$params)
	{
		$this->boxAdd = 2;
	}

	function DoOptionDelete(&$params)
	{
		$delcount = 0;
		foreach ($params as $thisKey=>$thisVal)
			{
			if (substr($thisKey,0,4) == 'del_')
				{
				$this->RemoveOptionElement('box_name', $thisVal - $delcount);
				$this->RemoveOptionElement('box_checked', $thisVal - $delcount);
				$this->RemoveOptionElement('box_unchecked', $thisVal - $delcount);
				$delcount++;
				}
			}
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;

		$this->countBoxes();
		if ($this->boxAdd > 0)
			{
			$this->boxCount += $this->boxAdd;
			$this->boxAdd = 0;
			}
		$boxes = '<table><tr><th>'.$mod->Lang('title_checkbox_label').'</th><th>'.
			$mod->Lang('title_checked_value').'</th><th>'.
			$mod->Lang('title_unchecked_value').'</th><th>'.
			$mod->Lang('title_delete').'</th></tr>';


		for ($i=0;$i<($this->boxCount>1?$this->boxCount:1);$i++)
			{
			$boxes .= '<tr><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_box_name[]',$this->GetOptionElement('box_name',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_box_checked[]',$this->GetOptionElement('box_checked',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_box_unchecked[]',$this->GetOptionElement('box_unchecked',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputCheckbox($formDescriptor, 'del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$boxes .= '</table>';
		$main = array(
			array($mod->Lang('title_checkbox_details'),$boxes)
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
		$names = &$this->GetOptionRef('box_name');
		$checked = &$this->GetOptionRef('box_checked');
		$unchecked = &$this->GetOptionRef('box_unchecked');
		for ($i=0;$i<count($names);$i++)
			{
			if ($names[$i] == '' && $checked[$i] == '' )
				{
				$this->RemoveOptionElement('box_name', $i);
				$this->RemoveOptionElement('box_checked', $i);
				$this->RemoveOptionElement('box_unchecked', $i);
				$i--;
				}
			}
		$this->countBoxes();
	}

	function Validate()
	{
		$mod = $this->form_ptr->module_ptr;
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
		  	           $message = $mod->Lang('please_check_something',$this->Name);
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
