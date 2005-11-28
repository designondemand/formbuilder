<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbForm {

	var $module_ptr = -1;
    var $Id = -1;
    var $Name = '';
    var $Alias = '';
    var $loaded = 'not';
	var $formTotalPages = 0;
	var $Page;
    var $Attrs;
    var $Fields;

	function fbForm(&$module_ptr, &$params, $loadDeep=false)
	{
	   $this->module_ptr = $module_ptr;
	   $this->Fields = array();
	   $this->Attrs = array();
	   if (isset($params['form_id']))
	       {
	       $this->Id = $params['form_id'];
	       }
	   if (isset($params['form_alias']))
	       {
	       $this->Alias = $params['form_alias'];
	       }
	   if (isset($params['form_name']))
	       {
	       $this->Name = $params['form_name'];
	       }
	   if (isset($params['continue']))
	       {
	       $this->Page = $params['continue'];
	       }
	   else
	   	  {
	   	  $this->Page = 0;
	   	  }
	   $this->formTotalPages = 1;
	   if ($this->Id != -1)
	   		{
	   		$this->Load($this->Id, $params, $loadDeep);
	   		}
	   	foreach ($params as $thisParamKey=>$thisParamVal)
	   		{
	   		if (substr($thisParamKey,0,6) == 'forma_')
	   			{
	   			$thisParamKey = substr($thisParamKey,6);
	   			$this->Attrs[$thisParamKey] = $thisParamVal;
	   			if ($thisParamKey == 'form_template' && $this->Id != -1)
	   				{
	   				$this->module_ptr->SetTemplate('fb_'.$this->Id,$thisParamVal);
	   				}
	   			}
	   		}
	}

	function SetAttributes($attrArray)
	{
		$this->Attrs = array_merge($this->Attrs,$attrArray);
	}

	function SetTemplate($template)
	{
		$this->Attrs['form_template'] = $template;
		$this->module_ptr->SetTemplate('fb_'.$this->Id,$template);
	}

	function GetId()
	{
		return $this->Id;
	}

	function SetId($id)
	{
		$this->Id = $id;
	}

	function GetName()
	{
		return $this->Name;
	}
	
	function GetPageCount()
	{
		return $this->formTotalPages;
	}
	
	function GetPageNumber()
	{
		return $this->Page;
	}

	function PageBack()
	{
		$this->Page--;
	}


	function SetName($name)
	{
		$this->Name = $name;
	}
	
	function GetAlias()
	{
		return $this->Alias;
	}

	function SetAlias($alias)
	{
		$this->Alias = $alias;
	}

	function DebugDisplay()
	{
		$tmp = $this->module_ptr;
		$this->module_ptr = '';
		debug_display($this);
		$this->module_ptr = $tmp;
	}

	
	function GetAttr($attrname, $default)
	{
		if (isset($this->Attrs[$attrname]))
			{
			return $this->Attrs[$attrname];
			}
		else
			{
			return $default;
			}
	}
	
	function GetFieldCount()
	{
		return count($this->Fields);
	}


	function Validate()
	{
        $validated = true;
        $message = '';
        $formPageCount=1;
        for($i=0;$i<count($this->Fields);$i++)
        	{
        	if ($this->Fields[$i]->GetFieldType() == 'PageBreak')
        		{
        		$formPageCount++;
        		}
        	if (isset($this->Page) && $this->Page != $formPageCount)
        		{
        		continue;
        		}
//error_log('validating field: '. $this->Fields[$i]->GetName());
        	if (! $this->Fields[$i]->IsDisposition() &&
        		 $this->Fields[$i]->IsRequired() &&
        		 	$this->Fields[$i]->GetValue() ==
        		 	$this->module_ptr->Lang('unspecified'))
        		{
        			$message .= "<h4>".$this->module_ptr->Lang('please_enter_a_value').' "'. $this->Fields[$i]->GetName()."\"</h4>\n";
        			$validated = false;
        		
        		}
        	else
        		{ 
        		$res = $this->Fields[$i]->Validate();
        		if ($res[0] != true)
        			{
        			$message .= "<h4>". $res[1]."</h4>\n";
        			$validated = false;
        			}
        		}
        	}
		return array($validated, $message);
	}


    function HasDisposition()
    {
    	$hasDisp = false;
        for($i=0;$i<count($this->Fields);$i++)
        	{
        	if ($this->Fields[$i]->IsDisposition())
        	   {
        	   	$hasDisp = true;
        	   }
        	}
        return $hasDisp;
    }

	// return an array: element 0 is true for success, false for failure
	// element 1 is an array of reasons, in the event of failure.
	function Dispose()
	{
		$resArray = array();
        $retCode = true;
        // for each form disposition pseudo-field, dispose the form results
        for($i=0;$i<count($this->Fields);$i++)
        	{
        	if ($this->Fields[$i]->IsDisposition())
        		{
        		$res = $this->Fields[$i]->DisposeForm();
				if ($res[0] == false)
					{
					$retCode = false;
					array_push($resArray,$res[1]);
					}
        		}
        	}
        return array($retCode,$resArray);		
	}

    function RenderFormHeader()
    {
    	if ($this->module_ptr->GetPreference('show_version',0) == 1)
    	   {
    	   return "\n<!-- Start FeedbackForm Module (".$this->module_ptr->GetVersion().") -->\n";
    	   }
    }

    function RenderFormFooter()
    {
    	if ($this->module_ptr->GetPreference('show_version',0) == 1)
    	   {
    	   return "\n<!-- End FeedbackForm Module -->\n";
    	   }
    }


	// now returns a string
	function RenderForm($id, &$params, $returnid)
	{
		switch ($this->GetAttr('form_displaytype','tab')) {
			case 'tab':
				{
				return $this->RenderFormTable($id, $params, $returnid);
				break;
				}
			case 'cssonly':
				{
				break;
				}
			case 'template':
				{
				break;
				}		
		}
	}

    function RenderFormTable($id, &$params, $returnid)
    {
		$mod = $this->module_ptr;
    	if ($this->Id == -1)
			{
			return "<!-- no form -->\n";
			}
		if ($this->loaded != 'full')
			{
			$this->LoadForm($params);
			}
		$this->thisPage++;
		$retStr = '<table';
		if ($this->GetAttr('css_class','') != '')
			{
			$retStr .= ' class="'.$this->GetAttr('css_class').'">';
			}
		$retStr .= '<tr><td colspan="2">';
        if ($this->formTotalPages > 1)
        	{
        	$retStr .= 'Page '.$this->thisPage.' of ';
        	$retStr .= $this->formTotalPages;
        	}
		$retStr .= $mod->CreateInputHidden($id, 'form_id', $this->Id);
	    $retStr .= $mod->CreateInputHidden($id, 'continue', $this->thisPage);
    	if ($this->thisPage == $this->formTotalPages)
			{
			$retStr .= $mod->CreateInputHidden($id, 'done', 1);
			}
		$retStr .= '</td></tr>';
        $formPageCount = 1;
    	for ($i=0; $i < count($this->Fields); $i++)
			{
			$thisField = &$this->Fields[$i];
			if ($thisField->GetFieldType() == 'PageBreak')
				{
				$formPageCount++;
				}
			if ($formPageCount != $this->thisPage)
				{
				if (is_array($params[$this->Fields[$i]->GetId()]))
					{
					foreach ($params[$this->Fields[$i]->GetId()] as $val)
						{
						$retStr .= $mod->CreateInputHidden($id,
							$this->Fields[$i]->GetId().'[]',
							htmlspecialchars($val,ENT_QUOTES));
						}
				}
				else
					{
					$retStr .= $mod->CreateInputHidden($id,
						$this->Fields[$i]->GetId(),
						htmlspecialchars($params[$this->Fields[$i]->GetId()],ENT_QUOTES));
					}
				continue;
				}
			if ($thisField->DisplayInForm())
				{
				if ($this->GetAttr('title_position','left') == 'left')
					{
    	       		$retStr .= "<tr><td align=\"right\" valign=\"top\"";
    	       		}
    	       	else if ($this->GetAttr('title_position','left') == 'top')
    	       		{
    	       		$retStr .= "<tr><td";
    	       		}
    	        if ($thisField->IsRequired())
					{
					$retStr .= ' class="required';
					if ($thisField->GetOption('css_class') != '')
						{
						$retStr .= ' '.$thisField->GetOption('css_class');
						}
					$retStr .= '"';
					}
				else if ($thisField->GetOption('css_class') != '')
					{
					$retStr .= ' class="'.
						$thisField->GetOption('css_class').'"';
					}
				$retStr .= ">";
				if (!$thisField->HideLabel())
					{
					$retStr .= $thisField->GetName();
					if ($thisField->IsRequired() &&
						strlen($this->GetAttr('required_field_symbol','*'))>0)
						{
						$retStr .= " ".
							$this->GetAttr('required_field_symbol','*');
						}
					}
				if ($this->GetAttr('title_position','left') == 'left')
					{
					$retStr .= "</td><td align=\"left\" valign=\"top\">";
					}
				else if ($this->GetAttr('title_position','left') == 'top')
					{
					$retStr .= "</td></tr><tr><td>";
					}
				$retStr .= $thisField->GetFieldInput($id, $params, $returnid);
				$retStr .= "</td></tr>\n";
				}
			else if ($thisField->GetFieldType() == 'FunctionCallInput')
				{
				$retStr .= $thisField->GetFieldInput($id, $params, $returnid);
				}
			}
		$retStr .= "<tr>";
    	if ($this->GetAttr('title_position','left') == 'left')
			{
			$retStr .= "<td>&nbsp;</td><td>";
			}
		else
			{
    		$retStr .= "<td>";
    		}
		if ($this->thisPage < $formPageCount)
			{
    	   	$retStr .= $mod->CreateInputSubmit($id, 'submit',
    	   		$this->GetAttr('next_button_text'),
    	   		$this->GetAttr('use_id_and_name','')==''?'class="fbsubmit"':'id="'.$this->Alias.'_sub" class="fbsubmit"');
			}
		else
			{
            $retStr .= $mod->CreateInputSubmit($id, 'submit',
            	$this->GetAttr('submit_button_text'),
            	$this->GetAttr('use_id_and_name','')==''?'class="fbsubmit"':'id="'.$this->Alias.'_sub" class="fbsubmit"');
			}
    	$retStr .= "</td></tr></table>\n";
		return $retStr;
    }

/*
    function RenderFormOrig($id, &$params, $return_id)
    {
    	if ($this->Id == -1)
    	   {
    	   	echo "<!-- no form -->\n";
    	   	return;
    	   }
    	if ($this->loaded != 'full')
    	   {
    	   	$this->LoadForm($params);
    	   }
		$this->thisPage++;
        if ($this->formTotalPages > 1)
        	{
        	echo "<p>Page ".$this->thisPage." of ".$this->formTotalPages."</p>";
        	}
        echo '<div class="feedbackform_hidden">';
		echo CMSModule::CreateInputHidden($id, 'form_id', $this->Id);
	    echo CMSModule::CreateInputHidden($id, 'continue', $this->thisPage);
    	if ($this->thisPage == $this->formTotalPages)
			{
            echo CMSModule::CreateInputHidden($id, 'done', 1);
		    }
		echo "</div>";
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
        $formPageCount = 1;
    	for ($i=0; $i < count($this->Fields); $i++)
    	   {
    	   $thisField = $this->Fields[$i];
    	   if ($thisField->Type == 'PageBreak')
    	   	   {
    	   	   $formPageCount++;
    	   	   }
//echo 'render: '.$formPageCount . ' ' . $this->thisPage . ' ' . $thisField->Name.'<br>';
    	   if ($formPageCount != $this->thisPage)
    	   	   {
    	   	   if (is_array($params[$this->Fields[$i]->Alias]))
    	   	       {
    	   	       foreach ($params[$this->Fields[$i]->Alias] as $val)
    	   	           {
                       echo CMSModule::CreateInputHidden($id, $this->Fields[$i]->Alias.'[]', htmlspecialchars($val,ENT_QUOTES));
                       }
    	   	       }
               else
                   {
                   echo CMSModule::CreateInputHidden($id, $this->Fields[$i]->Alias, htmlspecialchars($params[$this->Fields[$i]->Alias],ENT_QUOTES));
                   }
    	   	   continue;
    	   	   }
    	   if ($thisField->DisplayInForm)
    	       {
    	        if ($this->mod_globals->UseCSS)
    	        	{
    	        	if ($thisField->Required)
    	        		{
    	        		echo "<p class=\"required\">";
    	        		}
    	        	else
    	        		{
    	        		echo "<p>";
    	        		}
    	        	echo $thisField->Name;
    	        	if ($thisField->Required && strlen($this->RequiredFieldSymbol)>0) 
    	        		{
    	        		echo " ".$this->RequiredFieldSymbol;
    	        		}
    	        	echo "</p>";
    	        	echo $thisField->WriteToPublicForm($id, $params, $return_id);
    	        	}
    	        else
    	        	{
    	       		if ($this->mod_globals->TitlePosition == 0)
    	       			{
    	       			echo "<tr><td align=\"right\" valign=\"top\"";
    	       			}
    	       		else if ($this->mod_globals->TitlePosition == 1)
    	       			{
    	       			echo "<tr><td";
    	       			}
    	        	if ($thisField->Required)
    	        		{
    	        		echo " class=\"required";
    	        	    if (isset($thisField->CSSClass) && $thisField->CSSClass != '')
                            {
                            echo ','.$thisField->CSSClass.'"';
                            }
                        else
                            {
                            echo '"';
                            }
    	        		}
    	        	else if (isset($thisField->CSSClass) && $thisField->CSSClass != '')
                        {
                        echo ' class="'.$thisField->CSSClass.'"';
                        }
    	       		echo ">".$thisField->Name;
    	        	if ($thisField->Required && strlen($this->RequiredFieldSymbol)>0)
    	        		{
    	        		echo " ".$this->RequiredFieldSymbol;
    	        		}
    	       		if ($this->mod_globals->TitlePosition == 0)
    	       			{
    	       			echo "</td><td align=\"left\" valign=\"top\">";
    	       			}
    	       		else if ($this->mod_globals->TitlePosition == 1)
    	       			{
    	       			echo "</td></tr><tr><td>";
    	       			}
                	echo $thisField->WriteToPublicForm($id, $params, $return_id);
                	echo "</td></tr>\n";
    	        	}
                }
            else if ($thisField->Type == 'FunctionCallInput')
                {
                echo $thisField->WriteToPublicForm($id, $params, $return_id);
                }
    	   }
    	if (! $this->mod_globals->UseCSS)
    		{
    		echo "<tr>";
    		if ($this->mod_globals->TitlePosition == 0)
				{
				echo "<td>&nbsp;</td><td>";
				}
			else
				{
    			echo "<td>";
    			}
    		}
    	if ($this->thisPage < $formPageCount)
    	   {
    	   	echo CMSModule::CreateInputSubmit($id, 'submit', $this->NextButtonText,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_sub" class="ffsubmit"':'class="ffsubmit"');
    	   }
        else
           {
            echo CMSModule::CreateInputSubmit($id, 'submit', $this->SubmitButtonText,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_sub" class="ffsubmit"':'class="ffsubmit"');
           }
    	if ($this->mod_globals->UseCSS)
    		{
    		echo "</div>\n";
    		}
    	else
    		{
    		echo "</td></tr></table>\n";
    		}
    }
*/


    function AddField($fieldInfo=array())
    {
        $className = $this->MakeClassName($fieldInfo['type'], '');
        // create the field object
        $this->Fields[] = new $className($this->mod_globals, $fieldInfo);
    }

    function LoadForm()
    {
    	return $this->Load($this->Id, true);
    }

    function Load($formId, &$params, $loadDeep=false)
    {
        $sql = 'SELECT * FROM '.cms_db_prefix().'module_fb_form WHERE form_id=?';
	    $rs = $this->module_ptr->dbHandle->Execute($sql, array($formId));
        if($rs && $rs->RowCount() > 0)
	       {
	       $result = $rs->FetchRow();
           $this->Id = $result['form_id'];
           $this->Name = $result['name'];
	       $this->Alias = $result['alias'];
           }
        else
           {
           return false;
           }
        $sql = 'SELECT name,value FROM '.cms_db_prefix().
        	'module_fb_form_attr WHERE form_id=?';
	    $rs = $this->module_ptr->dbHandle->Execute($sql, array($formId));
        while ($rs && $result=$rs->FetchRow())
	       {
	       //$attrName = substr($result['name'],6);
           //$this->Attrs[$attrName] = $result['value'];
           $this->Attrs[$result['name']] = $result['value'];
           }
          
        $this->loaded = 'summary';
        if ($loadDeep)
           {
           $sql = 'SELECT * FROM ' . cms_db_prefix().
           	'module_fb_field WHERE form_id=? ORDER BY order_by';
	       $rs = $this->module_ptr->dbHandle->Execute($sql, array($formId));
           $result = array();
           if ($rs && $rs->RowCount() > 0)
                {
                $result = $rs->GetArray();
                }
            $fieldCount = 0;
            if (count($result) > 0)
                {
                foreach($result as $thisRes)
                    {
                    $className = $this->MakeClassName($thisRes['type'], '');
                    // create the field object
                    $thisRes = array_merge($thisRes, $params);
                    $this->Fields[$fieldCount] = $this->NewField($thisRes);
                    // load its options
                 //   $this->Fields[$fieldCount]->LoadOptions($params);
                    $fieldCount++;
                    }
                }
            $this->loaded = 'full';
           }
		
		for ($i=0; $i < count($this->Fields); $i++)
			{
			//echo $this->Fields[$i]->Type.' ';
			if ($this->Fields[$i]->Type == 'PageBreak')
				{
				$this->formTotalPages++;
				}
			}          
           
        return true;
    }

/*
    function ListSavedFields($formId)
    {
		$sql = 'SELECT * FROM ' . $this->mod_globals->FieldTableName .
                ' WHERE form_id=? ORDER BY order_by';
	    
	    $rs = $this->mod_globals->DBHandle->Execute($sql, array( $formId ));
        $result = array();
        if ($rs && $rs->RowCount() > 0)
        	{
            $result=$rs->GetRows();
            }
        return $result;
    }
*/

    // storeDeep also stores all fields and options for a form
    function Store($storeDeep=false)
    {
        if ($this->Id == -1)
            {
            $this->Id = $this->module_ptr->dbHandle->GenID(cms_db_prefix().
            	'module_fb_form_seq');
			$sql = 'INSERT INTO ' . cms_db_prefix().
				'module_fb_form (form_id, name, alias) '.
				'VALUES (?, ?, ?)';
			$res = $this->module_ptr->dbHandle->Execute($sql,
				array($this->Id, $this->Name, $this->Alias));
            }
        else
            {
			$sql = 'UPDATE ' . cms_db_prefix().
				'module_fb_form set name=?, alias=? where form_id=?';
			$res = $this->module_ptr->dbHandle->Execute($sql,
				array($this->Name, $this->Alias, $this->Id));
            }
        // save out the attrs
		$sql = 'DELETE FROM '.cms_db_prefix().
			'module_fb_form_attr WHERE form_id=?';
		$res = $this->module_ptr->dbHandle->Execute($sql,
			array($this->Id));
		foreach ($this->Attrs as $thisAttrKey=>$thisAttrValue)
			{
            $formAttrId = $this->module_ptr->dbHandle->GenID(cms_db_prefix().
            	'module_fb_form_attr_seq');
			$sql = 'INSERT INTO ' . cms_db_prefix().
				'module_fb_form_attr (form_attr_id, form_id, name, value) '.
				'VALUES (?, ?, ?, ?)';
			$res = $this->module_ptr->dbHandle->Execute($sql,
				array($formAttrId, $this->Id, $thisAttrKey,
				$thisAttrValue));
			}

/*        if ($storeDeep)
            {
            foreach ($this->Fields as $field)
                {
                $field->Store($storeDeep);
                }
            }
*/        return $res;
    }

    function Delete()
    {
		if ($this->Id == -1)
		  {
		  return false;
		  }
		if ($this->loaded != 'full')
			{
			$this->LoadForm();
			}
        foreach ($this->Fields as $field)
            {
            $field->Delete();
            }
        $this->module_ptr->DeleteTemplate('fb_'.$this->Id);
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_fb_form where form_id=?';
		$res = $this->module_ptr->dbHandle->Execute($sql, array($this->Id));
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_fb_form_attr where form_id=?';
		$res = $this->module_ptr->dbHandle->Execute($sql, array($this->Id));
		return true;
    }

    // returns a class name, and makes sure the file where the class is
    // defined has been loaded.
    function MakeClassName($type, $classDirPrefix)
    {
    	// perform rudimentary security, since Type could come in from a form
    	$type = preg_replace("/[\W]|\.\./", "_", $type);
		if ($type == '' || strlen($type) < 1)
			{
			$type = 'Field';
			}
    	if (strlen($classDirPrefix) > 0)
    	   {
    	   $classFile = $classDirPrefix .'/'.$type.'.class.php';
    	   }
        else
           {
           $classFile = $type.'.class.php';
           }
        require_once $classFile;
        // class names are prepended with "fb" to prevent namespace clash.
        return ( 'fb'.$type );
    }

	function AddEditForm($id, $returnid, $message='')
	{
		$mod = &$this->module_ptr;
		$mod->smarty->assign('message',$message);
		$mod->smarty->assign('formstart',
			$mod->CreateFormStart($id, 'admin_form_update', $returnid));
		$mod->smarty->assign('formid',
			$mod->CreateInputHidden($id, 'form_id', $this->Id));
		$mod->smarty->assign('tab_start',$mod->StartTabHeaders().
			$mod->SetTabHeader('maintab',$mod->Lang('tab_main')).
			$mod->SetTabHeader('addition',$mod->Lang('tab_additional')).
			$mod->SetTabHeader('tablelayout',$mod->Lang('tab_tablelayout')).
			$mod->SetTabHeader('csslayout',$mod->Lang('tab_csslayout')).
			$mod->SetTabHeader('templatelayout',$mod->Lang('tab_templatelayout')).
			
			$mod->EndTabHeaders() . $mod->StartTabContent());

		$mod->smarty->assign('tabs_end',$mod->EndTabContent());
		$mod->smarty->assign('maintab_start',$mod->StartTab("maintab"));
		$mod->smarty->assign('additionaltab_start',$mod->StartTab("addition"));
		$mod->smarty->assign('tabletab_start',$mod->StartTab("tablelayout"));
		$mod->smarty->assign('csstab_start',$mod->StartTab("csslayout"));
		$mod->smarty->assign('templatetab_start',$mod->StartTab("templatelayout"));
		$mod->smarty->assign('tab_end',$mod->EndTab());
		$mod->smarty->assign('form_end',$mod->CreateFormEnd());
		$mod->smarty->assign('title_form_name',$mod->Lang('title_form_name'));
		$mod->smarty->assign('input_form_name',
			$mod->CreateInputText($id, 'form_name',
			$this->Name, 50));
		$mod->smarty->assign('title_form_status',
			$mod->Lang('title_form_status'));
		$mod->smarty->assign('text_ready',
			$mod->Lang('title_ready_for_deployment'));
		$mod->smarty->assign('title_form_alias',$mod->Lang('title_form_alias'));
		$mod->smarty->assign('input_form_alias',
			$mod->CreateInputText($id, 'form_alias',
			$this->Alias, 50));
		$mod->smarty->assign('title_form_css_class',
			$mod->Lang('title_form_css_class'));
		$mod->smarty->assign('input_form_css_class',
			$mod->CreateInputText($id, 'forma_css_class',
				$this->GetAttr('css_class','formbuilderform'), 50,50));
		$mod->smarty->assign('title_form_fields',
			$mod->Lang('title_form_fields'));
		$mod->smarty->assign('title_field_name',
			$mod->Lang('title_field_name'));
		$mod->smarty->assign('title_field_type',
			$mod->Lang('title_field_type'));
		$mod->smarty->assign('title_field_type',
			$mod->Lang('title_field_type'));
		$mod->smarty->assign('title_form_template',
			$mod->Lang('title_form_template'));
		$mod->smarty->assign('title_information',$mod->Lang('information'));
		$mod->smarty->assign('title_order',$mod->Lang('order'));    
		$mod->smarty->assign('title_form_displaytype', $mod->Lang('title_form_displaytype'));
        $mod->smarty->assign('title_field_required_abbrev',$mod->Lang('title_field_required_abbrev'));
		$mod->smarty->assign('link_notready',"<strong>".$mod->Lang('title_not_ready1')."</strong><P>".$mod->Lang('title_not_ready2')." ".$mod->CreateLink($id, 'admin_add_formbuilder_field', $returnid,$mod->Lang('title_not_ready_link'),array('form_id'=>$this->Id, 'order_by'=>$this->GetFieldCount(),'dispose_only'=>1), '', false)." ".$mod->Lang('title_not_ready3')."</p>");
		$mod->smarty->assign('hasdisposition',$this->HasDisposition()?1:0);

		if($this->Id > 0)
    	{
    		$mod->smarty->assign('submit_button',
    			$mod->CreateInputSubmit($id, 'submit',
    			$mod->Lang('save_and_continue')));
    		$mod->smarty->assign('hidden',
    			$mod->CreateInputHidden($id, 'form_op',$mod->Lang('updated')));
			$mod->smarty->assign('adding',0);
			$mod->smarty->assign('save_button',
				$mod->CreateInputSubmit($id, 'submit', $mod->Lang('save')));
			$fieldList = array();
			$currow = "row1";
			$count = 1;
			$maxOrder = 1;
			$last = $this->GetFieldCount();
			foreach ($this->Fields as $thisField)
				{
				$oneset = new stdClass();
				$oneset->rowclass = $currow;
				$oneset->name = $mod->CreateLink($id, 'admin_edit_formbuilder_field', '', $thisField->GetName(), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
				$oneset->type = $thisField->GetDisplayType();
				if ($thisField->IsDisposition() || !$thisField->DisplayInForm())
					{
					$oneset->disposition = '.';
					}
				else if ($thisField->IsRequired())
					{
					$oneset->disposition = $mod->CreateLink($id, 'admin_field_required_update', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/true.gif','true','','','systemicon'), array('form_id'=>$this->Id,'active'=>'off','field_id'=>$thisField->GetId()));
					}
				else
					{
					$oneset->disposition = $mod->CreateLink($id, 'admin_field_required_update', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/false.gif','false','','','systemicon'), array('form_id'=>$this->Id,'active'=>'on','field_id'=>$thisField->GetId()));
					}
				$oneset->field_status = $thisField->StatusInfo();
				if ($count > 1)
					{
					$oneset->up = $mod->CreateLink($id, 'admin_field_order_update', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/arrow-u.gif','up','','','systemicon'), array('form_id'=>$this->Id,'dir'=>'up','field_id'=>$thisField->GetId()));
					}
				else
					{
					$oneset->up = '&nbsp;';
					}
				if ($count < $last)
					{
					$oneset->down=$mod->CreateLink($id, 'admin_field_order_update', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/arrow-d.gif','down','','','systemicon'), array('form_id'=>$this->Id,'dir'=>'down','field_id'=>$thisField->GetId()));
					}
				else
					{
					$oneset->down = '&nbsp;';
					}
				$oneset->editlink = $mod->CreateLink($id, 'admin_edit_formbuilder_field', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/edit.gif','edit','','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
				$oneset->deletelink = $mod->CreateLink($id, 'admin_delete_formbuilder_field', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/delete.gif','delete','','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id),$mod->Lang('are_you_sure_delete_field',$thisField->GetName()));
				($currow == "row1"?$currow="row2":$currow="row1");
				$count++;
				if ($thisField->GetOrder() >= $maxOrder)
					{
					$maxOrder = $thisField->GetOrder() + 1;
					}
				array_push($fieldList, $oneset);
				}
			$mod->smarty->assign('fields',$fieldList);
			$mod->smarty->assign('add_field_link',
				$mod->CreateLink($id, 'admin_add_formbuilder_form', $returnid,$mod->cms->variables['admintheme']->DisplayImage('icons/system/newobject.gif',$mod->Lang('title_add_new_field'),'','','systemicon'),array('form_id'=>$this->Id, 'order_by'=>$maxOrder), '', false) . $mod->CreateLink($id, 'admin_add_formbuilder_field', $returnid,$mod->Lang('title_add_new_field'),array('form_id'=>$this->Id, 'order_by'=>$maxOrder), '', false));			
		}
		else
		{
			$mod->smarty->assign('save_button','');
			$mod->smarty->assign('submit_button',
				$mod->CreateInputSubmit($id, 'submit', $mod->Lang('add')));
    		$mod->smarty->assign('hidden',
    			$mod->CreateInputHidden($id, 'form_op',$mod->Lang('added')));
			$mod->smarty->assign('adding',1);
		}
		$mod->smarty->assign('title_form_submit_button',
			$mod->Lang('title_form_submit_button'));
		$mod->smarty->assign('input_form_submit_button',
			$mod->CreateInputText($id, 'forma_submit_button_text',
				$this->GetAttr('submit_button_text','Submit Form'), 35, 35));
		$mod->smarty->assign('title_form_next_button',
			$mod->Lang('title_form_next_button'));
		$mod->smarty->assign('input_form_next_button',
			$mod->CreateInputText($id, 'forma_next_button_text',
				$this->GetAttr('next_button_text','Continue...'), 35, 35));
		$mod->smarty->assign('title_form_thanks_text',
			$mod->Lang('title_form_thanks_text'));
		$mod->smarty->assign('input_form_thanks_text',
			$mod->CreateTextArea(false, $id,
				$this->GetAttr('thanks_message','Thanks!'), 'forma_thanks_message', 'pageheadtags', '', '', ''));
		$mod->smarty->assign('title_form_required_symbol',
			$mod->Lang('title_form_required_symbol'));
		$mod->smarty->assign('input_form_required_symbol',
			$mod->CreateInputText($id, 'forma_required_field_symbol',
				$this->GetAttr('required_field_symbol','*'), 50));

		$displayTypes = array($mod->Lang('disptype_table')=>'tab',
			$mod->Lang('disptype_css')=>'cssonly',
			$mod->Lang('disptype_template')=>'template');
		$mod->smarty->assign('input_form_displaytype',
			$mod->CreateInputRadioGroup($id, 'forma_form_displaytype', $displayTypes, $this->GetAttr('form_displaytype','tab')));
				
		$mod->smarty->assign('title_css_id_and_name',
			$mod->Lang('title_css_id_and_name'));
		$mod->smarty->assign('input_css_id_and_name',
			$mod->CreateInputCheckbox($id, 'forma_css_id_and_name', 1,
				$this->GetAttr('css_id_and_name','0')).$mod->Lang('title_css_id_and_name'));
		$mod->smarty->assign('title_title_position',
			$mod->Lang('title_title_position'));
		$pos = array($mod->Lang('title_table_layout_left')=>'left',$mod->Lang('title_table_layout_above')=>'top');	
		$mod->smarty->assign('input_title_position',
			$mod->CreateInputRadioGroup($id, 'forma_title_position',
				$pos, $this->GetAttr('title_position','left')));		
		$mod->smarty->assign('input_form_template',
			$mod->CreateTextArea(false, $id,
				$this->GetAttr('form_template','default form template'), 'forma_form_template'));
        return $mod->ProcessTemplate('AddEditForm.tpl');
	}


	function &NewField($params)
	{
		$aefield = new fbFieldBase($this,$params);
        if ($aefield->GetId() != -1 )
            {
            // we're loading an extant field
			$sql = 'SELECT type FROM ' . cms_db_prefix() . 'module_fb_field WHERE field_id=?';
	    	$rs = $this->module_ptr->dbHandle->Execute($sql, array($aefield->GetId()));
        	if($rs && $result = $rs->FetchRow())
				{
				$aefield->SetFieldType($result['type']);
            	}
            }
		// what kind of field?
		// need to know what kind of object to instantiate!
        if ($aefield->GetFieldType() != '')
            {
            // OK, instantiating a specific Input class.
            $className = $this->MakeClassName($aefield->GetFieldType(), '');
            $aefield = new $className($this, $params);
            }
        $aefield->LoadField($params);
		return $aefield;
	}


	function AddEditField($id, &$aefield, $returnid, $message='')
	{
		$mod = $this->module_ptr;
		
		$mod->smarty->assign('message',$message);
		if ($aefield->GetFieldType() == '')
			{
			$mod->smarty->assign('start_form',$mod->CreateFormStart($id, 'admin_field_update', $returnid));
			$mod->smarty->assign('type_set',0);
			}
		else
			{
			$mod->smarty->assign('start_form',$mod->CreateFormStart($id, 'admin_field_update', $returnid));
			$mod->smarty->assign('type_set',1);
			}
		$mod->smarty->assign('end_form', $mod->CreateFormEnd());
		$mod->smarty->assign('tab_start',$mod->StartTabHeaders().
			$mod->SetTabHeader('maintab',$mod->Lang('tab_main')).
			$mod->SetTabHeader('advancedtab',$mod->Lang('tab_advanced')).
			$mod->EndTabHeaders() . $mod->StartTabContent());
		$mod->smarty->assign('tabs_end',$mod->EndTabContent());
		$mod->smarty->assign('maintab_start',$mod->StartTab("maintab"));
		$mod->smarty->assign('advancedtab_start',$mod->StartTab("advancedtab"));
		$mod->smarty->assign('tab_end',$mod->EndTab());
		$mod->smarty->assign('notice_select_type',$mod->Lang('notice_select_type'));

		if($aefield->GetId() != -1)
			{
			$mod->smarty->assign('op',$mod->CreateInputHidden($id, 'op',$mod->Lang('updated')));
			$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, '', $mod->Lang('update')));
			}
		else
			{
			$mod->smarty->assign('op',$mod->CreateInputHidden($id, 'op', $mod->Lang('added')));
			$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, '', $mod->Lang('add')));
			}

		$mod->smarty->assign('hidden', $mod->CreateInputHidden($id, 'form_id', $this->Id) . $mod->CreateInputHidden($id, 'field_id', $aefield->GetId()) . $mod->CreateInputHidden($id, 'order_by', $aefield->GetOrder()).
		$mod->CreateInputHidden($id,'set_from_form','1'));
		$mod->smarty->assign('title_field_name',$mod->Lang('title_field_name'));
		$mod->smarty->assign('input_field_name', $mod->CreateInputText($id, 'field_name', $aefield->GetName(), 50));
		$mod->smarty->assign('title_field_type',$mod->Lang('title_field_type'));
		
		if ($aefield->GetFieldType() == '')
			{
			if (isset($params['dispose_only']) && $params['dispose_only']==1)
				{
				$mod->smarty->assign('input_field_type',$mod->CreateInputDropdown($id, 'field_type',array_merge(array($mod->Lang('select_type')=>''),$mod->disp_field_types), -1,'', 'onchange="this.form.submit()"'));
				}
			else
				{
				$mod->smarty->assign('input_field_type',$mod->CreateInputDropdown($id, 'field_type',array_merge(array($mod->Lang('select_type')=>''),$mod->field_types), -1,'', 'onchange="this.form.submit()"'));
				}
			}
		else
			{
			$mod->smarty->assign('input_field_type',$aefield->GetDisplayType().$mod->CreateInputHidden($id, 'type', $aefield->GetFieldType()));
			}

		if (!$aefield->IsDisposition() && !$aefield->IsSpecialInput())
			{
			$mod->smarty->assign('requirable',1);
			}
		else
			{
			$mod->smarty->assign('requirable',0);
			}
		$mod->smarty->assign('title_field_required',$mod->Lang('title_field_required'));
		$mod->smarty->assign('input_field_required',$mod->CreateInputCheckbox($id, 'required', 1, $aefield->IsRequired()).$mod->Lang('title_field_required_long'));
		$mod->smarty->assign('title_hide_label',$mod->Lang('title_hide_label'));

		$mod->smarty->assign('input_hide_label',$mod->CreateInputCheckbox($id, 'hide_label', 1, $aefield->HideLabel()).$mod->Lang('title_hide_label_long'));

		$mod->smarty->assign('title_field_validation',$mod->Lang('title_field_validation'));
		if (count($aefield->GetValidationTypes()) > 1)
			{
			$mod->smarty->assign('input_field_validation',$mod->CreateInputDropdown($id, 'validation_type', $aefield->GetValidationTypes(), -1, $aefield->GetValidationType()));
			}
		else
			{
			$mod->smarty->assign('input_field_validation',$mod->Lang('automatic'));
			}

		if ($aefield->DisplayInForm())
			{
			$mod->smarty->assign('displayinform',1);
			$mod->smarty->assign('title_field_css_class',$mod->Lang('title_field_css_class'));
			$mod->smarty->assign('input_field_css_class',$mod->CreateInputText($id, 'opt_css_class', $aefield->GetOption('css_class'), 50));
			}
		else
			{
			$mod->smarty->assign('displayinform',0);
			}
		$mainList = array();
		$advList = array();
		$fieldList = $aefield->RenderAdminForm($id);
		if (isset($fieldList['main']))
			{
			foreach ($fieldList['main'] as $titleStr=>$inputStr)
				{
				$oneset = new stdClass();
				$oneset->title = $titleStr;
				if (is_array($inputStr))
					{
					$oneset->input = $inputStr[0];
					$oneset->help = $inputStr[1];
					}
				else
					{
					$oneset->input = $inputStr;
					$oneset->help='';
					}
				array_push($mainList,$oneset);
				}
			}
		if (isset($fieldList['adv']))
			{
			foreach ($fieldList['adv'] as $titleStr=>$inputStr)
				{
				$oneset = new stdClass();
				$oneset->title = $titleStr;
				if (is_array($inputStr))
					{
					$oneset->input = $inputStr[0];
					$oneset->help = $inputStr[1];
					}
				else
					{
					$oneset->input = $inputStr;
					$oneset->help='';
					}
				array_push($advList,$oneset);
				}
			}
		
		$mod->smarty->assign('mainList',$mainList);
		$mod->smarty->assign('advList',$advList);
        return $mod->ProcessTemplate('AddEditField.tpl');
    }

    function MakeAlias($string, $isForm=false)
    {
    	$string = trim($string);
        $string = preg_replace("/[_-\W]+/", "_", $string);
		$string = trim($string, '_');
		if ($isForm)
		  {
		  return strtolower($string);
		  }
		else
		  {
		  return 'fb'.strtolower($string);
		  }
    }
    
    function SwapFieldsByIndex($src_field_index, $dest_field_index)
    {
        $srcField = $this->GetFieldByIndex($src_field_index);
        $destField = $this->GetFieldByIndex($dest_field_index);
        $tmpOrderBy = $destField->GetOrder();
        $destField->SetOrder($srcField->GetOrder());
        $srcField->SetOrder($tmpOrderBy);
        $this->Fields[$src_field_index] = $destField;
        $this->Fields[$dest_field_index] = $srcField;
        $srcField->Store();
        $destField->Store();
    }
    
    function &GetFieldById($field_id)
    {
    	$index = -1;
    	for ($i=0;$i<count($this->Fields);$i++)
    		{
    		if ($this->Fields[$i]->GetId() == $field_id)
    			{
    			$index = $i;
    			}
    		}
    	if ($index != -1)
    		{
    		return $this->Fields[$index];
    		}
    	else
    		{
    		return false;
    		}
    }

    function &GetFieldByIndex($field_index)
    {
    	return $this->Fields[$field_index];
    }


    function GetFieldIndexFromId($field_id)
    {
    	$index = -1;
    	for ($i=0;$i<count($this->Fields);$i++)
    		{
    		if ($this->Fields[$i]->GetId() == $field_id)
    			{
    			$index = $i;
    			}
    		}
		return $index;
    }

    
    function DeleteField($field_id)
    {
    	$index = $this->GetFieldIndexFromId($field_id);
    	if ($index != -1)
    		{
    		$this->Fields[$index]->Delete();
    		array_splice($this->Fields,$index,1);
    		}
    }    
    
}

?>
