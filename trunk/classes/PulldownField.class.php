<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbPulldownField extends fbFieldBase {

	var $optionCount;
	var $optionAdd;

	function fbPulldownField(&$form_ptr, &$params)
	{
       $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'PulldownField';
		$this->DisplayInForm = true;
		$this->NonRequirableField = false;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->ValidationTypes = array(
            );
        $this->optionAdd = 0;
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

	function DoOptionAdd(&$params)
	{
		$this->optionAdd = 1;
	}

	function DoOptionDelete(&$params)
	{
		$delcount = 0;
		foreach ($params as $thisKey=>$thisVal)
			{
			if (substr($thisKey,0,4) == 'del_')
				{
				$this->RemoveOptionElement('option_name', $thisVal - $delcount);
				$this->RemoveOptionElement('option_value', $thisVal - $delcount);
				$delcount++;
				}
			}
	}

	function countAddresses()
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
		$mod = $this->form_ptr->module_ptr;

		// why all this? Associative arrays are not guaranteed to preserve
		// order, except in "chronological" creation order.
		$sorted =array();
		if ($this->GetOption('select_one','') != '')
			{
			$sorted[' '.$this->GetOption('select_one','')]='';
			}
		else
			{
			$sorted[' '.$mod->Lang('select_one')]='';
			}
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
		return $mod->CreateInputDropdown($id, '_'.$this->Id, $sorted, -1, $this->Value);
	}



    function StatusInfo()
	{
		$mod = $this->form_ptr->module_ptr;
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
		$mod = $this->form_ptr->module_ptr;

		$this->countAddresses();
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
            		$mod->CreateInputText($formDescriptor, 'opt_option_name[]',$this->GetOptionElement('option_name',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_option_value[]',$this->GetOptionElement('option_value',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputCheckbox($formDescriptor, 'del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$dests .= '</table>';
		$main = array();
		$adv = array();
		array_push($main,array($mod->Lang('title_select_one_message'),
			$mod->CreateInputText($formDescriptor, 'opt_select_one',
			$this->GetOption('select_one',$mod->Lang('select_one')),25,128)));
		array_push($main,array($mod->Lang('title_pulldown_details'),$dests));
		return array('main'=>$main,'adv'=>$adv);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = $this->form_ptr->module_ptr;
		// remove the "required" field
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
		if (count($advArray) == 0)
			{
			$advArray[0]->title = $mod->Lang('tab_advanced');
			$advArray[0]->input = $mod->Lang('title_no_advanced_options');
			}
	}

	function GetHumanReadableValue()
	{
		$mod = $this->form_ptr->module_ptr;
		if ($this->HasValue())
			{
			return $this->GetOptionElement('option_value',($this->Value-1));
			}
		else
			{
			return $mod->Lang('unspecified');
			}	
	}
	
}
?>