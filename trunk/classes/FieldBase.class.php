<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbFieldBase {

    var $Id=-1;
    var $FormId;
    var $Name;
    var $Type;
    var $Required=-1;
    var $OrderBy;
    var $HideLabel=-1;
    
    var $ValidationTypes;
    var $ValidationType;

    var $DisplayInForm;
    var $NonRequirableField;
    var $HasAddOp;
    var $HasDeleteOp;

    var $Value=false;
    var $form_ptr;
    var $Options;
    var $loaded;

	function fbFieldBase(&$form_ptr, &$params)
	{
//	echo "fbBase instantiate";
// debug_display($params);

	   $this->form_ptr = $form_ptr;
	   $mod = $form_ptr->module_ptr;
	   $this->Options = array();
	   if (isset($params['form_id']))
	       {
	       $this->FormId = $params['form_id'];
	       }
	   if (isset($params['field_id']))
	       {
	       $this->Id = $params['field_id'];
	       }
	   if (isset($params['field_name']))
	       {
	       $this->Name = $params['field_name'];
	       }
	   if (isset($params['field_type']))
	       {
	       $this->Type = $params['field_type'];
	       }
	   else
	   	   {
	   	   $this->Type = '';
	   	   }
	   if (isset($params['order_by']))
	       {
	       $this->OrderBy = $params['order_by'];
	       }
	   if (isset($params['hide_label']))
	       {
	       $this->HideLabel = $params['hide_label'];
	       }
	   else if (isset($params['set_from_form']))
	   	   {
	   	   $this->HideLabel = 0;
	   	   }
	   if (isset($params['required']))
	       {
	       $this->Required = $params['required'];
	       }
	   else if (isset($params['set_from_form']))
	   	   {
	   	   $this->Required = 0;
	   	   }
	   if (isset($params['validation_type']))
	       {
	       $this->ValidationType = $params['validation_type'];
	       }
//debug_display($params);
		foreach ($params as $thisParamKey=>$thisParamVal)
		{
	   		if (substr($thisParamKey,0,4) == 'opt_')
	   			{
	   			$thisParamKey = substr($thisParamKey,4);
	   			$this->Options[$thisParamKey] = $thisParamVal;
	   			}
	   	}

//echo '-'.$params['_'.$this->Id].' '.$params['__'.$this->Id];
	   if (isset($params['_'.$this->Id]) &&
	   		(is_array($params['_'.$this->Id]) ||
	   		strlen($params['_'.$this->Id]) > 0))
//	   if (isset($params['_'.$this->Id]))
	   		{
//	   		error_log('Setting '.'_'.$this->Id.' value to '.$params['_'.$this->Id]);
//echo " setting from form<br>";
	   		$this->SetValue($params['_'.$this->Id]);
	   		}
/* new regime!
	   	elseif (isset($params['__'.$this->Id]) &&
	   		(is_array($params['__'.$this->Id]) ||
	   		strlen($params['__'.$this->Id]) > 0))
	   		{
	   		// a response value
//echo " setting from stored response<br>";	   		
	   		$this->SetStoredValue($params['__'.$this->Id]);
	   		}
*/
//	   else {echo 'no value to set for '.'_'.$this->Id;
//	   debug_display($params);
//	   }
	   $this->DisplayInForm = true;
	   $this->IsDisposition = false;
	   $this->ValidationTypes = array($mod->Lang('validation_none')=>'none');
	   $this->loaded = 'not';
	   $this->NonRequirableField = false;
	   $this->HasAddOp = false;
	   $this->HasDeleteOp = false;

	}

	function GetFieldInputId($id, &$params, $returnid)
	{
		return $id.'_'.$this->Id;
	}


	// override me with a form input string or something
	// this should just be the input portion. The title
	// and any wrapping divs will be provided by the form
	// renderer.
	function GetFieldInput($id, &$params, $returnid)
	{
		return '';
	}
	
	// override me with something to show users
	function StatusInfo()
	{
		return '';
	}

	function DebugDisplay()
	{
		$tmp = $this->form_ptr;
		$this->form_ptr = '';
		debug_display($this);
		$this->form_ptr = $tmp;
	}

	function GetId()
	{
		return $this->Id;
	}

	function HasAddOp()
	{
		return $this->HasAddOp;
	}

	// override me, when necessary or useful
	function DoOptionAdd(&$params)
	{
	}

	// override me
	function GetOptionAddButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('add_options');
	}
	
	function HasDeleteOp()
	{
		return $this->HasDeleteOp;
	}

	// override me, when necessary or useful
	function DoOptionDelete(&$params)
	{
	}

	// override me
	function GetOptionDeleteButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('delete_options');
	}

	function GetName()
	{
		return $this->Name;
	}

	function GetOrder()
	{
		return $this->OrderBy;
	}
	
	function SetOrder($order)
	{
		$this->OrderBy = $order;
	}
	
	function GetFieldType()
	{
		return $this->Type;
	}

	function SetFieldType($type)
	{
		return $this->Type = $type;
	}

	
	function IsDisposition()
	{
		return $this->IsDisposition;
	}
	
	function HideLabel()
	{
		return ($this->HideLabel==1?true:false);
	}

	function DisplayInForm()
	{
		return $this->DisplayInForm;
	}

	
	function IsNonRequirableField()
	{
		return $this->NonRequirableField;
	}

	function IsRequired()
	{
		return ($this->Required == 1?true:false);
	}

	function SetRequired($required)
	{
		$this->Required = ($required?1:0);
	}
	
	function GetValidationTypes()
	{
		return $this->ValidationTypes;
	}
	
	function GetValidationType()
	{
		return $this->ValidationType;
	}

	// override me with a displayable type
	function GetDisplayType()
	{
		return $this->form_ptr->module_ptr->Lang('field_type_'.$this->Type);
	}


	function PrePopulateBaseAdminForm($formDescriptor,$disposeOnly=0)
	{
		$mod = $this->form_ptr->module_ptr;
		if ($this->Type == '')
			{
			if ($disposeOnly == 1)
				{
				$typeInput = $mod->CreateInputDropdown($formDescriptor, 'field_type',array_merge(array($mod->Lang('select_type')=>''),$mod->disp_field_types), -1,'', 'onchange="this.form.submit()"');
				}
			else
				{
				$typeInput = $mod->CreateInputDropdown($formDescriptor, 'field_type',array_merge(array($mod->Lang('select_type')=>''),$mod->field_types), -1,'', 'onchange="this.form.submit()"');
				}
			}
		else
			{
			$typeInput = $this->GetDisplayType().$mod->CreateInputHidden($formDescriptor, 'field_type', $this->Type);
			}
		
		$main = array(
			array($mod->Lang('title_field_name'),
					  $mod->CreateInputText($formDescriptor, 'field_name', $this->GetName(), 50)),
		    array($mod->Lang('title_field_type'),$typeInput),
		);
		
		$adv = array();

		// if we know our type, we can load up with additional options
		if ($this->Type != '')
			{
			
			// validation types?
			if (count($this->GetValidationTypes()) > 1)
				{
				$validInput = $mod->CreateInputDropdown($formDescriptor, 'validation_type', $this->GetValidationTypes(), -1, $this->GetValidationType());
				}
			else
				{
				$validInput = $mod->Lang('automatic');
				}
				
			// requirable?
			if (!$this->IsDisposition() && !$this->IsNonRequirableField())
				{
				array_push($main, array($mod->Lang('title_field_required'),$mod->CreateInputCheckbox($formDescriptor, 'required', 1, $this->IsRequired()).$mod->Lang('title_field_required_long')));
				}
				
			array_push($main, array($mod->Lang('title_field_validation'),$validInput));

			array_push($adv, array($mod->Lang('title_hide_label'),$mod->CreateInputCheckbox($formDescriptor, 'hide_label', 1, $this->HideLabel()).$mod->Lang('title_hide_label_long')));

//$this->DebugDisplay();
			if ($this->DisplayInForm())
				{
				array_push($adv,array($mod->Lang('title_field_css_class'),$mod->CreateInputText($formDescriptor, 'opt_css_class', $this->GetOption('css_class'), 50)));
				}
			
			}
		else
			{
			// no advanced options until we know our type
			array_push($adv,array($mod->Lang('tab_advanced'),$mod->Lang('notice_select_type')));
			}
				
		return array('main'=>$main, 'adv'=>$adv);
	}
	
	
    // override me.
    // I return an ugly data structure:
    // It's an associative array with two items, 'main' and 'adv' (for the
    // main and advanced setting tabs).
    // Each of these is an associative array of Title / Input values.
    // The Title will be displayed if it has a length;
    // the "Input" should be a Form input for that field attribute/option
	function PrePopulateAdminForm($formDescriptor)
	{
		return array();
	}

    // override me.
    // This gives you a chance to alter the array contents before
    // they get rendered. 
	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
	}


	// override me as necessary
	function PostAdminSubmitCleanup()
	{
	}

	// clear fields unused by invisible dispositions
	function HiddenDispositionFields(&$mainArray, &$advArray)
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
		// remove the "hide name" field
		$hideIndex = -1;
		for ($i=0;$i<count($advArray);$i++)
			{
			if ($advArray[$i]->title == $mod->Lang('title_hide_label'))
				{
				$hideIndex = $i;
				}
			}
		if ($hideIndex != -1)
			{
			array_splice($advArray, $hideIndex,1);
			}
		// remove the "css" field
		$hideIndex = -1;
		for ($i=0;$i<count($advArray);$i++)
			{
			if ($advArray[$i]->title == $mod->Lang('title_field_css_class'))
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
	

	// override me. Returns an array: first value is a true or false (whether or not
    // the value is valid), the second is a message
	function Validate()
	{
		return array(true,'');
	}


	function GetHumanReadableValue()
	{
		if ($this->Value !== false)
			{
			return $this->Value;
			}
		else
			{
			return $this->form_ptr->GetAttr('unspecified','[unspecified]');
			}
	}


	// override this if you have some unusual format for values,
	// especially if "false" is a valid value!
	function HasValue()
	{
		return ($this->Value !== false);
	}
	
	// probably don't need to override this
	function GetValue()
	{
		return $this->Value;
	}

	// override me? Returns the (possibly converted) value of the field.
	function GetArrayValue($index)
	{
		if ($this->Value !== false)
			{
			if (is_array($this->Value))
				{
				if (isset($this->Value[$index]))
					{
					return $this->Value[$index];
					}
				}
			elseif ($index == 0)
				{
				return $this->Value;
				}
			}
		return false;
	}

	// override me? Returns true if the value is contained in the Value array
	function FindArrayValue($value)
	{
		if ($this->Value !== false)
			{
			if (is_array($this->Value))
				{
					return array_search($value,$this->Value);
				}
			elseif ($this->Value == $value)
				{
				return true;
				}
			}
		return false;
	}


	// override me, if necessary to convert type or something.
	function SetValue($valStr)
	{
		if ($this->Value === false)
			{
			$this->Value = $valStr;
			}
		else
			{
			if (! is_array($this->Value))
				{
				$this->Value = array($this->Value);
				}
			array_push($this->Value,$valStr);
			}
	}

	// override me, if necessary to convert type or something.
/*	function SetStoredValue($valStr)
	{
		if ($this->Value === false)
			{
			$this->Value = $valStr;
			}
		else
			{
			if (! is_array($this->Value))
				{
				$this->Value = array($this->Value);
				}
			array_push($this->Value,$varStr);
			}
	}
*/

	function RequiresValidation()
	{
		if ($this->ValidationType == 'none')
			{
			return false;
			}
		else
			{
			return true;
			}
	}

	function DoesFieldNameExist()
	{
		$mod = $this->form_ptr->module_ptr;
		
		// field name in use??
		if ($this->form_ptr->HasFieldNamed($this->GetName()))
			{
			return array(false,$mod->Lang('field_name_in_use',$this->GetName()).'<br />');
			}
		
		return array(true,'');
	}

	// override me, if needed. Returns an array: first value is a true or
    // false (whether or not the value is valid), the second is a message
	function AdminValidate()
	{
		return $this->DoesFieldNameExist();
	}


	// override me if you're a Form Disposition pseudo-field.
	// This method can do just
	// about anything you want it to, in order to handle form contents.
	// it returns an array, where the first element is true on success,
	// or false on failure, and the second element is explanatory
	// text for the failure
	function DisposeForm()
	{
		return array(true, '');
	}	

	function GetOptionNames()
	{
		return array_keys($this->Options);
	}

    function GetOption($optionName, $default='')
	{
		if (isset($this->Options[$optionName]))
			{
			return $this->Options[$optionName];
			}
		return $default;
	}

    function &GetOptionRef($optionName)
	{
		if (isset($this->Options[$optionName]))
			{
			return $this->Options[$optionName];
			}
		return false;
	}

	
	function RemoveOptionElement($optionName, $index)
	{
		if (isset($this->Options[$optionName]))
			{
			if (is_array($this->Options[$optionName]))
				{
				if (isset($this->Options[$optionName][$index]))
					{
					array_splice($this->Options[$optionName],$index,1);
					}
				}
			}
	}
	
	function GetOptionElement($optionName, $index, $default="")
	{
		if (isset($this->Options[$optionName]))
			{
			if (is_array($this->Options[$optionName]))
				{
				if (isset($this->Options[$optionName][$index]))
					{
					return $this->Options[$optionName][$index];
					}
				}
			elseif ($index == 0)
				{
				return $this->Options[$optionName];
				}
			}
		return $default;		
	}

    function SetOption($optionName, $optionValue)
    {
        $this->Options[$optionName] = $optionValue;
    }

    function LoadField(&$params)
    {
    	if ($this->Id > 0)
    	   {
    	   $this->Load($this->Id, $params, true);
    	   }
    	return;
    }

    // loadDeep also loads all options for a field.
    function Load(&$params, $loadDeep=false)
    {
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_fb_field WHERE field_id=?';
        if($result = $this->form_ptr->module_ptr->dbHandle->GetRow($sql, array($this->Id)))
			{
			if (strlen($this->Name) < 1)
				{
				$this->Name = $result['name'];
				}
			if (strlen($this->ValidationType) < 1)
				{
				$this->ValidationType = $result['validation_type'];
				}
			$this->Type = $result['type'];
			$this->OrderBy = $result['order_by'];
			if ($this->Required == -1)
				{
				$this->Required = $result['required'];
				}
			if ($this->HideLabel == -1)
				{
				$this->HideLabel = $result['hide_label'];
				}
			}
        else
			{
			return false;
			}
		$this->loaded = 'summary';
		if ($loadDeep)
			{
        	$sql = 'SELECT name, value FROM ' . cms_db_prefix() .
        		'module_fb_field_opt WHERE field_id=? ORDER BY option_id';
			$rs = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
				array($this->Id));
			$tmpOpts = array();
			while ($rs && $results = $rs->FetchRow())
            	{
            	if (isset($tmpOpts[$results['name']]))
            		{
            		if (! is_array($tmpOpts[$results['name']]))
            			{
            			$tmpOpts[$results['name']] = array($tmpOpts[$results['name']]);
            			}
            		array_push($tmpOpts[$results['name']],$results['value']);
            		}
            	else
            		{
            		$tmpOpts[$results['name']]=$results['value'];
            		}
            	}
            $this->Options = array_merge($tmpOpts,$this->Options);
			$this->loaded = 'full';
			}
		return true;
    }


    function Store($storeDeep=false)
    {
    	$mod =  $this->form_ptr->module_ptr;
        if ($this->Id == -1)
            {
            $this->Id = $mod->dbHandle->GenID(cms_db_prefix().'module_fb_field_seq');
 			$sql = 'INSERT INTO ' .cms_db_prefix().
				'module_fb_field (field_id, form_id, name, type, ' .
                'required, validation_type, hide_label, order_by) '.
                ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
			$res = $mod->dbHandle->Execute($sql,
				array($this->Id, $this->FormId, $this->Name,
				$this->Type, $this->Required?1:0, 
				$this->ValidationType, $this->HideLabel, $this->OrderBy));
            }
        else
            {
			$sql = 'UPDATE ' . cms_db_prefix() .
				'module_fb_field set name=?, type=?,'.
                'required=?, validation_type=?, order_by=?, '.
                'hide_label=? where field_id=?';
			$res = $mod->dbHandle->Execute($sql,
				array($this->Name, $this->Type, $this->Required?1:0,
					$this->ValidationType,
					$this->OrderBy, $this->HideLabel, $this->Id));
            }
            
        if ($storeDeep)
            {
            // drop old options
			$sql = 'DELETE FROM ' . cms_db_prefix() .
				'module_fb_field_opt where field_id=?';
			$res = $mod->dbHandle->Execute($sql,
				array($this->Id));

			foreach ($this->Options as $thisOptKey=>$thisOptValueList)
				{
				if (! is_array($thisOptValueList))
					{
					$thisOptValueList = array($thisOptValueList);
					}
				foreach ($thisOptValueList as $thisOptValue)
					{
            		$optId = $mod->dbHandle->GenID(
            			cms_db_prefix().'module_fb_field_opt_seq');
					$sql = 'INSERT INTO ' . cms_db_prefix().
						'module_fb_field_opt (option_id, field_id, form_id, '.
						'name, value) VALUES (?, ?, ?, ?, ?)';
					$res = $mod->dbHandle->Execute($sql,
						array($optId, $this->Id, $this->FormId, $thisOptKey,
						$thisOptValue));
					}
            	}
            }
        return $res;
    }

    function Delete()
    {
		if ($this->Id == -1)
		  {
		  return false;
		  }
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_fb_field where field_id=?';
		$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
			array($this->Id));
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_fb_field_opt where field_id=?';
		$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
			array($this->Id));
		return true;
    }
}

?>
