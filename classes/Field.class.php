<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbField {

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
    var $SpecialInput;

    var $Value=false;
    var $form_ptr;
    var $Options;
    var $loaded;

	function fbField(&$form_ptr, $params=array())
	{
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
	   if (isset($params[$this->Id]))
	   		{
	   		$this->Value = $params[$this->Id];
	   		}
	   $this->DisplayInForm = true;
	   $this->IsDisposition = false;
	   $this->ValidationTypes = array($mod->Lang('validation_none')=>'none');
	   $this->loaded = 'not';
	   $this->SpecialInput = false;
		foreach ($params as $thisParamKey=>$thisParamVal)
		{
	   		if (substr($thisParamKey,0,4) == 'opt_')
	   			{
	   			$thisParamKey = substr($thisParamKey,4);
	   			$this->Options[$thisParamKey] = $thisParamVal;
	   			}
	   	}

	}

	// override me with a form input string or something
	function WriteToPublicForm($id, &$params, $return_id)
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

	
	function IsSpecialInput()
	{
		return $this->SpecialInput;
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

    // override me.
    // I return an ugly data structure:
    // It's an associative array with two items, 'main' and 'adv' (for the
    // main and advanced setting tabs).
    // Each of these is an associative array of Title / Input values.
    // The Title will be displayed if it has a length;
    // the "Input" should be a Form input for that field attribute/option
	function RenderAdminForm($formDescriptor)
	{
		return array();
	}


	// override me. Returns an array: first value is a true or false (whether or not
    // the value is valid), the second is a message
	function Validate()
	{
		return array(true,'');
	}

	// override me. Returns the (possibly converted) value of the field.
	function GetValue()
	{
		if ($this->Value !== false)
			{
			return $this->Value;
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}
	}

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

	// override me, but call me first!. Returns an array: first value is a true or
    // false (whether or not the value is valid), the second is a message
	function AdminValidate()
	{
		$mod = $this->form_ptr->module_ptr;
		$ret = array(true,'');
/*		if ($this->AliasExists())
		  {
		  $ret = array(false,$mod->Lang('field_name_in_use1').' "'.$this->Name.'" '. $mod->Lang('field_name_in_use2'));
		  }
*/
		return $ret;
	}


	// override me if you're a Form Disposition pseudo-field.
	// This method can do just
	// about anything you want it to, in order to handle form contents.
	// it receives the rest of the form as an array of name/value arrays.
	function DisposeForm($formName, &$config, $results)
	{
		return false;
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
		else
			{
			return $default;
			}
	}

    function SetOption($optionName, $optionValue)
    {
        $this->Options[$optionName] = $optionValue;
    }

    function LoadField($params)
    {
    	if ($this->Id > 0)
    	   {
    	   $this->Load($this->Id, $params, true);
    	   }
    	return;
    }

    // loadDeep also loads all options for a field.
    function Load($params=array(), $loadDeep=false)
    {
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_fb_field WHERE field_id=?';
	    $rs = $this->form_ptr->module_ptr->dbHandle->Execute($sql, array($this->Id));
        if($rs && $result = $rs->FetchRow())
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


/*    function AliasExists()
    {
        $sql = 'SELECT field_id FROM ' . cms_db_prefix() . 'module_fb_field WHERE alias=? and form_id=?';
	    $rs = $this->form_ptr->module_ptr->dbHandle->Execute($sql, array($this->Alias,$this->form_ptr->GetId()));
        if ($rs && $rs->RowCount()>0)
            {
            error_log('alias exists');
            return true;
            }
            error_log('alias doesn');
        return false;
    }
*/

    function Store($storeDeep=false)
    {
        if ($this->Id == -1)
            {
            $this->Id = $this->form_ptr->module_ptr->dbHandle->GenID(cms_db_prefix().'module_fb_field_seq');
			$sql = 'INSERT INTO ' .cms_db_prefix().
				'module_fb_field (field_id, form_id, name, type, ' .
                'required, validation_type, hide_label, order_by) '.
                ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
			$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
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
			$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
				array($this->Name, $this->Type, $this->Required?1:0,
					$this->ValidationType,
					$this->OrderBy, $this->HideLabel, $this->Id));
            }
            
        if ($storeDeep)
            {
            // drop old options
			$sql = 'DELETE FROM ' . cms_db_prefix() .
				'module_fb_field_opt where field_id=?';
			$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
				array($this->Id));

			foreach ($this->Options as $thisOptKey=>$thisOptValueList)
				{
				if (! is_array($thisOptValueList))
					{
					$thisOptValueList = array($thisOptValueList);
					}
				foreach ($thisOptValueList as $thisOptValue)
					{
            		$optId = $this->form_ptr->module_ptr->dbHandle->GenID(
            			cms_db_prefix().'module_fb_field_opt_seq');
					$sql = 'INSERT INTO ' . cms_db_prefix().
						'module_fb_field_opt (option_id, field_id, form_id, '.
						'name, value) VALUES (?, ?, ?, ?, ?)';
					$res = $this->form_ptr->module_ptr->dbHandle->Execute($sql,
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
