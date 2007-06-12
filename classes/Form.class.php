<?php
/* 
   FormBuilder. Copyright (c) 2005-2007 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2007 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

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
  var $formState;
  var $sampleTemplateCode;
  var $templateVariables;

  function fbForm(&$module_ptr, &$params, $loadDeep=false)
  {
    $this->module_ptr =& $module_ptr;
    $this->Fields = array();
    $this->Attrs = array();
    $this->formState = 'new';
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
	$this->Page = 1;
      }
    if (isset($params['prev']) && isset($params['previous']))
      {
	$this->Page = $params['previous'];
	$params['done'] = 0;
      }
    $this->formTotalPages = 1;
    if (isset($params['done'])&& $params['done']==1)
      {
	$this->formState = 'submit';
      }
    if (isset($params['user_form_validate']) && $params['user_form_validate']==true)
      {
	$this->formState = 'confirm';
      }
    if ($this->Id != -1)
      {
	if (isset($params['response_id']) && $this->formState == 'submit')
	  {
	    $this->formState = 'update';
	  }
	$this->Load($this->Id, $params, $loadDeep);
      }
    foreach ($params as $thisParamKey=>$thisParamVal)
      {
	if (substr($thisParamKey,0,6) == 'forma_')
	  {
	    $thisParamKey = substr($thisParamKey,6);
	    $this->Attrs[$thisParamKey] = $thisParamVal;
	  }
	else if ($thisParamKey == 'form_template' && $this->Id != -1)
	  {
	    $this->module_ptr->SetTemplate('fb_'.$this->Id,$thisParamVal);
	  }
      }

    $this->templateVariables = Array(
		'{$sub_form_name}'=>$this->module_ptr->Lang('title_form_name'),
		'{$sub_date}'=>$this->module_ptr->Lang('help_submission_date'),
		'{$sub_host}'=>$this->module_ptr->Lang('help_server_name'),
		'{$sub_source_ip}'=>$this->module_ptr->Lang('help_sub_source_ip'),
		'{$sub_url}'=>$this->module_ptr->Lang('help_sub_url')
	);
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
	
  function GetFormState()
  {
    return $this->formState;
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
    $this->module_ptr = '[mdptr]';
    $template_tmp = $this->GetAttr('form_template','');
    $this->SetAttr('form_template',strlen($template_tmp).' characters');
    $field_tmp = $this->Fields;
    $this->Fields = 'Field Array: '.count($field_tmp);
    debug_display($this);
    $this->SetAttr('form_template',$template_tmp);
    $this->Fields = $field_tmp;
    foreach($this->Fields as $fld)
      {
		$fld->DebugDisplay();
      }
    $this->module_ptr = $tmp;
  }

	
  function SetAttr($attrname, $val)
  {
    $this->Attrs[$attrname] = $val;
  }
	
  function GetAttr($attrname, $default="")
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

  function HasFieldNamed($name)
  {
    $ret = -1;
    foreach($this->Fields as $fld)
		{
		if ($fld->GetName() == $name)
			{
			$ret = $fld->GetId();
			}
		}
    return $ret;
  }

  function AddTemplateVariable($name,$def)
  {
    $theKey = '{$'.$name.'}';
    $this->templateVariables[$theKey] = $def;
  }

  function MakeVar($string)
  {
    $maxvarlen = 24;
    $string = strtolower(preg_replace('/\s+/','_',$string));
    $string = strtolower(preg_replace('/\W/','_',$string));
    if (strlen($string) > $maxvarlen)
      {
	$string = substr($string,0,$maxvarlen);
	$pos = strrpos($string,'_');
	if ($pos !== false)
	  {
	    $string = substr($string,0,$pos);
	  }
      }
    return $string;
  }

  function createSampleTemplateJavascript($fieldName='opt_email_template', $includeHTML=true, $includeText=true)
  {
    $jsCode = "<script type=\"text/javascript\">\n
function populate(formname)
    {
    var fname = 'ID".$fieldName."';
    formname[fname].value=|TEMPLATE|;
    }
function populate_html(formname)
    {
    var fname = 'ID".$fieldName."';
    formname[fname].value=|HTMLTEMPLATE|;
	 }
</script>";
	if ($includeText)
		{
		$jsCode .= "<input type=\"button\" value=\"".
$this->module_ptr->Lang('title_create_sample_template')."\" onClick=\"javascript:populate(this.form)\" />";
		}
	if ($includeHTML)
		{
		$jsCode .= "<input type=\"button\" value=\"".
$this->module_ptr->Lang('title_create_sample_html_template')."\" onClick=\"javascript:populate_html(this.form)\" />";
		}
  return $jsCode;
  }


  function createSampleTemplate($htmlish=false,$email=true)
  {
    $mod = &$this->module_ptr;
    $ret = "";
	if ($email)
		{
    	if ($htmlish)
    		{
			$ret .= "<h1>".$mod->Lang('email_default_template')."</h1>\n";
			}
	 	else
	 		{
			$ret .= $mod->Lang('email_default_template')."\n";
			}
    	foreach($this->templateVariables as $thisKey=>$thisVal)
      		{
			if ($htmlish)
				{
				$ret .= '<strong>'.$thisVal.'</strong>: '.$thisKey."<br />\n";
				}
			else
				{
				$ret .= $thisVal.': '.$thisKey."\n";
				}
      		}
     	if ($htmlish)
     	  	{
		  	$ret .= "\n<hr />\n";
	  	  	}
	  	else
	  	  	{
    	  	$ret .= "\n-------------------------------------------------\n";
    	  	}
    	  }
	else
		{
		if ($htmlish)
			{
			$ret .= '<h2>';
			}
		$ret .= $mod->Lang('thanks');
		if ($htmlish)
			{
			$ret .= '</h2>';
			}
		}
    $others = &$this->GetFields();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {
	  if ($htmlish)
     	  {
  			$ret .= '<strong>'.$others[$i]->GetName() . '</strong>: {$fld_' .   			$others[$i]->GetId(). "}<br />\n";
  		  }
  	  else
  	  	  {
	     $ret .= $others[$i]->GetName() . ': {$fld_' .$others[$i]->GetId() 	     . "}\n";
	     }
	  }
      }
    return $ret;
  }


  function AdminTemplateHelp($formDescriptor,$fieldName='opt_email_template',
  	$includeHTML=true, $includeText=true)
  {
    $mod = &$this->module_ptr;
    $ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_template').'</th></tr>';
    $ret .= '<tr><th>'.$mod->Lang('help_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
    $odd = false;
    foreach($this->templateVariables as $thisKey=>$thisVal)
      {
		$ret .= '<tr><td class="'.($odd?'odd':'even').
		'">'.$thisKey.'</td><td class="'.($odd?'odd':'even').
		'">'.$thisVal.'</td></tr>';
      $odd = ! $odd;
      }

    $others = &$this->GetFields();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {                
	    $ret .= '<tr><td class="'.($odd?'odd':'even').
	    '">{$'.$this->MakeVar($others[$i]->GetName()).
	    '} / {$fld_'.
	    $others[$i]->GetId().
	    '}</td><td class="'.($odd?'odd':'even').
	    '">' .$others[$i]->GetName() . '</td></tr>';
	  	$odd = ! $odd;
	  }
      }
       	
    $ret .= '<tr><td colspan="2">'.$mod->Lang('help_other_fields').'</td></tr>';
        
    $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate(false));
    $escapedSampleHTML = preg_replace('/\'/',"\\'",$this->createSampleTemplate(true));
    $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
    $escapedSampleHTML = preg_replace('/\n/',"\\n'+\n'", $escapedSampleHTML);
    
    $sampleTemplateCode = preg_replace('/\|TEMPLATE\|/',"'".$escapedSample."'",
    	$this->createSampleTemplateJavascript($fieldName, $includeHTML, $includeText));
    $sampleTemplateCode = preg_replace('/\|HTMLTEMPLATE\|/',"'".$escapedSampleHTML."'",
    	$sampleTemplateCode);
    $sampleTemplateCode = preg_replace('/ID/',$formDescriptor,
    	$sampleTemplateCode);
    $ret .= '<tr><td colspan="2">'.$sampleTemplateCode.'</td></tr>';
    $ret .= '</table>';
    return $ret;
  }


  function Validate()
  {
    $validated = true;
    $message = '';
    $formPageCount=1;
    $valPage = $this->Page - 1;
    for($i=0;$i<count($this->Fields);$i++)
      {
	if ($this->Fields[$i]->GetFieldType() == 'PageBreakField')
	  {
	    $formPageCount++;
	  }
	if ($valPage != $formPageCount)
	  {
	    continue;
	  }
	if (! $this->Fields[$i]->IsDisposition() &&
	    $this->Fields[$i]->IsRequired() &&
	    $this->Fields[$i]->HasValue() == false)
	  {
	    $message .= "<h4>".$this->module_ptr->Lang('please_enter_a_value',$this->Fields[$i]->GetName())."</h4>\n";
	    $validated = false;
	    $this->Fields[$i]->SetOption('is_valid',false);
	  }
	else if ($this->Fields[$i]->GetValue() != $this->module_ptr->Lang('unspecified'))
	  { 
	    $res = $this->Fields[$i]->Validate();
	    if ($res[0] != true)
	      {
		$message .= "<h4>". $res[1]."</h4>\n";
		$validated = false;
		$this->Fields[$i]->SetOption('is_valid',false);
	      }
	    else
	      {
		$this->Fields[$i]->SetOption('is_valid',true);
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
  function Dispose($returnid)
  {
    // first, we run all field methods that will modify other fields
    for($i=0;$i<count($this->Fields);$i++)
      {
	if ($this->Fields[$i]->ModifiesOtherFields())
	  {
	    $this->Fields[$i]->ModifyOtherFields();
	  }
      }

    $resArray = array();
    $retCode = true;
    // for each form disposition pseudo-field, dispose the form results
    for($i=0;$i<count($this->Fields);$i++)
      {
	if ($this->Fields[$i]->IsDisposition() && $this->Fields[$i]->DispositionIsPermitted())
	  {
	    $res = $this->Fields[$i]->DisposeForm($returnid);
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
	return "\n<!-- Start FormBuilder Module (".$this->module_ptr->GetVersion().") -->\n";
      }
  }

  function RenderFormFooter()
  {
    if ($this->module_ptr->GetPreference('show_version',0) == 1)
      {
	return "\n<!-- End FormBuilder Module -->\n";
      }
  }


  // returns a string.
  function RenderForm($id, &$params, $returnid)
  {
    $mod = $this->module_ptr;
    if ($this->Id == -1)
      {
	return "<!-- no form -->\n";
      }
    if ($this->loaded != 'full')
      {
	$this->Load($this->Id,$params,true);
      }
    $reqSymbol = $this->GetAttr('required_field_symbol','*');
				
    $mod->smarty->assign('title_page_x_of_y',$mod->Lang('title_page_x_of_y',array($this->Page,$this->formTotalPages)));
		
    $mod->smarty->assign('css_class',$this->GetAttr('css_class',''));
    $mod->smarty->assign('total_pages',$this->formTotalPages);
    $mod->smarty->assign('this_page',$this->Page);
    $mod->smarty->assign('form_name',$this->Name);
    $mod->smarty->assign('form_id',$this->Id);
		
    $hidden = $mod->CreateInputHidden($id, 'form_id', $this->Id);
    $hidden .= $mod->CreateInputHidden($id, 'continue', ($this->Page + 1));
    if (isset($params['browser_id']))
      {
	$hidden .= $mod->CreateInputHidden($id,'browser_id',$params['browser_id']);
      }
    if ($this->Page > 1)
      {
	$hidden .= $mod->CreateInputHidden($id, 'previous', ($this->Page - 1));
      }
    if ($this->Page == $this->formTotalPages)
      {
	$hidden .= $mod->CreateInputHidden($id, 'done', 1);
      }
    $fields = array();
    $formPageCount = 1;
    for ($i=0; $i < count($this->Fields); $i++)
      {
	$thisField = &$this->Fields[$i];
	if ($thisField->GetFieldType() == 'PageBreakField')
	  {
	    $formPageCount++;
	  }
	if ($formPageCount != $this->Page)
	  {
	    $testIndex = '_'.$this->Fields[$i]->GetId();
	    if (!isset($params[$testIndex]))
	      {
		// do we need to write something?
	      }
	    elseif (is_array($params[$testIndex]))
	      {
		foreach ($params[$testIndex] as $val)
		  {
		    $hidden .= $mod->CreateInputHidden($id,
						       $testIndex.'[]',
						       htmlspecialchars($val,ENT_QUOTES));
		  }
	      }
	    else
	      {
		$hidden .= $mod->CreateInputHidden($id,
						   $testIndex,
						   htmlspecialchars($params[$testIndex],ENT_QUOTES));
	      }
	    continue;
	  }
	$oneset = new stdClass();
	$oneset->display = $thisField->DisplayInForm()?1:0;
	$oneset->required = $thisField->IsRequired()?1:0;
	$oneset->required_symbol = $thisField->IsRequired()?$reqSymbol:'';
	$oneset->css_class = $thisField->GetOption('css_class');
	$oneset->valid = $thisField->GetOption('is_valid',true)?1:0;
	$oneset->hide_name = 0;
	if( (!$thisField->HasLabel()) || $thisField->HideLabel() )
	  {
	    $oneset->hide_name = 1;
	  }
	$oneset->has_label = $thisField->HasLabel();
	$oneset->needs_div = $thisField->NeedsDiv();
	$oneset->name = $thisField->GetName();
	$oneset->input = $thisField->GetFieldInput($id, $params, $returnid);
	$oneset->smarty_eval = $thisField->GetSmartyEval()?1:0;

	$oneset->input_id = $id.'_'.$thisField->GetID();
	$oneset->multiple_parts = $thisField->HasMultipleFormComponents()?1:0;
	$oneset->label_parts = $thisField->LabelSubComponents()?1:0;
	$oneset->type = $thisField->GetDisplayType();
	$mod->smarty->assign($thisField->GetName(),$oneset);
	array_push($fields,$oneset);
      }

    $mod->smarty->assign_by_ref('hidden',$hidden);
    $mod->smarty->assign_by_ref('fields',$fields);

    $jsStr = '';
    $jsTrigger = '';
    if ($this->GetAttr('input_button_safety','0') == '1')
      {
	$jsStr = '<script type="text/javascript">
    var submitted = 0;
    function LockButton ()
       {
       var ret = false;
       if ( ! submitted )
          {
           var item = document.getElementById("fbsubmit");
           if (item != null)
             {
             setTimeout(function() {item.disabled = true}, 0);
             }
           submitted = 1;
           ret = true;
          }
        return ret;
        }
</script>';
      $jsTrigger = " onclick='return LockButton()'";
      }

    if ($this->Page > 1)
      {
	$mod->smarty->assign('prev',$mod->CreateInputSubmit($id, 'prev',
							    $this->GetAttr('prev_button_text'),
							    'class="fbsubmit_prev"'));
      }
    else
      {
	$mod->smarty->assign('prev','');
      }

    if ($this->Page < $formPageCount)
      {
	$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, 'submit',
							      $this->GetAttr('next_button_text'),
							      'class="fbsubmit_next"'));
      }
    else
      {
      $captcha = &$mod->getModuleInstance('Captcha');
      if ($this->GetAttr('use_captcha','0')== '1' && $captcha != null)
         {
         $mod->smarty->assign('graphic_captcha',$captcha->getCaptcha());
         $mod->smarty->assign('title_captcha',$this->GetAttr('title_user_captcha',$mod->Lang('title_user_captcha')));
         $mod->smarty->assign('input_captcha',$mod->CreateInputText($id, 'captcha_phrase',''));
         $mod->smarty->assign('has_captcha','1');
         }
      else
         {
         $mod->smarty->assign('has_captcha','0');
         }
	   $mod->smarty->assign('submit',$jsStr . $mod->CreateInputSubmit($id, 'submit',
				$this->GetAttr('submit_button_text'),
				'class="fbsubmit" id="fbsubmit"'.$jsTrigger));
      }
	  return $mod->ProcessTemplateFromDatabase('fb_'.$this->Id);
  }

  function LoadForm($loadDeep=false)
  {
    return $this->Load($this->Id, array(), $loadDeep);
  }

  function Load($formId, &$params, $loadDeep=false)
  {
    $sql = 'SELECT * FROM '.cms_db_prefix().'module_fb_form WHERE form_id=?';
    $rs = $this->module_ptr->dbHandle->Execute($sql, array($formId));
    if($rs && $rs->RecordCount() > 0)
      {
	$result = $rs->FetchRow();
	$this->Id = $result['form_id'];
	if (!isset($params['form_name']) || empty($params['form_name']))
	  {
	    $this->Name = $result['name'];
	  }
	if (!isset($params['form_alias']) || empty($params['form_alias']))
	  {
	    $this->Alias = $result['alias'];
	  }
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
	$this->Attrs[$result['name']] = $result['value'];
      }
          
    $this->loaded = 'summary';

    if (isset($params['response_id']))
      {
	$loadDeep = true;
      }
			
    if ($loadDeep)
      {
	// if it's a stored form, load the results -- but we need to manually merge them,
	// since $params[] should override the database value (say we're resubmitting a form)
	if (isset($params['response_id']))
	  {
	    $loadParams = array('response_id'=>$params['response_id']);
	    $this->LoadResponseValues($loadParams);
	    foreach ($loadParams as $thisParamKey=>$thisParamValue)
	      {
		if (! isset($params[$thisParamKey]))
		  {
		    $params[$thisParamKey] = $thisParamValue;
		  }
	      }
	  }

	$sql = 'SELECT * FROM ' . cms_db_prefix().
	  'module_fb_field WHERE form_id=? ORDER BY order_by';
	$rs = $this->module_ptr->dbHandle->Execute($sql, array($formId));
	$result = array();
	if ($rs && $rs->RecordCount() > 0)
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
		if (
		    ( 
		     isset($thisRes['field_id']) &&
		      (
		       isset($params['_'.$thisRes['field_id']]) ||
		       isset($params['__'.$thisRes['field_id']])
		       )
		      ) ||
		    (isset($thisRes['field_id']) &&
		     isset($params['value_'.$thisRes['name']])) ||
		    (
		     isset($params['field_id']) && isset($thisRes['field_id']) &&
		     $params['field_id'] == $thisRes['field_id']
		     )
		    )
		  {
		    $thisRes = array_merge($thisRes,$params);
		  }
		$this->Fields[$fieldCount] = &$this->NewField($thisRes);
		$fieldCount++;
	      }
	  }
	$this->loaded = 'full';
      }

		
    for ($i=0; $i < count($this->Fields); $i++)
      {
	if ($this->Fields[$i]->Type == 'PageBreakField')
	  {
	    $this->formTotalPages++;
	  }
      }

    return true;
  }

  function ImportXML(&$params)
  {
  	// xml_parser_create, xml_parse_into_struct
  	$parser = xml_parser_create('');
   xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
   xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 0 ); // was 1
	xml_parse_into_struct($parser, file_get_contents($params['xml_file']), $vals);
	xml_parser_free($parser);
	$elements = array();
	$stack = array();
	foreach ( $vals as $tag )
		{
		$index = count( $elements );
		if ( $tag['type'] == "complete" || $tag['type'] == "open" )
			{
			$elements[$index] = array();
			$elements[$index]['name'] = $tag['tag'];
			$elements[$index]['attributes'] = empty($tag['attributes']) ? "" : $tag['attributes'];
			$elements[$index]['content']    = empty($tag['value']) ? "" : $tag['value'];
			if ( $tag['type'] == "open" )
				{    # push
				$elements[$index]['children'] = array();
				$stack[count($stack)] = &$elements;
				$elements = &$elements[$index]['children'];
				}
        }
		if ( $tag['type'] == "close" )
			{    # pop
			$elements = &$stack[count($stack) - 1];
			unset($stack[count($stack) - 1]);
			}
		}
	if (!isset($elements[0]) || !isset($elements[0]) || !isset($elements[0]['attributes']))
		{
		//parsing failed, or invalid file.
		return false;
		}
	$params['form_id'] = -1; // override any form_id values that may be around
	$formAttrs = &$elements[0]['attributes'];
	if ($this->inXML($formAttrs['name']))
		{
		$this->SetName($formAttrs['name']);
		}
	if ($this->inXML($formAttrs['alias']))
		{
		$this->SetAlias($formAttrs['alias']);
		}
	// populate the attributes first, so we can save the form and then start adding fields to it.
	foreach ($elements[0]['children'] as $thisChild)
		{
		if ($thisChild['name'] == 'attribute')
			{
			$this->SetAttr($thisChild['attributes']['key'], $thisChild['content']);
			}
		}
	if( isset($params['import_formname']) && 
	    trim($params['import_formname']) != '')
	  {
	    $this->SetName(trim($params['import_formname']));
	  }
	if( isset($params['import_formalias']) &&
	    trim($params['import_formname']) != '')
	  {
	    $this->SetAlias(trim($params['import_formalias']));
	  }
	$this->Store();
	$params['form_id'] = $this->GetId();
	foreach ($elements[0]['children'] as $thisChild)
		{
		if ($thisChild['name'] == 'field')
			{
			$newField = new fbFieldBase($this, $params);
			$fieldAttrs = &$thisChild['attributes'];
			if ($this->inXML($fieldAttrs['name']))
				{
				$newField->SetName($fieldAttrs['name']);
				}
			if ($this->inXML($fieldAttrs['type']))
				{
				$newField->SetFieldType($fieldAttrs['type']);
				}
			$newField->SetValidationType($fieldAttrs['validation_type']);
			if ($this->inXML($fieldAttrs['order_by']))
				{
				$newField->SetOrder($fieldAttrs['order_by']);
				}
			if ($this->inXML($fieldAttrs['required']))
				{
				$newField->SetRequired($fieldAttrs['required']);
				}
			if ($this->inXML($fieldAttrs['hide_label']))
				{
				$newField->SetHideLabel($fieldAttrs['hide_label']);
				}
			foreach ($thisChild['children'] as $thisOpt)
				{
				$newField->PushOptionElement($thisOpt['attributes']['name'],
					$thisOpt['content']);
				}
			$newField->Store(true);
			}
		}
	return true;	
  }

  function inXML(&$var)
  {
  		if (isset($var) && strlen($var) > 0)
  			{
			return true;
			}
		else
			{
			return false;
			}
  }

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
    $res = $this->module_ptr->dbHandle->Execute($sql, array($this->Id));
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
	if ($thisAttrKey == 'form_template')
	  {
	    $this->module_ptr->SetTemplate('fb_'.$this->Id,$thisAttrValue);
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
    if ($this->loaded != 'full')
      {
	$this->Load($this->Id,array(),true);
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
    $classFile='';
    if (strlen($classDirPrefix) > 0)
      {
	$classFile = $classDirPrefix .'/'.$type.'.class.php';
      }
    else
      {
	$classFile = $type.'.class.php';
      }
    require_once dirname(__FILE__).'/'.$classFile;
    // class names are prepended with "fb" to prevent namespace clash.
    return ( 'fb'.$type );
  }

  function AddEditForm($id, $returnid, $message='')
  {
    $mod = &$this->module_ptr;
    $mod->smarty->assign('message',$message);
    $mod->smarty->assign('formstart',
			 $mod->CreateFormStart($id, 'admin_store_form', $returnid));
    $mod->smarty->assign('formid',
			 $mod->CreateInputHidden($id, 'form_id', $this->Id));
    $mod->smarty->assign('tab_start',$mod->StartTabHeaders().
         $mod->SetTabHeader('maintab',$mod->Lang('tab_main')).
         $mod->SetTabHeader('submittab',$mod->Lang('tab_submit')).
         $mod->SetTabHeader('symboltab',$mod->Lang('tab_symbol')).
         $mod->SetTabHeader('captchatab',$mod->Lang('tab_captcha')).
         $mod->SetTabHeader('templatelayout',$mod->Lang('tab_templatelayout')).
         $mod->SetTabHeader('submittemplate',$mod->Lang('tab_submissiontemplate')).
			$mod->EndTabHeaders() . $mod->StartTabContent());
	  
    $mod->smarty->assign('tabs_end',$mod->EndTabContent());
    $mod->smarty->assign('maintab_start',$mod->StartTab("maintab"));
    $mod->smarty->assign('submittab_start',$mod->StartTab("submittab"));
    $mod->smarty->assign('symboltab_start',$mod->StartTab("symboltab"));
    $mod->smarty->assign('templatetab_start',$mod->StartTab("templatelayout"));
    $mod->smarty->assign('submittemplatetab_start',$mod->StartTab("submittemplate"));
    $mod->smarty->assign('captchatab_start',$mod->StartTab("captchatab"));
    $mod->smarty->assign('tab_end',$mod->EndTab());
    $mod->smarty->assign('form_end',$mod->CreateFormEnd());
    $mod->smarty->assign('title_form_name',$mod->Lang('title_form_name'));
    $mod->smarty->assign('input_form_name',
			 $mod->CreateInputText($id, 'form_name',
					       $this->Name, 50));
    $mod->smarty->assign('title_form_unspecified',$mod->Lang('title_form_unspecified'));
    $mod->smarty->assign('input_form_unspecified',
			 $mod->CreateInputText($id, 'forma_unspecified',
					       $this->GetAttr('unspecified',$mod->Lang('unspecified')), 50));
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
	$mod->smarty->assign('title_form_main',
			 $mod->Lang('title_form_main'));
    $mod->smarty->assign('title_field_name',
			 $mod->Lang('title_field_name'));
    $mod->smarty->assign('title_field_type',
			 $mod->Lang('title_field_type'));
    $mod->smarty->assign('title_field_type',
			 $mod->Lang('title_field_type'));
    $mod->smarty->assign('title_form_template',
			 $mod->Lang('title_form_template'));
    $mod->smarty->assign('title_list_delimiter',
			 $mod->Lang('title_list_delimiter'));
    $mod->smarty->assign('title_redirect_page',
			 $mod->Lang('title_redirect_page'));
			 
    $mod->smarty->assign('title_submit_action',
			 $mod->Lang('title_submit_action'));
    $mod->smarty->assign('title_submit_response',
			 $mod->Lang('title_submit_response'));


    $mod->smarty->assign('title_submit_actions',
			 $mod->Lang('title_submit_actions'));
    $mod->smarty->assign('title_submit_labels',
			 $mod->Lang('title_submit_labels'));
    $mod->smarty->assign('title_submit_help',
$mod->cms->variables['admintheme']->DisplayImage('icons/system/info.gif','true','','','systemicon').
			 $mod->Lang('title_submit_help'));
	$mod->smarty->assign('title_submit_response_help',
$mod->cms->variables['admintheme']->DisplayImage('icons/system/info.gif','true','','','systemicon').
			$mod->Lang('title_submit_response_help'));

    $submitActions = array($mod->Lang('display_text')=>'text',
         $mod->Lang('redirect_to_page')=>'redir');
    $mod->smarty->assign('input_submit_action',
          $mod->CreateInputRadioGroup($id, 'forma_submit_action', $submitActions, $this->GetAttr('submit_action','text')));

    $captcha = &$mod->getModuleInstance('Captcha');
    if ($captcha == null)
         {
         $mod->smarty->assign('title_install_captcha',
			   $mod->Lang('title_captcha_not_installed'));
         $mod->smarty->assign('captcha_installed',0);
         }
    else
         {
         $mod->smarty->assign('title_use_captcha',
			   $mod->Lang('title_use_captcha'));
         $mod->smarty->assign('captcha_installed',1);

         $mod->smarty->assign('input_use_captcha',$mod->CreateInputHidden($id,'forma_use_cpatcha','0').
			   $mod->CreateInputCheckbox($id,'forma_use_captcha','1',$this->GetAttr('use_captcha','0')).
			   $mod->Lang('title_use_captcha_help'));
			}
    $mod->smarty->assign('title_information',$mod->Lang('information'));
    $mod->smarty->assign('title_order',$mod->Lang('order'));
    $mod->smarty->assign('title_field_required_abbrev',$mod->Lang('title_field_required_abbrev'));
    $mod->smarty->assign('hasdisposition',$this->HasDisposition()?1:0);
    $maxOrder = 1;
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
	$last = $this->GetFieldCount();
	foreach ($this->Fields as $thisField)
	  {
	    $oneset = new stdClass();
	    $oneset->rowclass = $currow;
	    $oneset->name = $mod->CreateLink($id, 'admin_add_edit_field', '', $thisField->GetName(), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
	    $oneset->type = $thisField->GetDisplayType();
	    if ($thisField->IsDisposition() ||
		!$thisField->DisplayInForm() ||
		$thisField->IsNonRequirableField())
	      {
		$oneset->disposition = '.';
	      }
	    else if ($thisField->IsRequired())
	      {
		$oneset->disposition = $mod->CreateLink($id, 'admin_update_field_required', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/true.gif','true','','','systemicon'), array('form_id'=>$this->Id,'active'=>'off','field_id'=>$thisField->GetId()));
	      }
	    else
	      {
		$oneset->disposition = $mod->CreateLink($id, 'admin_update_field_required', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/false.gif','false','','','systemicon'), array('form_id'=>$this->Id,'active'=>'on','field_id'=>$thisField->GetId()));
	      }
	    $oneset->field_status = $thisField->StatusInfo();
	    if ($count > 1)
	      {
		$oneset->up = $mod->CreateLink($id, 'admin_update_field_order', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/arrow-u.gif','up','','','systemicon'), array('form_id'=>$this->Id,'dir'=>'up','field_id'=>$thisField->GetId()));
	      }
	    else
	      {
		$oneset->up = '&nbsp;';
	      }
	    if ($count < $last)
	      {
		$oneset->down=$mod->CreateLink($id, 'admin_update_field_order', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/arrow-d.gif','down','','','systemicon'), array('form_id'=>$this->Id,'dir'=>'down','field_id'=>$thisField->GetId()));
	      }
	    else
	      {
		$oneset->down = '&nbsp;';
	      }
	    $oneset->editlink = $mod->CreateLink($id, 'admin_add_edit_field', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/edit.gif',$mod->Lang('edit'),'','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
	    $oneset->deletelink = $mod->CreateLink($id, 'admin_delete_field', '', $mod->cms->variables['admintheme']->DisplayImage('icons/system/delete.gif',$mod->Lang('delete'),'','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id),$mod->Lang('are_you_sure_delete_field',$thisField->GetName()));
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
			     $mod->CreateLink($id, 'admin_add_edit_field', $returnid,$mod->cms->variables['admintheme']->DisplayImage('icons/system/newobject.gif',$mod->Lang('title_add_new_field'),'','','systemicon'),array('form_id'=>$this->Id, 'order_by'=>$maxOrder), '', false) . $mod->CreateLink($id, 'admin_add_edit_field', $returnid,$mod->Lang('title_add_new_field'),array('form_id'=>$this->Id, 'order_by'=>$maxOrder), '', false));
	$mod->smarty->assign('order_field_link',
			     $mod->CreateLink($id, 'admin_reorder_form', $returnid,$mod->cms->variables['admintheme']->DisplayImage('icons/system/reorder.gif',$mod->Lang('title_reorder_form'),'','','systemicon'),array('form_id'=>$this->Id), '', false) . $mod->CreateLink($id, 'admin_reorder_form', $returnid,$mod->Lang('title_reorder_form'),array('form_id'=>$this->Id), '', false));
			     			     
	if ($mod->GetPreference('enable_fastadd',1) == 1)
	  {
	    $mod->smarty->assign('fastadd',1);
	    $mod->smarty->assign('title_fastadd',$mod->Lang('title_fastadd'));
	    $typeInput = "<script type=\"text/javascript\">
function fast_add(field_type)
{
	var type=field_type.options[field_type.selectedIndex].value;
	var link = '".$mod->CreateLink($id, 'admin_add_edit_field', $returnid,'',array('form_id'=>$this->Id, 'order_by'=>$maxOrder), '', true,true)."&".$id."field_type='+type;
	this.location=link;
	return true;
}
</script>";
	    $typeInput = str_replace('&amp;','&',$typeInput); 
	    $mod->initialize();
	    $mod->smarty->assign('input_fastadd',$typeInput.$mod->CreateInputDropdown($id, 'field_type',array_merge(array($mod->Lang('select_type')=>''),$mod->field_types), -1,'', 'onchange="fast_add(this)"'));
	  }							
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
    $mod->smarty->assign('link_notready',"<strong>".$mod->Lang('title_not_ready1')."</strong> ".$mod->Lang('title_not_ready2')." ".$mod->CreateLink($id, 'admin_add_edit_field', $returnid,$mod->Lang('title_not_ready_link'),array('form_id'=>$this->Id, 'order_by'=>$maxOrder,'dispose_only'=>1), '', false, false,'class="module_fb_link"')." ".$mod->Lang('title_not_ready3')
			 );

    $mod->smarty->assign('title_form_submit_button',
			 $mod->Lang('title_form_submit_button'));
    $mod->smarty->assign('input_form_submit_button',
			 $mod->CreateInputText($id, 'forma_submit_button_text',
					       $this->GetAttr('submit_button_text',$mod->Lang('button_submit')), 35, 35));
    $mod->smarty->assign('title_submit_button_safety',
			 $mod->Lang('title_submit_button_safety_help'));
    $mod->smarty->assign('input_submit_button_safety',$mod->CreateInputHidden($id,'forma_input_button_safety','0').
			 $mod->CreateInputCheckbox($id,'forma_input_button_safety','1',$this->GetAttr('input_button_safety','0')).
			 $mod->Lang('title_submit_button_safety'));
    $mod->smarty->assign('title_form_prev_button',
			 $mod->Lang('title_form_prev_button'));
    $mod->smarty->assign('input_form_prev_button',
			 $mod->CreateInputText($id, 'forma_prev_button_text',
					       $this->GetAttr('prev_button_text',$mod->Lang('button_previous')), 35, 35));

    $mod->smarty->assign('input_title_user_captcha',
			 $mod->CreateInputText($id, 'forma_title_user_captcha',
                      $this->GetAttr('title_user_captcha',$mod->Lang('title_user_captcha')),35,80));
    $mod->smarty->assign('title_title_user_captcha',$mod->Lang('title_title_user_captcha'));

    $mod->smarty->assign('input_title_user_captcha_error',
			 $mod->CreateInputText($id, 'forma_captcha_wrong',
                      $this->GetAttr('title_user_captcha_error',$mod->Lang('wrong_captcha')),35,80));
    $mod->smarty->assign('title_user_captcha_error',$mod->Lang('title_user_captcha_error'));

    $mod->smarty->assign('title_form_next_button',
			 $mod->Lang('title_form_next_button'));
    $mod->smarty->assign('input_form_next_button',
			 $mod->CreateInputText($id, 'forma_next_button_text',
					       $this->GetAttr('next_button_text',$mod->Lang('button_continue')), 35, 35));
    $mod->smarty->assign('title_form_required_symbol',
			 $mod->Lang('title_form_required_symbol'));
    $mod->smarty->assign('input_form_required_symbol',
			 $mod->CreateInputText($id, 'forma_required_field_symbol',
					       $this->GetAttr('required_field_symbol','*'), 50));
    $mod->smarty->assign('input_list_delimiter',
			 $mod->CreateInputText($id, 'forma_list_delimiter',
					       $this->GetAttr('list_delimiter',','), 50));

    global $gCms;
    $contentops =& $gCms->GetContentOperations();
    $mod->smarty->assign('input_redirect_page',$contentops->CreateHierarchyDropdown('',$this->GetAttr('redirect_page','0'), $id.'forma_redirect_page'));

    $mod->smarty->assign('input_form_template',
			 $mod->CreateTextArea(false, $id,
					      $this->GetAttr('form_template',$this->DefaultTemplate()), 'forma_form_template'));
					      
    $mod->smarty->assign('input_submit_response',
			 $mod->CreateTextArea(false, $id,
					      $this->GetAttr('submit_response',''), 'forma_submit_response','module_fb_area_wide'));
	$mod->smarty->assign('help_submit_response',
		$this->AdminTemplateHelp($id,'forma_submit_response',true,false));
    return $mod->ProcessTemplate('AddEditForm.tpl');
  }

    function &NewField(&$params)
      {
	//$aefield = new fbFieldBase($this,$params);
	$aefield = false;
        if (isset($params['field_id']) && $params['field_id'] != -1 )
	  {
            // we're loading an extant field
	    $sql = 'SELECT type FROM ' . cms_db_prefix() . 'module_fb_field WHERE field_id=?';
	    $rs = $this->module_ptr->dbHandle->Execute($sql, array( $params['field_id']));
	    if($rs && $result = $rs->FetchRow())
	      {				
		if ($result['type'] != '')
		  {
		    $className = $this->MakeClassName($result['type'] , '');
		    $aefield = new $className($this, $params);
		    $aefield->LoadField($params);
		  }
	      }
	  }
	if ($aefield === false)
	  {
	    // new field
	    if (! isset($params['field_type']))
	      {
		// unknown field type
		$aefield = new fbFieldBase($this,$params);
	      }
	    else
	      {
		// specified field type via params
            	$className = $this->MakeClassName($params['field_type'], '');
            	$aefield = new $className($this, $params);
	      }
	  }
	return $aefield;
      }



  function AddEditField($id, &$aefield, $dispose_only, $returnid, $message='')
  {
    $mod = $this->module_ptr;
		
    $mod->smarty->assign('message',$message);
    $mod->smarty->assign('backtoform_nav',$mod->CreateLink($id, 'admin_add_edit_form', $returnid, $mod->Lang('link_back_to_form'), array('form_id'=>$this->Id)));
    $mainList = array();
    $advList = array();
    $baseList = $aefield->PrePopulateBaseAdminForm($id, $dispose_only);
    if ($aefield->GetFieldType() == '')
      {
	// still need type
	$mod->smarty->assign('start_form',$mod->CreateFormStart($id, 'admin_add_edit_field', $returnid));			
	$fieldList = array('main'=>array(),'adv'=>array());
      }
    else
      {
	// we have our type
	$mod->smarty->assign('start_form',$mod->CreateFormStart($id, 'admin_add_edit_field', $returnid));	
	$fieldList = $aefield->PrePopulateAdminForm($id);
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
	$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, 'aef_upd', $mod->Lang('update')));
      }
    else
      {
	$mod->smarty->assign('op',$mod->CreateInputHidden($id, 'op', $mod->Lang('added')));
	$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, 'aef_add', $mod->Lang('add')));
      }

    if ($aefield->HasAddOp())
      {
	$mod->smarty->assign('add',$mod->CreateInputSubmit($id,'aef_optadd',$aefield->GetOptionAddButton()));
      }
    else
      {
	$mod->smarty->assign('add','');
      }
    if ($aefield->HasDeleteOp())
      {
	$mod->smarty->assign('del',$mod->CreateInputSubmit($id,'aef_optdel',$aefield->GetOptionDeleteButton()));
      }
    else
      {
	$mod->smarty->assign('del','');
      }


    $mod->smarty->assign('hidden', $mod->CreateInputHidden($id, 'form_id', $this->Id) . $mod->CreateInputHidden($id, 'field_id', $aefield->GetId()) . $mod->CreateInputHidden($id, 'order_by', $aefield->GetOrder()).
			 $mod->CreateInputHidden($id,'set_from_form','1'));

    if (!$aefield->IsDisposition() && !$aefield->IsNonRequirableField())
      {
	$mod->smarty->assign('requirable',1);
      }
    else
      {
	$mod->smarty->assign('requirable',0);
      }
			
    if (isset($baseList['main']))
      {
	foreach ($baseList['main'] as $item)
	  {
	    $titleStr=$item[0];
	    $inputStr=$item[1];
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
    if (isset($baseList['adv']))
      {
	foreach ($baseList['adv'] as $item)
	  {
	    $titleStr = $item[0];
	    $inputStr = $item[1];
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
    if (isset($fieldList['main']))
      {
	foreach ($fieldList['main'] as $item)
	  {
	    $titleStr=$item[0];
	    $inputStr=$item[1];
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
	foreach ($fieldList['adv'] as $item)
	  {
	    $titleStr=$item[0];
	    $inputStr=$item[1];
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
		
    $aefield->PostPopulateAdminForm($mainList, $advList);
    $mod->smarty->assign('mainList',$mainList);
    $mod->smarty->assign('advList',$advList);
    return $mod->ProcessTemplate('AddEditField.tpl');
  }

  function MakeAlias($string, $isForm=false)
  {
    $string = trim(htmlspecialchars($string));
    //$string = preg_replace("/[_-\W]+/", "_", $string);
    //$string = trim($string, '_');
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
    $srcField = &$this->GetFieldByIndex($src_field_index);
    $destField = &$this->GetFieldByIndex($dest_field_index);
    $tmpOrderBy = $destField->GetOrder();
    $destField->SetOrder($srcField->GetOrder());
    $srcField->SetOrder($tmpOrderBy);
    //it seems this makes php4 go crazy fixed by reloading form before showing it again
    #        $this->Fields[$dest_field_index] = $srcField;
    #        $this->Fields[$src_field_index] = $destField;
    $srcField->Store();
    $destField->Store();
  }

  function &GetFields()
    {
      return $this->Fields;
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


  function DefaultTemplate()
  {
    return file_get_contents(dirname(__FILE__).'/../templates/RenderFormDefault.tpl');
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

  function ResetFields()
  {
    for($i=0;$i<count($this->Fields);$i++)
      {
	$this->Fields[$i]->ResetValue();
      }
  }
    
  // this will instantiate the form, and load the results
  function LoadResponse($response_id)
  {
    $db = &$this->module_ptr->dbHandle;
    // loading a response -- at this point, we check that the response
    // is for the correct form_id!
    $sql = 'SELECT form_id FROM ' . cms_db_prefix().
      'module_fb_resp where resp_id=?';
    if($result = $db->GetRow($sql, array($response_id)))
      {
	if ($result['form_id'] != $this->GetId())
	  {
	    return false;
	  }
		    
	$this->ResetFields();

	$sql = 'SELECT * FROM '.cms_db_prefix().
	  'module_fb_resp_val WHERE resp_id=? order by resp_val_id';
	$dbresult = $db->Execute($sql, array($response_id));
	while ($dbresult && $row = $dbresult->FetchRow())
	  {
	    $index = $this->GetFieldIndexFromId($row['field_id']);
            if( is_object($this->Fields[$index]) )
             {
	       $this->Fields[$index]->SetValue($row['value']);
             }
	  }
      }
    else
      {
	return false;
      }
  }   

  function LoadResponseValues(&$params)
  {
    $db = $this->module_ptr->dbHandle;
    // loading a response -- at this point, we check that the response
    // is for the correct form_id!
    $sql = 'SELECT form_id FROM ' . cms_db_prefix().
      'module_fb_resp where resp_id=?';
    if($result = $db->GetRow($sql, array($params['response_id'])))
      {
	if ($result['form_id'] != $this->GetId())
	  {
	    return false;
	  }
	$sql = 'SELECT * FROM '.cms_db_prefix().
	  'module_fb_resp_val WHERE resp_id=? order by resp_val_id';
	$dbresult = $db->Execute($sql, array($params['response_id']));
	while ($dbresult && $row = $dbresult->FetchRow())
	  { // was '__'		        	
	    if (isset($params['_'.$row['field_id']]) &&
		! is_array($params['_'.$row['field_id']]))
	      {
		$params['_'.$row['field_id']] = array($params['_'.$row['field_id']]);
		array_push($params['_'.$row['field_id']], $row['value']);
	      }
	    elseif (isset($params['_'.$row['field_id']]))
	      {
		array_push($params['_'.$row['field_id']], $row['value']);
	      }
	    else
	      {
		$params['_'.$row['field_id']] = $row['value'];
	      }
	  }
      }
    else
      {
	return false;
      }
  }   

  function DeleteResponse($response_id)
  {
    $db = $this->module_ptr->dbHandle;
    $sql = 'DELETE FROM ' . cms_db_prefix().
      'module_fb_resp_val where resp_id=?';
    $res = $db->Execute($sql, array($response_id));
    $sql = 'DELETE FROM '.cms_db_prefix().
      'module_fb_resp where resp_id=?';
    $res = $db->Execute($sql, array($response_id));	
  }

  function CheckResponse($form_id, $response_id, $code)
  {
    $db = $this->module_ptr->dbHandle;
    $sql = 'SELECT secret_code FROM ' . cms_db_prefix().
      'module_fb_resp where form_id=? and resp_id=?';
    if($result = $db->GetRow($sql, array($form_id,$response_id)))
      {
	if ($result['secret_code'] == $code)
	  {
	    return true;
	  }
      }
    return false;
  }


  function StoreResponse($response_id=-1,$approver='')
  {
    $db = $this->module_ptr->dbHandle;
    $fields = &$this->GetFields();
    $secret_code = '';
    $newrec = false;
    if ($response_id == -1)
      {
	$newrec = true;
      }
    if ($newrec)
      {
	// saving a new response
	$secret_code = substr(md5(session_id().'_'.time()),0,7);
	$response_id = $db->GenID(cms_db_prefix(). 'module_fb_resp_seq');
	$sql = 'INSERT INTO ' . cms_db_prefix().
	  'module_fb_resp (resp_id, form_id, submitted, secret_code)' .
	  ' VALUES (?, ?, ?, ?)';
	$res = $db->Execute($sql,
			    array($response_id,
				  $this->GetId(),
				  $this->clean_datetime($db->DBTimeStamp(time())),
				  $secret_code));
      }
    else if ($approver != '')
      {
	$sql = 'UPDATE ' . cms_db_prefix().
	  'module_fb_resp set user_approved=? where resp_id=?';
	$res = $db->Execute($sql,
			    array($this->clean_datetime($db->DBTimeStamp(time())),$response_id));
	audit(-1, (isset($name)?$name:""), $this->module_ptr->Lang('user_approved_submission',array($response_id,$approver)));
      }
    if (! $newrec)
      {
	// updating an old response, so we purge old values
	$sql = 'DELETE FROM ' . cms_db_prefix().
	  'module_fb_resp_val where resp_id=?';
	$res = $db->Execute($sql, array($response_id));
      }
    $sql = 'INSERT INTO ' . cms_db_prefix().
      'module_fb_resp_val (resp_val_id, resp_id, field_id, value)' .
      'VALUES (?, ?, ?, ?)';
    foreach ($fields as $thisField)
      {
	// set the response_id to be the attribute of the database disposition
	if ($thisField->GetFieldType() == 'DispositionDatabase')
	  {
	    $thisField->SetValue($response_id);
	  }
	elseif (! $thisField->DisplayInSubmission())
	  {
	    // skip if not a displayable field.
	    continue;
	  }
	if (! is_array($thisField->GetValue()))
	  {
	    $store = array();
	    array_push($store,$thisField->GetValue());
	  }
	else
	  {
	    $store = $thisField->GetValue();
	  }
	foreach ($store as $thisFieldVal)
	  {
	    if ($thisFieldVal !== false)
	      {
		$resp_val_id = $db->GenID(cms_db_prefix().
					  'module_fb_resp_val_seq');
		$res = $db->Execute($sql, array($resp_val_id,$response_id,
						$thisField->GetId(),$thisFieldVal));
	      }
	  } 
      }
    return array($response_id,$secret_code);
  }   
    
  function clean_datetime($dt)
  {
    return substr($dt,1,strlen($dt)-2);
  }
  
  function ExportXML($exportValues = false)
  {
	$xmlstr = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xmlstr .= "<form id=\"".$this->Id."\"\n";
	$xmlstr .= "\tname=\"".$this->Name."\"\n";
	$xmlstr .= "\talias=\"".$this->Alias."\">\n";
   foreach ($this->Attrs as $thisAttrKey=>$thisAttrValue)
      {
		$xmlstr .= "\t\t<attribute key=\"$thisAttrKey\"><![CDATA[$thisAttrValue]]></attribute>\n";
		}
	foreach($this->Fields as $thisField)
		{
			$xmlstr .= $thisField->ExportXML($exportValues);
		}
	$xmlstr .= "</form>\n";
	return $xmlstr;
  }
  
    
}

?>
