<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbCheckboxGroupField extends fbFieldBase {

	var $boxCount;
	
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


/*
	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->CreateInputText($id, '_'.$this->Id,
			htmlspecialchars($this->Value, ENT_QUOTES),
            $this->GetOption('length')<25?$this->GetOption('length'):25,
            $this->GetOption('length'),
            $this->form_ptr->GetAttr('name_as_id','0')=='1'?'id="'.$this->Name.'"':'');
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
		$optVals = $this->GetOptionByKind('checkbox');
        $dispRows = count($optVals);
        if ($this->mod_globals->UseCSS)
        	{
        	echo "<div";
        	}
        else
        	{
        	echo "<table";
        	}
        if (strlen($this->CSSClass)>0)
        	{
        	echo " class=\"".$this->CSSClass."\"";
        	}
        echo ">";

        for($i=0;$i<$dispRows;$i++)
        	{
        	if ($i%2 == 0)
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "<div class=\"left\">";
        			}
        		else
        			{
        			echo "<tr><td class=\"left\">";
        			}
        		}
        	if (is_array($this->Value))
        	   {
        	  
                $index = array_search($optVals[$i]->OptionId, $this->Value);
        	   	if ($this->Value[$index] == $optVals[$i]->OptionId)
        	   	   {
                    echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, $optVals[$i]->OptionId, $this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   	   }
        	   	else
        	   	   {
        	   	   echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, '',$this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   	   }
        	   }
        	else
        	   {
        	   echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   }
          echo $optVals[$i]->Name;
        	if ($i%2 == 0)
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "</div><div class=\"right\">";
        			}
        		else
        			{
        			echo "</td><td class=\"right\">";
        			}
        		}
        	else
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "</div>\n";
        			}
        		else
        			{
        			echo "</td></tr>\n";
        			}
        		}
          }
          if ($i%2 != 0)
          {
          	if ($this->mod_globals->UseCSS)
          		{
          		echo "</div>\n";
          		}
          	else
          		{
          		echo "</td></tr>\n";
          		}
          }
          if ($this->mod_globals->UseCSS)
          	{
          	echo "</div>";
          	}
          else
          	{
          	echo "</table>";
          	}
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			if (is_array($this->Value))
				{
				$val = '';
				foreach($this->Value as $tv)
					{
					$boxOpt = $this->GetOptionById($tv);
					$val .= $boxOpt[0]->Value.", ";
					}
				return rtrim($val,', ');
				}
			else
				{
				$boxOpt = $this->GetOptionById($this->Value);
				return $boxOpt[0]->Value;
				}
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}
*/


	function DoOptionAdd(&$params)
	{
		$this->boxCount += 5;
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$this->countBoxes();

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

		return array('main'=>$main,'adv'=>array());
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
error_log('['.$names[$i].']['.$checked[$i].']');
			if ($names[$i] == '' && $checked[$i] == '' )
				{
				array_splice($names, $i, 1);
				array_splice($checked, $i, 1);
				array_splice($unchecked, $i, 1);
				$i--;
				}
			}

error_log('Count of names: '.count($names));
error_log('count of opt: '.count($this->GetOption('box_name')));
		$this->countBoxes();
	}

/*


	function RenderAdminForm($formDescriptor)
	{
        $optVals = $this->GetOptionByKind('checkbox');
        $ret = '<table><tr><th>'.$this->mod_globals->Lang('title_checkbox_name').
            '</th><th>'.$this->mod_globals->Lang('title_submitted_value').'</th></tr>';
        $dispRows = count($optVals)+5;
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'checkboxname[]',
				ffUtilityFunctions::def($optVals[$i]->Name)?$this->NerfHTML($optVals[$i]->Name):'',25);
			$ret .= '</td><td>';
			$ret .= CMSModule::CreateInputText($formDescriptor, 'checkboxvalue[]',
				ffUtilityFunctions::def($optVals[$i]->Value)?$this->NerfHTML($optVals[$i]->Value):'',25);
			$ret .= '</td></tr>';
        	}
        $ret .= '</table>';
		return array($this->mod_globals->Lang('title_checkbox_details').':'=>$ret);
	}
*/

	function Validate()
	{
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'checked':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_check_something').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
