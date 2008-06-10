<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbMultiselectField extends fbFieldBase {

	var $optionCount;
	var $optionAdd;

	function fbMultiselectField(&$form_ptr, &$params)
	{
       $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type = 'MultiselectField';
		$this->DisplayInForm = true;
		$this->NonRequirableField = false;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->ValidationTypes = array(
            );
        $this->optionAdd = 0;
        $this->sortable = false;
	}

	function GetOptionAddButton()
	{
		$mod = &$this->form_ptr->module_ptr;
		return $mod->Lang('add_options');
	}

	function GetOptionDeleteButton()
	{
		$mod = &$this->form_ptr->module_ptr;
		return $mod->Lang('delete_options');
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
			if (substr($thisKey,0,9) == 'fbrp_del_')
				{
				$this->RemoveOptionElement('option_name', $thisVal - $delcount);
				$this->RemoveOptionElement('option_value', $thisVal - $delcount);
				$delcount++;
				}
			}
	}

	function countItems()
	{
			$tmp = &$this->GetOptionRef('option_name');
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

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		$js = $this->GetOption('javascript','');

		// why all this? Associative arrays are not guaranteed to preserve
		// order, except in "chronological" creation order.
		$sorted =array();
		$subjects = &$this->GetOptionRef('option_name');

		if (count($subjects) > 1)
			{
			for($i=0;$i<count($subjects);$i++)
				{
				$sorted[$subjects[$i]]=($i+1);
				}
			}
		else
			{
			$sorted[$subjects] = '1';
			}
		if ($this->Value === false)
			{
			$val = array();
			}
		elseif (!is_array($this->Value))
			{
			$val = array($this->Value);
			}
		else
			{
			$val = $this->Value;
			}
		return $mod->CreateInputSelectList($id, 'fbrp__'.$this->Id.'[]', $sorted,$val, $this->GetOption('lines','3'),
         'id="'.$id. '_'.$this->Id.'" '.$js);
	}



    function StatusInfo()
	{
		$mod = &$this->form_ptr->module_ptr;
		$opt = $this->GetOption('option_name','');
		
		if (is_array($opt))
		  {
		      $num = count($opt);
		  }
		elseif ($opt != '')
			{
			$num = 1;
			}
		else
		  {
          $num = 0;
          }
         $ret= $mod->Lang('options',$num);
        return $ret;
	}
	
	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;

		$this->countItems();
		if ($this->optionAdd > 0)
			{
			$this->optionCount += $this->optionAdd;
			$this->optionAdd = 0;
			}
		$dests = '<table class="module_fb_table"><tr><th>'.$mod->Lang('title_option_name').'</th><th>'.
			$mod->Lang('title_option_value').'</th><th>'.
			$mod->Lang('title_delete').'</th></tr>';


		for ($i=0;$i<($this->optionCount>1?$this->optionCount:1);$i++)
			{
			$dests .=  '<tr><td>'.
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_option_name[]',$this->GetOptionElement('option_name',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_option_value[]',$this->GetOptionElement('option_value',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputCheckbox($formDescriptor, 'fbrp_del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$dests .= '</table>';
		$main = array();
		$adv = array();
		array_push($main,array($mod->Lang('title_lines_to_show'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_lines',$this->GetOption('lines','3'),10,10)));
		array_push($main,array($mod->Lang('title_multiselect_details'),$dests));
		return array('main'=>$main,'adv'=>$adv);
	}

	function GetHumanReadableValue($as_string=true)
	{
		$mod = &$this->form_ptr->module_ptr;
		$form = &$this->form_ptr;
		$vals = &$this->GetOptionRef('option_value');
		if ($this->HasValue())
			{
			$fieldRet = array();
			if (! is_array($this->Value))
				{
				$this->Value = array($this->Value);
				}
			foreach ($this->Value as $thisOne)
				{
				array_push($fieldRet,$vals[$thisOne - 1]);
				}
			if ($as_string)
				{
				return join($form->GetAttr('list_delimiter',','),$fieldRet);
				}
			else
				{
				return array($fieldRet);
				}			
			}
		else
			{
			if ($as_string)
				{
				return $mod->Lang('unspecified');
				}
			else
				{
				return array($mod->Lang('unspecified'));
				}
			}
	
	}

   function OptionFromXML($theArray)
	{
		foreach ($theArray['children'] as $thisChildKey=>$thisChildVal)
			{
			if ($thisChildVal['name']=='name')
				{
				$this->PushOptionElement('option_name',$thisChildVal['content']);
				}
			elseif ($thisChildVal['name']=='selected_value')
				{
				$this->PushOptionElement('option_value',$thisChildVal['content']);	
				}
			elseif ($thisChildVal['name']=='selected' && $thisChildVal['content'] == 'true')
				{
				if (! is_array($this->Value))
					{
					$this->Value = array();
					}
				array_push($this->Value,count($this->Options['option_name']));
				}
			}
	}


	function OptionsAsXML()
	{
		$mod = &$this->form_ptr->module_ptr;
		$form = &$this->form_ptr;
		$vals = &$this->GetOptionRef('option_value');
		$xmlstr = "";
		if (! is_array($this->Value))
			{
			$test = array($this->Value);
			}
		else
			{
			$test = $this->Value;
			}
		for ($i=1;$i<=count($vals);$i++)
			{
			$xmlstr .= "\t\t\t<option>\n";
			$xmlstr .= "\t\t\t\t<name><![CDATA[".$this->GetOptionElement('option_name',$i-1)."]]></name>\n";
			$xmlstr .= "\t\t\t\t<selected_value><![CDATA[".$this->GetOptionElement('option_value',$i-1)."]]></selected_value>\n";
			$isselected = "false";
			if (in_array($i-1,$test))
				{
				$isselected = "true";
				}
			$xmlstr .= "\t\t\t\t<selected>$isselected</selected>\n";
			$xmlstr .= "\t\t\t</option>\n";	
			}
		return $xmlstr;
	}

	
}
?>
