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
        $mod = &$form_ptr->module_ptr;
		$this->Type = 'CheckboxGroupField';
		$this->DisplayInForm = true;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->NonRequirableField = true;
		$this->ValidationTypes = array(
            );
        $this->boxAdd = 0;
        $this->hasMultipleFormComponents = true;
        $this->sortable = false;
	}

	function countBoxes()
	{
			$tmp = &$this->GetOptionRef('box_name');
			if (is_array($tmp))
				{
	        	$this->boxCount = count($tmp);
	        	}
	        elseif ($tmp !== false)
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
        $mod = &$this->form_ptr->module_ptr;
		$this->countBoxes();
		$ret = $mod->Lang('boxes',$this->boxCount);
		 return $ret;
	}

	function GetOptionAddButton()
	{
		$mod = &$this->form_ptr->module_ptr;
		return $mod->Lang('add_checkboxes');
	}

	function GetOptionDeleteButton()
	{
		$mod = &$this->form_ptr->module_ptr;
		return $mod->Lang('delete_checkboxes');
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		$names = &$this->GetOptionRef('box_name');
		$is_set = &$this->GetOptionRef('box_is_set');
		$js = $this->GetOption('javascript','');
		if (! is_array($names))
			{
			$names = array($names);
			}	
		if (! is_array($is_set))
			{
			$is_set = array($is_set);
			}
		$fieldDisp = array();
		for ($i=0;$i<count($names);$i++)
			{
			$label = '';
			$thisBox = new stdClass();
			if (strlen($names[$i]) > 0)
				{
				$thisBox->name = '<label for="'.$id.'fbrp__'.$this->Id.'_'.$i.'">'.$names[$i].'</label>';
				$thisBox->title = $names[$i];
				}
			$check_val = false;
			if ($this->Value !== false)
				{
				$check_val = $this->FindArrayValue($i-1);
				}
			else
				{
				if (isset($is_set[$i]) && $is_set[$i] == 'y')
					{
					$check_val = true;
					}
				}
			$thisBox->input = $mod->CreateInputCheckbox($id, 'fbrp__'.$this->Id.'[]', ($i+1),
				$check_val !== false?($i+1):'-1',' id="'.$id.'fbrp__'.$this->Id.'_'.$i.'" '.$js);

			array_push($fieldDisp, $thisBox);
			}			
		return $fieldDisp;
	}

	function GetHumanReadableValue($as_string=true)
	{
		$form = &$this->form_ptr;
		$names = &$this->GetOptionRef('box_name');
		if (! is_array($names))
			{
				$names = array($names);
			}		
		$checked = &$this->GetOptionRef('box_checked');
		if (! is_array($checked))
			{
				$checked = array($checked);
			}
		$unchecked = &$this->GetOptionRef('box_unchecked');
		if (! is_array($unchecked))
			{
				$unchecked = array($unchecked);
			}
		$fieldRet = array();
		for ($i=1;$i<=count($names);$i++)
			{
				if ($this->FindArrayValue($i) === false)
					{
						if ($this->GetOption('no_empty','0') != '1' && isset($unchecked[$i-1]) && trim($unchecked[$i-1]) != '' )
							{
							$fieldRet[] = $unchecked[$i-1];
							}
					}
				else
					{
						if( isset($checked[$i-1]) && trim($checked[$i-1]) != '' )
							$fieldRet[] = $checked[$i-1];
					}
			}
		if ($as_string)
			{
			return join($form->GetAttr('list_delimiter',','),$fieldRet);
			}
		else
			{
			return $fieldRet;
			}
	}

	function DoOptionAdd(&$params)
	{
		$this->boxAdd = 1;
	}

	function DoOptionDelete(&$params)
	{
		$delcount = 0;
		foreach ($params as $thisKey=>$thisVal)
			{
			if (substr($thisKey,0,9) == 'fbrp_del_')
				{
				$this->RemoveOptionElement('box_name', $thisVal - $delcount);
				$this->RemoveOptionElement('box_checked', $thisVal - $delcount);
				$this->RemoveOptionElement('box_unchecked', $thisVal - $delcount);
				$this->RemoveOptionElement('box_is_set', $thisVal - $delcount);
				$delcount++;
				}
			}
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$yesNo = array($mod->Lang('no')=>'n',$mod->Lang('yes')=>'y');

		$this->countBoxes();
		if ($this->boxAdd > 0)
			{
			$this->boxCount += $this->boxAdd;
			$this->boxAdd = 0;
			}
		$boxes = '<table class="module_fb_table"><tr><th>'.$mod->Lang('title_checkbox_label').'</th><th>'.
			$mod->Lang('title_checked_value').'</th><th>'.
			$mod->Lang('title_unchecked_value').'</th><th>'.
			$mod->Lang('title_default_set').'</th><th>'.
			$mod->Lang('title_delete').'</th></tr>';


		for ($i=0;$i<($this->boxCount>1?$this->boxCount:1);$i++)
			{
			$boxes .= '<tr><td>'.
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_box_name[]',$this->GetOptionElement('box_name',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_box_checked[]',$this->GetOptionElement('box_checked',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_box_unchecked[]',$this->GetOptionElement('box_unchecked',$i),25,128).
            		'</td><td>'.            		    
            		$mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_box_is_set[]', $yesNo, -1, $this->GetOptionElement('box_is_set',$i)).
            		
            		
            		'</td><td>'.
//CreateInputCheckbox($id, $name, $value='', $selectedvalue='',
            		$mod->CreateInputCheckbox($formDescriptor, 'fbrp_del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$boxes .= '</table>';
		$main = array(
			array($mod->Lang('title_dont_submit_unchecked'),
				$mod->CreateInputHidden($formDescriptor,'fbrp_opt_no_empty','0').
					$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_no_empty','1',$this->GetOption('no_empty','0')).
					$mod->Lang('title_dont_submit_unchecked_help'),
	           ),
			array($mod->Lang('title_checkbox_details'),$boxes)
		);
		$adv = array();
		return array('main'=>$main,'adv'=>$adv);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = &$this->form_ptr->module_ptr;
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
		$is_set = &$this->GetOptionRef('box_is_set');
		for ($i=0;$i<count($names);$i++)
			{
			if ($names[$i] == '' && $checked[$i] == '' )
				{
				$this->RemoveOptionElement('box_name', $i);
				$this->RemoveOptionElement('box_checked', $i);
				$this->RemoveOptionElement('box_unchecked', $i);
				$this->RemoveOptionElement('box_is_set', $i);
				$i--;
				}
			}
		$this->countBoxes();
	}
	
	function OptionsAsXML()
	{
		$names = &$this->GetOptionRef('box_name');
		if (! is_array($names))
			{
				$names = array($names);
			}		
		$checked = &$this->GetOptionRef('box_checked');
		if (! is_array($checked))
			{
				$checked = array($checked);
			}
		$unchecked = &$this->GetOptionRef('box_unchecked');
		if (! is_array($unchecked))
			{
				$unchecked = array($unchecked);
			}
		$xmlstr = "";
		for ($i=1;$i<=count($names);$i++)
			{
			$xmlstr .= "\t\t\t<option>\n";
			$xmlstr .= "\t\t\t\t<name><![CDATA[".$names[$i-1]."]]></name>\n";
			$xmlstr .= "\t\t\t\t<checked_value><![CDATA[".$checked[$i-1]."]]></checked_value>\n";
			$xmlstr .= "\t\t\t\t<unchecked_value><![CDATA[".$unchecked[$i-1]."]]></unchecked_value>\n";
			$ischecked = "false";
			if ($this->FindArrayValue($i) !== false)
				{
				$ischecked = "true";
				}
			
			$xmlstr .= "\t\t\t\t<checked>$ischecked</checked>\n";
			$xmlstr .= "\t\t\t</option>\n";
			}

		return $xmlstr;
	}

}

?>
