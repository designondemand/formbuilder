<?php
#-------------------------------------------------------------------------
# Module: FormBuilder
# Version: 1.0, released 2012
#
# Copyright (c) 2007, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple Forge:
# http://dev.cmsmadesimple.org/projects/formbuilder/
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

class fbForm {

	#---------------------
	# Attributes
	#---------------------	

	public $module_ptr = -1; // deprecate
	private $module_params = -1; // deprecate
	public $Id = -1;
	public $Name = '';
	public $Alias = '';
	public $Attrs;
	public $Fields;	
	private $loaded = 'not'; // deprecate
	private $formTotalPages = 0;
	private $Page;
	public $formState; // deprecate
	private $sampleTemplateCode;
	private $templateVariables;
	private static $ModuleInstance;

	#---------------------
	# Magic methods
	#---------------------		
											//Deprecate		 // Deprecate
	public function __construct(&$params, $loadDeep=false, $loadResp=false)
	{
	
		if (!isset($this->ModuleInstance)) {

			$mod = cmsms()->GetModuleinstance('FormBuilder');
			
			if(is_object($mod)) {
			
				$this->ModuleInstance = &$mod;
			}
		}	
	
		//$this->module_ptr = &$this; // deprecate
		$this->module_params = $params; // deprecate
		$this->Fields = array();
		$this->Attrs = array();
		$this->formState = 'new'; // deprecate
		
		// Deprecate
		// Stikki adding: $id overwrite possible with $param
		/*if ((!isset($this->module_ptr->module_id) || empty($this->module_ptr->module_id)) && isset($params['module_id'])) {

			$this->module_ptr->module_id = $params['module_id'];
		}*/	 
		
		// Move to Load method
		if (isset($params['form_id'])) {
			
			$this->Id = $params['form_id'];
		}
		
		// Move to Load method		
		if (isset($params['fbrp_form_alias'])) {
		  
			$this->Alias = $params['fbrp_form_alias'];
		}
		
		// Move to Load method
		if (isset($params['fbrp_form_name'])) {

			$this->Name = $params['fbrp_form_name'];
		}

		// Move to Load method
		// WTF is this?
		$fieldExpandOp = false;
		foreach($params as $pKey=>$pVal) {

			if (substr($pKey,0,9) == 'fbrp_FeX_' || substr($pKey,0,9) == 'fbrp_FeD_') {
			
				// expanding or shrinking a field
				$fieldExpandOp = true;
			}
		}

		
		// Move to Load method
		if ($fieldExpandOp) {

			$params['fbrp_done'] = 0;
			if (isset($params['fbrp_continue'])) {
			
				$this->Page = $params['fbrp_continue'] - 1;
			} else {

				$this->Page = 1;
			}
		} else {

			if (isset($params['fbrp_continue'])) {
			
				$this->Page = $params['fbrp_continue'];
			} else {
			
				$this->Page = 1;
			}
			
			if (isset($params['fbrp_prev']) && isset($params['fbrp_previous'])) {
			
				$this->Page = $params['fbrp_previous'];
				$params['fbrp_done'] = 0;
			}
		}
		
		// Move to Load method
		$this->formTotalPages = 1;
		if (isset($params['fbrp_done'])&& $params['fbrp_done']==1) {

			$this->formState = 'submit';
		}

		// Move to Load method		
		if (isset($params['fbrp_user_form_validate']) && $params['fbrp_user_form_validate']==true) {

			$this->formState = 'confirm';
		}

		// Only thing that actually should stay here.
		if ($this->Id != -1) {

			if (isset($params['response_id']) && $this->formState == 'submit') {
				
				$this->formState = 'update';
			}
			
			$this->Load($this->Id, $params, $loadDeep, $loadResp);
		}

		// Move to Load method
		foreach ($params as $thisParamKey=>$thisParamVal) {

			if (substr($thisParamKey,0,11) == 'fbrp_forma_') {
			
				$thisParamKey = substr($thisParamKey,11);
				$this->Attrs[$thisParamKey] = $thisParamVal;
			} else if ($thisParamKey == 'fbrp_form_template' && $this->Id != -1) {
			
				$this->ModuleInstance->SetTemplate('fb_'.$this->Id,$thisParamVal);
			}
		}	

		// Leave this for now, but get ridd of it later.
		$this->templateVariables = Array(
			'{$sub_form_name}'=>$this->Lang('title_form_name'),
			'{$sub_date}'=>$this->Lang('help_submission_date'),
			'{$sub_host}'=>$this->Lang('help_server_name'),
			'{$sub_source_ip}'=>$this->Lang('help_sub_source_ip'),
			'{$sub_url}'=>$this->Lang('help_sub_url'),
			'{$fb_version}'=>$this->Lang('help_fb_version'),
			'{$TAB}'=>$this->Lang('help_tab'),
		);
	} // end of __construct()

	#---------------------
	# Shadow module methods
	#---------------------	

	public function Lang() {
	
		$mod = $this->ModuleInstance;
		
		if(is_object($mod)) {
				
			$mod->LoadLangMethods();

			$args = func_get_args();
			array_unshift($args,'');
			$args[0] = &$mod;

			return call_user_func_array('cms_module_Lang', $args);
		}
	}
	
	#---------------------
	# get/set methods
	#---------------------		
	
	public final function getId()
	{
		return $this->Id;
	}

	public final function setId($id)
	{
		$this->Id = $id;
	}

	public final function getName()
	{
		return $this->Name;
	}

	public final function setName($name)
	{
		$this->Name = $name;
	}
	
	public final function getAlias()
	{
		return $this->Alias;
	}

	public final function setAlias($alias)
	{
		$this->Alias = $alias;
	}	
  
	public final function getFormState()
	{
		return $this->formState;
	}

	public final function getPageCount()
	{
		return $this->formTotalPages;
	}

	public final function getPageNumber()
	{
		return $this->Page;
	}

	public final function setAttributes($attrArray)
	{
		$this->Attrs = array_merge($this->Attrs,$attrArray);
	}

	public final function setTemplate($template)
	{
		$this->Attrs['form_template'] = $template;
		$this->ModuleInstance->SetTemplate('fb_'.$this->Id,$template);
	}

	public final function setAttr($attrname, $val)
	{
		$this->Attrs[$attrname] = $val;
	}

	public final function getAttr($attrname, $default="")
	{
		if (isset($this->Attrs[$attrname])) {
		
			return $this->Attrs[$attrname];
		} else {
		
			return $default;
		}
	}
	
	public final function getFieldCount()
	{
		return count($this->Fields);
	}
	
	public final function &GetFields()
	{
		return $this->Fields;
	}

	public final function &GetFieldById($field_id)
	{
		$index = -1;
		$ret = false;
		for ($i=0;$i<count($this->Fields);$i++)
		{
		if ($this->Fields[$i]->GetId() == $field_id)
		{
		$index = $i;
		}
		}
		if ($index != -1)
		{
		$ret = $this->Fields[$index];
		}
		return $ret;
	}


	public final function &GetFieldByAlias($field_alias)
	{
		$index = -1;
		$ret = false;
		for ($i=0;$i<count($this->Fields);$i++)
		{
		if ($this->Fields[$i]->GetAlias() == $field_alias)
		{
		$index = $i;
		}
		}
		if ($index != -1)
		{
		$ret = $this->Fields[$index];
		}
		return $ret;
	}

	public final function &GetFieldByName($field_name)
	{
		$index = -1;
		$ret = false;
		for ($i=0;$i<count($this->Fields);$i++)
		{
		if ($this->Fields[$i]->GetName() == $field_name)
		{
		$index = $i;
		}
		}
		if ($index != -1)
		{
		$ret = $this->Fields[$index];
		}
		return $ret;
	}

	public final function &GetFieldByIndex($field_index)
	{
		return $this->Fields[$field_index];
	}

	public final function GetFieldIndexFromId($field_id)
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
	

	#---------------------
	# Test methods
	#---------------------		

	function HasFieldNamed($name)
	{
		$ret = -1;
		foreach($this->Fields as $fld)
			{
			if ($fld->getName() == $name)
				{
				$ret = $fld->getId();
				}
			}
		return $ret;
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
	
	#---------------------
	# General methods
	#---------------------	
	
	function PageBack()
	{
		$this->Page--;
	}	
	
// dump params
  function DebugDisplay($params=array())
  {
    $tmp = $this->module_ptr;
    $this->module_ptr = '[mdptr]';

   if (isset($params['FORM']))
		{
		$fpt = $params['FORM'];
		$params['FORM'] = '[form_pointer]';
		}

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

   
  function AddTemplateVariable($name,$def)
  {
    $theKey = '{$'.$name.'}';
    $this->templateVariables[$theKey] = $def;
  }


  function createSampleTemplateJavascript($fieldName='opt_email_template', $button_text='', $suffix='')
  {
	$fldAlias = preg_replace('/[^\w\d]/','_',$fieldName).$suffix;
    $jsCode = "<script type=\"text/javascript\">\n
/* <![CDATA[ */
function populate".$fldAlias."(formname)
    {
    var fname = 'IDfbrp_".$fieldName."';
    formname[fname].value=|TEMPLATE|;
    }
/* ]]> */
</script>";
	$jsCode .= "<input type=\"button\" value=\"".
$button_text."\" onclick=\"javascript:populate".$fldAlias."(this.form)\" />";
  return $jsCode;
  }


	function fieldValueTemplate()
	{
		$mod = $this->module_ptr;
		$ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_computation').'</th></tr>';
		$ret .= '<tr><th>'.$mod->Lang('help_php_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
		$odd = false;
		$others = $this->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			// Removed by Stikki: BUT WHY?
			//if (!$others[$i]->HasMultipleFormComponents())
				//{
				$ret .= '<tr><td class="'.($odd?'odd':'even').'">$fld_'.$others[$i]->GetId().'</td><td class="'.($odd?'odd':'even').'">' .$others[$i]->GetName() . '</td></tr>';
				//}
			$odd = ! $odd;
			}
		return $ret;
	}

  function createSampleTemplate($htmlish=false,$email=true, $oneline=false,$header=false,$footer=false)
  {
    $mod = &$this;
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
				$ret .= '<strong>'.$thisVal.'</strong>: '.$thisKey."<br />";
				}
			else
				{
				$ret .= $thisVal.': '.$thisKey;
				}
			$ret .= "\n";
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
		
	elseif (!$oneline)
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
		
	elseif ($footer)
		 {
		 $ret .= '------------------------------------------\nEOF\n';
		 return $ret;
		 }			
		
    $others = $this->GetFields();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {
	  if ($others[$i]->GetAlias() != '')
		{
		$fldref = $others[$i]->GetAlias();
		}
	  else
		{
		$fldref = 'fld_'. $others[$i]->GetId();
		}
		
	  $ret .= '{if $'.$fldref.' != "" && $'.$fldref.' != "'.$this->GetAttr('unspecified',$mod->Lang('unspecified')).'" }';
	  $fldref = '{$'.$fldref.'}';
	  
	  if ($htmlish)
     	  {
  		  $ret .= '<strong>'.$others[$i]->GetName() . '</strong>: ' . $fldref. "<br />";
  		  }
  	  elseif ($oneline && !$header)
		  {
		  $ret .= $fldref. '{$TAB}';
		  }
	  elseif ($oneline && $header)
		 {
		 $ret .= $others[$i]->GetName().'{$TAB}';
		 }	 
	  else
  	  	  {
	      $ret .= $others[$i]->GetName() . ': ' .$fldref;
	      }
	  	$ret .= "{/if}\n";
		}
      }	  
	  
	 /* Stikki says: Don't see any use for this, correct me if i'm wrong.
    if ($oneline)
		{
		$ret = substr($ret,0,strlen($ret) - 6). "\n";
		}
	*/
    return $ret;
  }


//  function AdminTemplateHelp($formDescriptor,$fields='opt_email_template',
//  	$includeHTML=true, $includeText=true, $oneline = false, $headerName='')
  function AdminTemplateHelp($formDescriptor,$fieldStruct)
  {
    $mod = &$this;
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

    $others = $this->GetFields();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {                
	    $ret .= '<tr><td class="'.($odd?'odd':'even').
	    '">{$'.$others[$i]->GetVariableName().
	    '} / {$fld_'.
	    $others[$i]->GetId().'}';
		if ($others[$i]->GetAlias() != '')
			{
			$ret .= ' / {$'.$others[$i]->GetAlias().'}';	
			}
	    $ret .= '</td><td class="'.($odd?'odd':'even').
	    '">' .$others[$i]->GetName() . '</td></tr>';
	  	$odd = ! $odd;
	  }
      }
       	
    $ret .= '<tr><td colspan="2">'.$mod->Lang('help_array_fields').'</td></tr>';
    $ret .= '<tr><td colspan="2">'.$mod->Lang('help_other_fields').'</td></tr>';

    $sampleTemplateCode = '';
    foreach ($fieldStruct as $key=>$val)
		{
		$html_button = (isset($val['html_button']) && $val['html_button']);
		$text_button = (isset($val['text_button']) && $val['text_button']);
		$is_oneline = (isset($val['is_oneline']) && $val['is_oneline']);
		$is_email = (isset($val['is_email']) && $val['is_email']);
		$is_header = (isset($val['is_header']) && $val['is_header']);
		$is_footer = (isset($val['is_footer']) && $val['is_footer']);
		
		if ($html_button)
			{
			$button_text = $mod->Lang('title_create_sample_html_template');
			}
		elseif ($is_header)
			{
			$button_text = $mod->Lang('title_create_sample_header_template');
			}
		elseif ($is_footer)
			{
			$button_text = $mod->Lang('title_create_sample_footer_template');
			}						
		else
			{
			$button_text = $mod->Lang('title_create_sample_template');
			}

		if ($html_button && $text_button)
			{
			$sample = $this->createSampleTemplate(false, $is_email, $is_oneline, $is_header, $is_footer);
			$sample = preg_replace('/\'/',"\\'",$sample);
			$sample = preg_replace('/\n/',"\\n'+\n'", $sample);
			$sampleTemplateCode .= preg_replace('/\|TEMPLATE\|/',"'".$sample."'",
			$this->createSampleTemplateJavascript($key, $mod->Lang('title_create_sample_template'),'text'));
			}
		
		$sample = $this->createSampleTemplate($html_button,$is_email, $is_oneline,$is_header, $is_footer);
		$sample = preg_replace('/\'/',"\\'",$sample);
		$sample = preg_replace('/\n/',"\\n'+\n'", $sample);
		$sampleTemplateCode .= preg_replace('/\|TEMPLATE\|/',"'".$sample."'",
	    $this->createSampleTemplateJavascript($key, $button_text));
		}

    $sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $sampleTemplateCode);
    $ret .= '<tr><td colspan="2">'.$sampleTemplateCode.'</td></tr>';
    $ret .= '</table>';
	
    return $ret;
  }


	/**
	* Validate form, goes trought all form fields and their Validate methods.
	*
	* @final
	* @access public
	* @return array(boolean, string)
	*/  
	public final function Validate()
	{
		$gCms = cmsms();
		$validated = true;
		$message = array();
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
		  
		$deny_space_validation = ($this->module_ptr->GetPreference('blank_invalid','0') == '1');
		/*	  debug_display($this->Fields[$i]->GetName().' '.
		  ($this->Fields[$i]->HasValue() === false?'False':'true'));
		  if ($this->Fields[$i]->HasValue())
			 debug_display($this->Fields[$i]->GetValue());
		*/
		if (/*! $this->Fields[$i]->IsNonRequirableField() && */
			$this->Fields[$i]->IsRequired() &&
			$this->Fields[$i]->HasValue($deny_space_validation) === false)
		  {
			array_push($message,
				$this->module_ptr->Lang('please_enter_a_value',$this->Fields[$i]->GetName()));
			$validated = false;
			$this->Fields[$i]->SetOption('is_valid',false);
			$this->Fields[$i]->validationErrorText = $this->module_ptr->Lang('please_enter_a_value',$this->Fields[$i]->GetName());
			$this->Fields[$i]->validated = false;
		  }
		else if ($this->Fields[$i]->GetValue() != $this->module_ptr->Lang('unspecified'))
		  { 
			$res = $this->Fields[$i]->Validate();
			if ($res[0] != true)
			  {
			array_push($message,$res[1]);
			$validated = false;
			$this->Fields[$i]->SetOption('is_valid',false);
			  }
			else
			  {
			$this->Fields[$i]->SetOption('is_valid',true);
			  }
		  }
		$usertagops = $gCms->GetUserTagOperations();
		$udt = $this->GetAttr('validate_udt','');
		$unspec = $this->GetAttr('unspecified',$this->module_ptr->Lang('unspecified'));

		if( $validated == true && !empty($udt) && "-1" != $udt )
			{
			$parms = $params; 
			$others = $this->GetFields();
			for($i=0;$i<count($others);$i++)
				{
				$replVal = '';
				if ($others[$i]->DisplayInSubmission())
					{
					$replVal = $others[$i]->GetHumanReadableValue();
					if ($replVal == '')
						{
						$replVal = $unspec;
						}
					}
				$name = $others[$i]->GetVariableName();
				$parms[$name] = $replVal;
				$id = $others[$i]->GetId();
				$parms['fld_'.$id] = $replVal;
				$alias = $others[$i]->GetAlias();
				if (!empty($alias))
					{
					$parms[$alias] = $replVal;
					}
				}
			$res = $usertagops->CallUserTag($udt,$parms);
			if ($res[0] != true)
				{	
				array_push($message,$res[1]);
				$validated = false;	
				}
			}	
		  }
		return array($validated, $message);
	}

	/**
	* Dispose form (called after submit success and form validation completed)
	*
	* @final
	* @access public
	* @return array(boolean, string)
	*/		
	public final function Dispose($returnid,$suppress_email=false)
	{
		// first, we run all field methods that will modify other fields
		$computes = array();
		for($i=0;$i<count($this->Fields);$i++) {

			if ($this->Fields[$i]->ModifiesOtherFields()) {
			
				$this->Fields[$i]->ModifyOtherFields();
			}
			
			if ($this->Fields[$i]->ComputeOnSubmission()) {
			
				$computes[$i] = $this->Fields[$i]->ComputeOrder();
			}
		}

		asort($computes);
		foreach($computes as $cKey=>$cVal) {

			$this->Fields[$cKey]->Compute();
		}

		// Do actual disposition
		$resArray = array();
		$retCode = true;
		// for each form disposition pseudo-field, dispose the form results
		for($i=0;$i<count($this->Fields);$i++) {

			if ($this->Fields[$i]->IsDisposition() && $this->Fields[$i]->DispositionIsPermitted()) {
			
				if (! ($suppress_email && $this->Fields[$i]->IsEmailDisposition())) {
				
					$res = $this->Fields[$i]->DisposeForm($returnid);
					if ($res[0] == false) {
					
						$retCode = false;
						array_push($resArray,$res[1]);
					}
				}
			}
		}

		// Manage file uploads
		for($i=0;$i<count($this->Fields);$i++) {

			if ($this->Fields[$i]->IsFileUpload()) {
			
				$res2 = $this->Fields[$i]->HandleFileUpload();
				if ($res[0] == false) {
				
					$retCode = false;
					array_push($resArray,$res2[1]);
				}			
				
			}
		}

		// handle any last cleanup functions
		for($i=0;$i<count($this->Fields);$i++) {

			$this->Fields[$i]->PostDispositionAction();
		}

		return array($retCode,$resArray);
	}

	// deprecate
	function RenderFormHeader()
	{
		if ($this->module_ptr->GetPreference('show_version',0) == 1)
		  {
		return "\n<!-- Start FormBuilder Module (".$this->module_ptr->GetVersion().") -->\n";
		  }
	}

	// deprecate
	function RenderFormFooter()
	{
		if ($this->module_ptr->GetPreference('show_version',0) == 1)
		  {
		return "\n<!-- End FormBuilder Module -->\n";
		  }
	}

	/**
	* Renders form. Assigns all smarty variables that are available for form and processes form.
	*
	* @final
	* @access public
	* @return void
	*/
	public final function RenderForm($id, &$params, $returnid)
	{
		include(dirname(__FILE__) . '/../../../lib/replacement.php');  
		$mod = $this->module_ptr; // deprecate
		$smarty = cmsms()->GetSmarty();
		
		// Check if form id given
		if ($this->Id == -1)
		  {
			return "<!-- no form -->\n";
		  }
		  
		// Check if show full form
		if ($this->loaded != 'full')
		  {
			$this->Load($this->Id,$params,true);
		  }

		// Usual crap
		$reqSymbol = $this->GetAttr('required_field_symbol','*');

		$smarty->assign('title_page_x_of_y',$mod->Lang('title_page_x_of_y',array($this->Page,$this->formTotalPages)));
			
		$smarty->assign('css_class',$this->GetAttr('css_class',''));
		$smarty->assign('total_pages',$this->formTotalPages);
		$smarty->assign('this_page',$this->Page);
		$smarty->assign('form_name',$this->Name);
		$smarty->assign('form_id',$this->Id);
		$smarty->assign('actionid',$id);

		// Build hidden
		$hidden = $mod->CreateInputHidden($id, 'form_id', $this->Id);
		if (isset($params['lang']))
			{
			$hidden .= $mod->CreateInputHidden($id, 'lang', $params['lang']);
			}
		$hidden .= $mod->CreateInputHidden($id, 'fbrp_continue', ($this->Page + 1));
		if (isset($params['fbrp_browser_id']))
		  {
		$hidden .= $mod->CreateInputHidden($id,'fbrp_browser_id',$params['fbrp_browser_id']);
		  }
		if (isset($params['response_id']))
		  {
		  $hidden .= $mod->CreateInputHidden($id,'response_id',$params['response_id']);
		  }
		if ($this->Page > 1)
		  {
		$hidden .= $mod->CreateInputHidden($id, 'fbrp_previous', ($this->Page - 1));
		  }
		if ($this->Page == $this->formTotalPages)
		  {
		$hidden .= $mod->CreateInputHidden($id, 'fbrp_done', 1);
		  }
		  
		// Start building fields
		$fields = array();
		$prev = array();
		$formPageCount = 1;

		for ($i=0; $i < count($this->Fields); $i++) {


		$thisField = &$this->Fields[$i];

		if ($thisField->GetFieldType() == 'PageBreakField')
		  {
			$formPageCount++;
		  }
		if ($formPageCount != $this->Page)
		  {
			$testIndex = 'fbrp__'.$this->Fields[$i]->GetId();
				
				// Ryan's ugly fix for Bug 4307
				// We should figure out why this field wasn't populating its Smarty variable
				if ($thisField->GetFieldType() == 'FileUploadField')
					{
					$smarty->assign('fld_'.$thisField->GetId(),$thisField->GetHumanReadableValue());
					$hidden .= $mod->CreateInputHidden($id,
						$testIndex,
						$this->unmy_htmlentities($thisField->GetHumanReadableValue()));
					$thisAtt = $thisField->GetHumanReadableValue(false);
					$smarty->assign('test_'.$thisField->GetId(), $thisAtt);
					$smarty->assign('value_fld'.$thisField->GetId(), $thisAtt[0]);
					}
				
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
								   $this->unmy_htmlentities($val));
			  }
			  }
			else
			  {
			$hidden .= $mod->CreateInputHidden($id,
							   $testIndex,
							   $this->unmy_htmlentities($params[$testIndex]));
			  }
			  
		  if ($formPageCount < $this->Page && $this->Fields[$i]->DisplayInSubmission())
			 {
			 $oneset = new stdClass();
			 $oneset->value = $this->Fields[$i]->GetHumanReadableValue();

			 $smarty->assign($this->Fields[$i]->GetName(),$oneset);

			  if ($this->Fields[$i]->GetAlias() != '')
				{
				  $smarty->assign($this->Fields[$i]->GetAlias(),$oneset);
				  }

			  array_push($prev,$oneset);
			 }
			continue;
		  }
		$oneset = new stdClass();
		$oneset->display = $thisField->DisplayInForm()?1:0;
		$oneset->required = $thisField->IsRequired()?1:0;
		$oneset->required_symbol = $thisField->IsRequired()?$reqSymbol:'';
		$oneset->css_class = $thisField->GetOption('css_class');
		$oneset->helptext = $thisField->GetOption('helptext');
		$oneset->field_helptext_id = 'fbrp_ht_'.$thisField->GetID();
		//	$oneset->valid = $thisField->GetOption('is_valid',true)?1:0;
		$oneset->valid = $thisField->validated?1:0;
		$oneset->error = $thisField->GetOption('is_valid',true)?'':$thisField->validationErrorText;
		$oneset->hide_name = 0;
		if( ((!$thisField->HasLabel()) || $thisField->HideLabel()) && ($thisField->GetOption('fbr_edit','0') == '0' || $params['in_admin'] != 1) )
		  {
			$oneset->hide_name = 1;
		  }
		$oneset->has_label = $thisField->HasLabel();
		$oneset->needs_div = $thisField->NeedsDiv();
		$oneset->name = $thisField->GetName();
		$oneset->input = $thisField->GetFieldInput($id, $params, $returnid);
		$oneset->logic = $thisField->GetFieldLogic();
		$oneset->values = $thisField->GetAllHumanReadableValues();
		$oneset->smarty_eval = $thisField->GetSmartyEval()?1:0;

		$oneset->multiple_parts = $thisField->HasMultipleFormComponents()?1:0;
		$oneset->label_parts = $thisField->LabelSubComponents()?1:0;
		$oneset->type = $thisField->GetDisplayType();
		$oneset->friendlytype = $thisField->GetDisplayFriendlyType();
		$oneset->input_id = $thisField->GetCSSId();

		// Added by Stikki STARTS
		$name_alias = $thisField->GetName();
		$name_alias = str_replace($toreplace, $replacement, $name_alias);
		$name_alias = strtolower($name_alias);
		$name_alias = preg_replace('/[^a-z0-9]+/i','_',$name_alias);

		$smarty->assign($name_alias,$oneset);
		// Added by Stikki ENDS

		if ($thisField->GetAlias() != '')
			{
			$smarty->assign($thisField->GetAlias(),$oneset);
			$oneset->alias = $thisField->GetAlias();
			}
		else
			{
			$oneset->alias = $name_alias;
			}

		$fields[$oneset->input_id] = $oneset;
		//array_push($fields,$oneset);
		  }
		  
		$smarty->assign_by_ref('fb_hidden',$hidden);
		$smarty->assign_by_ref('fields',$fields);
		$smarty->assign_by_ref('previous',$prev);

		$jsStr = '';
		$jsTrigger = '';
		if ($this->GetAttr('input_button_safety','0') == '1')
		  {
		$jsStr = '<script type="text/javascript">
		/* <![CDATA[ */
		var submitted = 0;
		function LockButton ()
		   {
		   var ret = false;
		   if ( ! submitted )
			  {
			   var item = document.getElementById("'.$id.'fbrp_submit");
			   if (item != null)
				 {
				 setTimeout(function() {item.disabled = true}, 0);
				 }
			   submitted = 1;
			   ret = true;
			  }
			return ret;
			}
		/* ]]> */
		</script>';
		  $jsTrigger = " onclick='return LockButton()'";
		  }

		$js = $this->GetAttr('submit_javascript');

		if ($this->Page > 1)
		  {
		$smarty->assign('prev','<input class="cms_submit fbsubmit_prev" name="'.$id.'fbrp_prev" id="'.$id.'fbrp_prev" value="'.$this->GetAttr('prev_button_text').'" type="submit" '.$js.' />');						
		  }
		else
		  {
		$smarty->assign('prev','');
		  }

		if ($this->Page < $formPageCount)
		  {

		$smarty->assign('submit','<input class="cms_submit fbsubmit_next" name="'.$id.'fbrp_submit" id="'.$id.'fbrp_submit" value="'.$this->GetAttr('next_button_text').'" type="submit" '.$js.' />');  
		  }
		else
		  {
		  $captcha = $mod->getModuleInstance('Captcha');
		  if ($this->GetAttr('use_captcha','0')== '1' && $captcha != null)
			 {
			 $smarty->assign('graphic_captcha',$captcha->getCaptcha());
			 $smarty->assign('title_captcha',$this->GetAttr('title_user_captcha',$mod->Lang('title_user_captcha')));
			 $smarty->assign('input_captcha',$mod->CreateInputText($id, 'fbrp_captcha_phrase',''));
			 $smarty->assign('has_captcha','1');
			 }
		  else
			 {
			 $smarty->assign('has_captcha','0');
			 }
			 
			 
			$smarty->assign('submit','<input class="cms_submit fbsubmit" name="'.$id.'fbrp_submit" id="'.$id.'fbrp_submit" value="'.$this->GetAttr('submit_button_text').'" type="submit" '.$js.' />');  		 
		  }

		return $this->ProcessTemplateFromDatabase('fb_'.$this->Id);
	}

  function LoadForm($loadDeep=false)
  {
    return $this->Load($this->Id, array(), $loadDeep);
  }

	function unmy_htmlentities($val)
	{
		if ($val == "")
		{
			return "";
		}
		$val = html_entity_decode($val);
		$val = str_replace("&amp;","&",$val);
		$val = str_replace("&#60;&#33;--","<!--",$val);
		$val = str_replace("--&#62;","-->",$val);
		$val = str_replace("&gt;",">", $val);
		$val = str_replace("&lt;","<",$val);
		$val = str_replace("&quot;","\"",$val);
		$val = str_replace("&#036;","\$",$val);
		$val = str_replace("&#33;","!",$val);
		$val = str_replace("&#39;","'",$val);

		// Uncomment if you need to convert unicode chars
		return $val;
	}

	/**
	* Loads form, sets all given parameters, etc.
	*
	* @final
	* @access public
	* @return boolean
	*/
	public final function Load($formId, &$params, $loadDeep=false, $loadResp=false)
	{

		$db = cmsms()->GetDb();
		$mod = $this->module_ptr;

		$sql = 'SELECT * FROM '.cms_db_prefix().'module_fb_form WHERE form_id=?';
		$row = $db->GetRow($sql, array($formId));
	
		if($row) {

			$this->Id = $row['form_id'];
			
			if (!isset($params['fbrp_form_name']) || empty($params['fbrp_form_name']))	{
				$this->Name = $row['name'];
			}
			
			if (!isset($params['fbrp_form_alias']) || empty($params['fbrp_form_alias'])) {
			
				$this->Alias = $row['alias'];
			}
		} else {
		
			return false;
		}
		
		$sql = 'SELECT name, value FROM '.cms_db_prefix().'module_fb_form_attr WHERE form_id = ?';
		$rs = $db->Execute($sql, array($formId));
		
		while ($rs && $result = $rs->FetchRow()) {
		
			$this->Attrs[$result['name']] = $result['value'];
		}
			  
		$this->loaded = 'summary';

		if ($loadDeep) {

			  /*if ($loadResp)
				{
			// if it's a stored form, load the results -- but we need to manually merge them,
			// since $params[] should override the database value (say we're resubmitting a form)
				$fbf = $mod->GetFormBrowserField($formId);
				if ($fbf != false)
					{
					// if we're binding to FEU, get the FEU ID, see if there's a response for
					// that user. If so, load it. Otherwise, bring up an empty form.
					if ($fbf->GetOption('feu_bind','0')=='1')
						{
						$feu = $mod->GetModuleInstance('FrontEndUsers');
						if ($feu == false)
							{
							debug_display("FAILED to instatiate FEU!");
							return;
							}
						if (!isset($_COOKIE['cms_admin_user_id']))
							{
							// Fix for Bug 5422. Adapted from Mike Hughesdon's code.
							$response_id = $mod->GetResponseIDFromFEUID($feu->LoggedInId(), $formId);
							if ($response_id !== false)
								{
								$check = $this->module_ptr->dbHandle->GetOne('select count(*) from '.cms_db_prefix().
									'module_fb_formbrowser where fbr_id=?',array($response_id));
								if ($check == 1)
									{
									$params['response_id'] = $response_id;
									}
								}
							}
						}
					}
			if (isset($params['response_id']))
			  {
				$loadParams = array('response_id'=>$params['response_id']);
					$loadTypes = array();
				$this->LoadResponseValues($loadParams, $loadTypes);
				foreach ($loadParams as $thisParamKey=>$thisParamValue)
				  {
				if (! isset($params[$thisParamKey]))
				  {
						if ($this->GetFormState() == 'update' && $loadTypes[$thisParamKey] == 'CheckboxField')
						{
							$params[$thisParamKey] = '';
						}
						else
						{
							$params[$thisParamKey] = $thisParamValue;
						}
				  }
				  }
			  }
			}*/
		
			$sql = 'SELECT * FROM '.cms_db_prefix().'module_fb_field WHERE form_id = ? ORDER BY order_by';
			$result = $db->GetArray($sql, array($formId));

			$fieldCount = 0;
			if (count($result) > 0) {
			
				foreach($result as $thisRes) {
									
					// Merge down $params and $thisRes, any allowed input ($params overwrites $thisRes)
					if ((isset($thisRes['field_id']) && (isset($params['fbrp__'.$thisRes['field_id']]) || isset($params['fbrp___'.$thisRes['field_id']]))) ||
						(isset($thisRes['field_id']) && isset($params['value_'.$thisRes['name']])) || (isset($thisRes['field_id']) && isset($params['value_fld'.$thisRes['field_id']])) ||
						(isset($params['field_id']) && isset($thisRes['field_id']) && $params['field_id'] == $thisRes['field_id'])) {
						
						$thisRes = array_merge($thisRes, $params);
					}
					
					// Create the field object
					$this->Fields[$fieldCount] = $this->NewField($thisRes);
					$fieldCount++;
				}
			}
			
			$this->loaded = 'full';
			
		} // end of loadDeep

		
		for ($i=0; $i < count($this->Fields); $i++) {
		
			if ($this->Fields[$i]->Type == 'PageBreakField') {
			
				$this->formTotalPages++;
			}
		}

		return true;
		
	} // end of Load method


  function updateRefs($text, &$fieldMap)
   {
      foreach ($fieldMap as $k=>$v)
         {
         $text = preg_replace('/([\{\b\s])\$fld_'.$k.'([\}\b\s])/','$1\$fld_'.$v.'$2',$text);
         }
      return $text;
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

	// Change to method name to Save
	// storeDeep also stores all fields and options for a form
	function Store($storeDeep=false)
	{

		$db = cmsms()->GetDb();
		$params = $this->module_params;

		// Check if new or old form
		if ($this->Id == -1) {

			$this->Id = $db->GenID(cms_db_prefix().'module_fb_form_seq');
			$sql = "INSERT INTO ".cms_db_prefix()."module_fb_form (form_id, name, alias) VALUES (?, ?, ?)";
			$res = $db->Execute($sql, array($this->Id, $this->Name, $this->Alias));
		} else {
			
			$sql = "UPDATE ".cms_db_prefix()."module_fb_form set name=?, alias=? where form_id=?";
			$res = $db->Execute($sql, array($this->Name, $this->Alias, $this->Id));
		}
		  
		// Save out the attrs
		$sql = "DELETE FROM ".cms_db_prefix()."module_fb_form_attr WHERE form_id=?";
		$res = $db->Execute($sql, array($this->Id));

		foreach ($this->Attrs as $thisAttrKey=>$thisAttrValue) {

			$formAttrId = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
			$sql = "INSERT INTO ".cms_db_prefix()."module_fb_form_attr (form_attr_id, form_id, name, value) VALUES (?, ?, ?, ?)";
			$res = $db->Execute($sql, array($formAttrId, $this->Id, $thisAttrKey, $thisAttrValue));

			if ($thisAttrKey == 'form_template') {
			
				$this->ModuleInstance->SetTemplate('fb_'.$this->Id,$thisAttrValue);
			}
		}

		// Update field position
		$order_list = false;
		if (isset($params['fbrp_sort']))
			{
			$order_list = explode(',',$params['fbrp_sort']);
			}
			
		if(is_array($order_list) && count($order_list) > 0) {
			
			$count = 1;
			$sql = "UPDATE ".cms_db_prefix()."module_fb_field SET order_by=? WHERE field_id=?";

			foreach ($order_list as $onefldid) {

				$fieldid = substr($onefldid,5);
				$db->Execute($sql, array($count, $fieldid));
				$count++;
			}
		}

		// Reload everything
		$this->Load($this->Id,$params,true);

		return $res;
	}

	/**
	* Deletes from from database.
	*
	* @final
	* @access public
	* @return boolean
	*/	
	public final function Delete()
	{
		$db = cmsms()->GetDb();
	
		if ($this->Id == -1) {
		
			return false;
		}
		
		if ($this->loaded != 'full') {
		
			$this->Load($this->Id,array(),true);
		}
		
		foreach ($this->Fields as $field) {
		
			$field->Delete();
		}
		
		// Remove from templates
		$this->ModuleInstance->DeleteTemplate('fb_'.$this->Id);
		
		// Remove form itself
		$sql = 'DELETE FROM '.cms_db_prefix().'module_fb_form where form_id = ?';		
		$db->Execute($sql, array($this->Id));
		
		// Remove form attributes
		$sql = 'DELETE FROM '.cms_db_prefix().'module_fb_form_attr where form_id = ?';
		$db->Execute($sql, array($this->Id));
		
		return true;
	}

	/**
	* Makes class name as it says
	* NOTE: Merge this into NewField method, dosen't need to be separated, or figure something smarter. 
	*
	* @final
	* @access private
	* @return string
	*/	
	private final function MakeClassName($type, $classDirPrefix)
	{
		// perform rudimentary security, since Type could come in from a form
		$type = preg_replace("/[\W]|\.\./", "_", $type);
		if ($type == '' || strlen($type) < 1) {
		
			$type = 'Field';
		}
		
		$classFile='';
		if (strlen($classDirPrefix) > 0) {
		
			$classFile = $classDirPrefix .'/'.$type.'.class.php';
		} else {
		
			$classFile = $type.'.class.php';
		}
		
		require_once cms_join_path(dirname(__FILE__), 'fields', $classFile);
		// class names are prepended with "fb" to prevent namespace clash.
		return ( 'fb'.$type );
	}

	/**
	* Makes field type class object
	* NOTE: Public for now, action.admin_add_edit_field.php requires, change visibility to private when you can.
	*
	* @final
	* @access public
	* @return object
	*/	
    public final function &NewField(&$params)
    {

		$db = cmsms()->GetDb();
		
		//$aefield = new fbFieldBase($this,$params);
		$aefield = false;
		if (isset($params['field_id']) && $params['field_id'] != -1 )
		{
			// we're loading an extant field
		$sql = 'SELECT type FROM ' . cms_db_prefix() . 'module_fb_field WHERE field_id=?';
		$rs = $db->Execute($sql, array( $params['field_id']));
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
		if (! isset($params['fbrp_field_type']))
		  {
		// unknown field type
		$aefield = new fbFieldBase($this,$params);
		  }
		else
		  {
		// specified field type via params
				$className = $this->MakeClassName($params['fbrp_field_type'], '');
				$aefield = new $className($this, $params);
		  }
		}
		return $aefield;
    }

	// Not in use atm?????
	function MakeAlias($string, $isForm=false)
	{
		$string = trim(htmlspecialchars($string));
		if ($isForm) {
		
			return strtolower($string);
		} else {
		
			return 'fb'.strtolower($string);
		}
	}	
	
	
  function AddEditField($id, &$aefield, $dispose_only, $returnid, $message='')
  {
    $mod = $this->module_ptr;
		
    if(!empty($message)) $mod->smarty->assign('message',$mod->ShowMessage($message));
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
	$mod->smarty->assign('op',$mod->CreateInputHidden($id, 'fbrp_op',$mod->Lang('updated')));
	$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, 'fbrp_aef_upd', $mod->Lang('update')));
      }
    else
      {
	$mod->smarty->assign('op',$mod->CreateInputHidden($id, 'fbrp_op', $mod->Lang('added')));
	$mod->smarty->assign('submit',$mod->CreateInputSubmit($id, 'fbrp_aef_add', $mod->Lang('add')));
      }

    if ($aefield->HasAddOp())
      {
	$mod->smarty->assign('add',$mod->CreateInputSubmit($id,'fbrp_aef_optadd',$aefield->GetOptionAddButton()));
      }
    else
      {
	$mod->smarty->assign('add','');
      }
    if ($aefield->HasDeleteOp())
      {
	$mod->smarty->assign('del',$mod->CreateInputSubmit($id,'fbrp_aef_optdel',$aefield->GetOptionDeleteButton()));
      }
    else
      {
	$mod->smarty->assign('del','');
      }


    $mod->smarty->assign('fb_hidden', $mod->CreateInputHidden($id, 'form_id', $this->Id) . $mod->CreateInputHidden($id, 'field_id', $aefield->GetId()) . $mod->CreateInputHidden($id, 'fbrp_order_by', $aefield->GetOrder()).
			 $mod->CreateInputHidden($id,'fbrp_set_from_form','1'));

    if (/*!$aefield->IsDisposition() && */ !$aefield->IsNonRequirableField())
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

   
  function SwapFieldsByIndex($src_field_index, $dest_field_index)
  {
    $srcField = $this->GetFieldByIndex($src_field_index);
    $destField = $this->GetFieldByIndex($dest_field_index);
    $tmpOrderBy = $destField->GetOrder();
    $destField->SetOrder($srcField->GetOrder());
    $srcField->SetOrder($tmpOrderBy);
    //it seems this makes php4 go crazy fixed by reloading form before showing it again
    #        $this->Fields[$dest_field_index] = $srcField;
    #        $this->Fields[$src_field_index] = $destField;
    $srcField->Store();
    $destField->Store();
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

  // FormBrowser >= 0.3 Response load method. This populates the Field values directly
  // (as opposed to LoadResponseValues, which places the values into the $params array)
  function LoadResponse($response_id)
  {
	$mod = $this->module_ptr;
	$db = $this->module_ptr->dbHandle;
		
	$oneset = new StdClass();
	$res = $db->Execute('SELECT response, form_id FROM '.cms_db_prefix().'module_fb_formbrowser WHERE fbr_id=?', array($response_id));

	if ($res && $row=$res->FetchRow())
		{
		$oneset->xml = $row['response'];
		$oneset->form_id = $row['form_id'];
		}
	if ($oneset->form_id != $this->GetId())
		{
		return false;
		}
	$fbField = $this->GetFormBrowserField();
	if ($fbField == false)
		{
		// error handling goes here.
		echo($mod->Lang('error_has_no_fb_field'));
		}
	$mod->HandleResponseFromXML($fbField, $oneset);

	list($fnames, $aliases, $vals) = $mod->ParseResponseXML($oneset->xml, false);
	$this->ResetFields();
	foreach ($vals as $id=>$val)
		{
		//error_log("setting value of field ".$id." to be ".$val);
		$index = $this->GetFieldIndexFromId($id);
		if($index != -1 &&  is_object($this->Fields[$index]) )
			{
			$this->Fields[$index]->SetValue($val);
			}
		}
	return true;
  }

	// FBR methods, find other way.
	// Check if FormBroweiser field exists
	function GetFormBrowserField()
	{
		$fields = $this->GetFields();
		$fbField = false;
		foreach($fields as $thisField)
			{
			if ($thisField->GetFieldType() == 'DispositionFormBrowser')
				{
				$fbField = $thisField;
				}
			}
		if ($fbField == false)
			{
			// error handling goes here.
			return false;	
			}
		return $fbField;		
	}

	// FBR methods
	function ReindexResponses()
	{
	@set_time_limit(0);
	$mod = $this->module_ptr;
	$db = $this->module_ptr->dbHandle;
	$responses = array();
	$res = $db->Execute('SELECT fbr_id FROM '.cms_db_prefix().'module_fb_formbrowser WHERE form_id=?', array($this->Id));
	while ($res && $row=$res->FetchRow())
		{
		array_push($responses,$row['fbr_id']);
		}
	$fbr_field = $this->GetFormBrowserField();
	foreach($responses as $this_resp)
		{
		if ($this->LoadResponse($this_resp))
			{
			$this->StoreResponse($this_resp,'',$fbr_field);
			}
		}
	}


  // FormBrowser >= 0.3 Response load method. This populates the $params array for later processing/combination
  // (as opposed to LoadResponse, which places the values into the Field values directly)
  function LoadResponseValues(&$params, &$types)
  {
	$mod = $this->module_ptr;
	$db = $this->module_ptr->dbHandle;
	$oneset = new StdClass();
	$form_id = -1;
	$res = $db->Execute('SELECT response, form_id FROM '.cms_db_prefix().'module_fb_formbrowser WHERE fbr_id=?', array($params['response_id']));

	if ($res && $row=$res->FetchRow())
		{
		$oneset->xml = $row['response'];
		$form_id = $row['form_id'];
		}
	// loaded a response -- at this point, we check that the response
	// is for the correct form_id!
	if ($form_id != $this->GetId())
		{
		return false;
		}
	$fbField = $mod->GetFormBrowserField($form_id);
	if ($fbField == false)
		{
		// error handling goes here.
		echo($mod->Lang('error_has_no_fb_field'));
		}
	$mod->HandleResponseFromXML($fbField, $oneset);
	list($fnames, $aliases, $vals) = $mod->ParseResponseXML($oneset->xml, false);
	$types = $mod->ParseResponseXMLType($oneset->xml);
	foreach ($vals as $id=>$val)
		{
		if (isset($params['fbrp__'.$id]) &&
			! is_array($params['fbrp__'.$id]))
			{
			$params['fbrp__'.$id] = array($params['fbrp__'.$id]);
			array_push($params['fbrp__'.$id], $val);
			}
		elseif (isset($params['fbrp__'.$id]))
			{
			array_push($params['fbrp__'.$id], $val);
			}
		else
			{
			$params['fbrp__'.$id] = $val;
			}
		}
	return true;
  }

  // FormBrowser < 0.3 Response load method  
  function LoadResponseValuesOld(&$params)
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
	    if (isset($params['fbrp__'.$row['field_id']]) &&
		! is_array($params['fbrp__'.$row['field_id']]))
	      {
		$params['fbrp__'.$row['field_id']] = array($params['fbrp__'.$row['field_id']]);
		array_push($params['fbrp__'.$row['field_id']], $row['value']);
	      }
	    elseif (isset($params['fbrp__'.$row['field_id']]))
	      {
		array_push($params['fbrp__'.$row['field_id']], $row['value']);
	      }
	    else
	      {
		$params['fbrp__'.$row['field_id']] = $row['value'];
	      }
	  }
      }
    else
      {
	return false;
      }
  }   

  // FBR methods, or atleast should be.
  // Validation stuff action.validate.php
  function CheckResponse($form_id, $response_id, $code)
  {
    $db = $this->module_ptr->dbHandle;
    $sql = 'SELECT secret_code FROM ' . cms_db_prefix(). 'module_fb_formbrowser WHERE form_id=? AND fbr_id=?';
    if($result = $db->GetRow($sql, array($form_id,$response_id)))
      {
	if ($result['secret_code'] == $code)
	  {
	    return true;
	  }
      }
    return false;
  }

  // FBR methods
  // Master response inputter
  function StoreResponse($response_id=-1,$approver='',&$formBuilderDisposition)
  {
	$mod = $this->module_ptr;
    $db = $this->module_ptr->dbHandle;
    $fields = $this->GetFields();
	$newrec = false;
	
	$crypt = false;
	$hash_fields = false;
	$sort_fields = array();
	
	// Check if form has Database fields, do init
	if (is_object($formBuilderDisposition) &&
      ($formBuilderDisposition->GetFieldType()=='DispositionFormBrowser' ||
       $formBuilderDisposition->GetFieldType()=='DispositionDatabase'))
		{
		$crypt = ($formBuilderDisposition->GetOption('crypt','0') == '1');
		$hash_fields = ($formBuilderDisposition->GetOption('hash_sort','0') == '1');
		for ($i=0;$i<5;$i++)
			{
			$sort_fields[$i] = $formBuilderDisposition->getSortFieldVal($i+1);
			}
		}

	// If new field
	if ($response_id == -1)
		{
		if (is_object($formBuilderDisposition) && $formBuilderDisposition->GetOption('feu_bind','0') == '1')
			{
			$feu = $mod->GetModuleInstance('FrontEndUsers');
			if ($feu == false)
				{
				debug_display("FAILED to instatiate FEU!");
				return;
				}
			$feu_id = $feu->LoggedInId();
			}
		else
			{
			$feu_id = -1;
			}			
		$response_id = $db->GenID(cms_db_prefix(). 'module_fb_formbrowser_seq');	
	    foreach ($fields as $thisField)
			{
			// set the response_id to be the attribute of the database disposition
			if (($thisField->GetFieldType() == 'DispositionDatabase')||
				($thisField->GetFieldType() == 'DispositionFormBrowser'))
				{
				$thisField->SetValue($response_id);
				}
			}
		$newrec = true;
		}
	else
		{
		$feu_id = $mod->getFEUIDFromResponseID($response_id);
		}
		
	// Convert form to XML
	$xml = $this->ResponseToXML();
	
	// Do the actual adding
	if (! $crypt)
		{
		$output = $this->StoreResponseXML(
			$response_id,
			$newrec,
			$approver,
			isset($sort_fields[0])?$sort_fields[0]:'',
			isset($sort_fields[1])?$sort_fields[1]:'',
			isset($sort_fields[2])?$sort_fields[2]:'',
			isset($sort_fields[3])?$sort_fields[3]:'',
			isset($sort_fields[4])?$sort_fields[4]:'',
			$feu_id,
			$xml);
		}
	elseif (! $hash_fields)
		{
		list($res, $xml) = $mod->crypt($xml,$formBuilderDisposition);
		if (! $res)
			{
			return array(false, $xml);
			}
		$output = $this->StoreResponseXML(
			$response_id,
			$newrec,
			$approver,
			isset($sort_fields[0])?$sort_fields[0]:'',
			isset($sort_fields[1])?$sort_fields[1]:'',
			isset($sort_fields[2])?$sort_fields[2]:'',
			isset($sort_fields[3])?$sort_fields[3]:'',
			isset($sort_fields[4])?$sort_fields[4]:'',
			$feu_id,
			$xml);
		}
	else
		{
		list($res, $xml) = $mod->crypt($xml,$formBuilderDisposition);
		if (! $res)
			{
			return array(false, $xml);
			}
		$output = $this->StoreResponseXML(
			$response_id,
			$newrec,
			$approver,
			isset($sort_fields[0])?$mod->getHashedSortFieldVal($sort_fields[0]):'',
			isset($sort_fields[1])?$mod->getHashedSortFieldVal($sort_fields[1]):'',
			isset($sort_fields[2])?$mod->getHashedSortFieldVal($sort_fields[2]):'',
			isset($sort_fields[3])?$mod->getHashedSortFieldVal($sort_fields[3]):'',
			isset($sort_fields[4])?$mod->getHashedSortFieldVal($sort_fields[4]):'',
			$feu_id,
			$xml);
		}
	//return array(true,''); Stikki replaced: instead of true, return actual data, didn't saw any side effects.
	return $output;
  }

  // Converts form to XML
  function &ResponseToXML()
  {
  	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<response form_id=\"".$this->Id."\">\n";
	foreach($this->Fields as $thisField)
		{
			$xml .= $thisField->ExportXML(true);
		}
	$xml .= "</response>\n";
   return $xml;
  }

  // FBR methods
  // Inserts parsed XML data to database
  function StoreResponseXML($response_id=-1,$newrec=false,$approver='',$sortfield1,
   $sortfield2,$sortfield3,$sortfield4,$sortfield5, $feu_id,$xml)
  {
    $db = $this->module_ptr->dbHandle;
    $secret_code = '';

    if ($newrec)
      {
		// saving a new response
		$secret_code = substr(md5(session_id().'_'.time()),0,7);
		//$response_id = $db->GenID(cms_db_prefix(). 'module_fb_formbrowser_seq');
		$sql = 'INSERT INTO ' . cms_db_prefix().
	  'module_fb_formbrowser (fbr_id, form_id, submitted, secret_code, index_key_1, index_key_2, index_key_3, index_key_4, index_key_5, feuid, response) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
		$res = $db->Execute($sql,
			array($response_id,
				$this->GetId(),
				$this->clean_datetime($db->DBTimeStamp(time())),
				$secret_code,
				$sortfield1,$sortfield2,$sortfield3,$sortfield4,$sortfield5,
				$feu_id,
				$xml
			));
      }
    else if ($approver != '')
      {
		$sql = 'UPDATE ' . cms_db_prefix().
			'module_fb_formbrowser set user_approved=? where fbr_id=?';
		$res = $db->Execute($sql,
			    array($this->clean_datetime($db->DBTimeStamp(time())),$response_id));
		audit(-1, (isset($name)?$name:""), $this->module_ptr->Lang('user_approved_submission',array($response_id,$approver)));
      }
    if (! $newrec)
      {
	  $sql = 'UPDATE ' . cms_db_prefix().
			'module_fb_formbrowser set index_key_1=?, index_key_2=?, index_key_3=?, index_key_4=?, index_key_5=?, response=? where fbr_id=?';
	  $res = $db->Execute($sql,
			    array($sortfield1,$sortfield2,$sortfield3,$sortfield4,$sortfield5,$xml,$response_id));
      }
    return array($response_id,$secret_code);
  }   

	// Some stupid date function
	function clean_datetime($dt)
	{
		return substr($dt,1,strlen($dt)-2);
	}
  
  
	/**
	* Form XML import method
	* NOTE: Check WTF this actually does.
	* NOTE: fbrp_xml_file -- source file for the XML
	* NOTE: xml_string -- source string for the XML
	*
	* @final
	* @access public
	* @return boolean
	*/	  
	public final function ImportXML(&$params)
	{
		// xml_parser_create, xml_parse_into_struct
		$parser = xml_parser_create('');
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 0 ); // was 1
		if (isset($params['fbrp_xml_file']) && ! empty($params['fbrp_xml_file']))
		{
		xml_parse_into_struct($parser, file_get_contents($params['fbrp_xml_file']), $values);
		}
		elseif (isset($params['xml_string']) && ! empty($params['xml_string']))
		{
		xml_parse_into_struct($parser, $params['xml_string'], $values);
		}
		else
		{
		return false;
		}
		xml_parser_free($parser);
		$elements = array();
		$stack = array();
		$fieldMap = array();
		foreach ( $values as $tag )
		{
		$index = count( $elements );
		if ( $tag['type'] == "complete" || $tag['type'] == "open" )
		{
		$elements[$index] = array();
		$elements[$index]['name'] = $tag['tag'];
		$elements[$index]['attributes'] = empty($tag['attributes']) ? "" : $tag['attributes'];
		$elements[$index]['content']    = empty($tag['value']) ? "" : $tag['value'];
		if ( $tag['type'] == "open" )
		{
		# push
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
		//debug_display($elements);
		if (!isset($elements[0]) || !isset($elements[0]) || !isset($elements[0]['attributes']))
		{
		//parsing failed, or invalid file.
		return false;
		}
		$params['form_id'] = -1; // override any form_id values that may be around
		$formAttrs = &$elements[0]['attributes'];

		if (isset($params['fbrp_import_formalias']) && !empty($params['fbrp_import_formalias']))
		{
		$this->SetAlias($params['fbrp_import_formalias']);
		}
		else if ($this->inXML($formAttrs['alias']))
		{
		$this->SetAlias($formAttrs['alias']);
		}
		if (isset($params['fbrp_import_formname']) && !empty($params['fbrp_import_formname']))
		{
		$this->SetName($params['fbrp_import_formname']);
		}
		$foundfields = false;
		// populate the attributes and field name first. When we see a field, we save the form and then start adding the fields to it.

		foreach ($elements[0]['children'] as $thisChild)
		{
			if ($thisChild['name'] == 'form_name')
			{
			$curname =  $this->GetName();
			if (empty($curname))
			{
			$this->SetName($thisChild['content']);
			}
			}
			elseif ($thisChild['name'] == 'attribute')
			{
			$this->SetAttr($thisChild['attributes']['key'], $thisChild['content']);
			}
			else
			{
			// we got us a field
			if (! $foundfields)
			{
			// first field
			$foundfields = true;
			if( isset($params['fbrp_import_formname']) && 
			trim($params['fbrp_import_formname']) != '')
			{
			$this->SetName(trim($params['fbrp_import_formname']));
			}
			if( isset($params['fbrp_import_formalias']) &&
			trim($params['fbrp_import_formname']) != '')
			{
			$this->SetAlias(trim($params['fbrp_import_formalias']));
			}
			$this->Store();
			$params['form_id'] = $this->GetId();
			}
			//debug_display($thisChild);
			$fieldAttrs = &$thisChild['attributes'];
			$className = $this->MakeClassName($fieldAttrs['type'], '');
			//debug_display($className);
			$newField = new $className($this, $params);
			$oldId = $fieldAttrs['id'];

			if ($this->inXML($fieldAttrs['alias']))
			{
			$newField->SetAlias($fieldAttrs['alias']);
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
			if ($thisOpt['name'] == 'field_name')
			{
			$newField->SetName($thisOpt['content']);
			}
			if ($thisOpt['name'] == 'options')
			{
			foreach ($thisOpt['children'] as $thisOption)
			{
			$newField->OptionFromXML($thisOption);
			}
			}
			}
			$newField->Store(true);
			array_push($this->Fields,$newField);
			$fieldMap[$oldId] = $newField->GetId();
			}
		}

		// clean up references
		if (isset($params['fbrp_xml_file']) && ! empty($params['fbrp_xml_file'])) {
		
			// need to update mappings in templates.
			$tmp = $this->updateRefs($this->GetAttr('form_template',''), $fieldMap);
			$this->SetAttr('form_template',$tmp);
			$tmp = $this->updateRefs($this->GetAttr('submit_response',''), $fieldMap);
			$this->SetAttr('submit_response',$tmp);

			// need to update mappings in field templates.
			$options = array('email_template','file_template');
			foreach($this->Fields as $fid=>$thisField)
			{
				$changes = false;
				foreach ($options as $to) {
				
					$templ = $thisField->GetOption($to,'');
					if (!empty($templ))
					{
					$tmp = $this->updateRefs($templ, $fieldMap);
					$thisField->SetOption($to,$tmp);
					$changes = true;
					}
				}
				
				// need to update mappings in FormBrowser sort fields
				if ($thisField->GetFieldType() == 'DispositionFormBrowser') {
				
					for ($i=1;$i<6;$i++)
					{
						$old = $thisField->GetOption('sortfield'.$i);
						if (isset($fieldMap[$old])) {
						
							$thisField->SetOption('sortfield'.$i,$fieldMap[$old]);
							$changes = true;
						}
					}
				}
				
				if ($changes) {
				
					$thisField->Store(true);
				}
			}

			$this->Store();
		}

		return true;	
	}  
  
	/**
	* Form XML export method
	*
	* @final
	* @access public
	* @return string
	*/
	public final function ExportXML($exportValues = false)
	{
		$xmlstr = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xmlstr .= "<form id=\"".$this->getId()."\"\n";
		$xmlstr .= "\talias=\"".$this->getAlias()."\">\n";
		$xmlstr .= "\t\t<form_name><![CDATA[".$this->getName()."]]></form_name>\n";
		foreach ($this->Attrs as $thisAttrKey=>$thisAttrValue) {
		
			$xmlstr .= "\t\t<attribute key=\"$thisAttrKey\"><![CDATA[$thisAttrValue]]></attribute>\n";
		}
		
		foreach($this->Fields as $thisField) {
		
			$xmlstr .= $thisField->ExportXML($exportValues);
		}
		$xmlstr .= "</form>\n";
		
		return $xmlstr;
	}
  
  // deprecate
  function GetFormBrowsersForForm()
	{
		$db = $this->module_ptr->dbHandle;
		$fbr = $this->module_ptr->GetModuleInstance('FormBrowser');
		$browsers = array();
		if ($fbr != FALSE)
			{
			$res = $db->Execute('SELECT browser_id from '. cms_db_prefix(). 'module_fbr_browser where form_id=?',
				array($this->GetId()));
			while ($res && $row=$res->FetchRow())
				{
				array_push($browsers, $row['browser_id']);
				}
	      	}
		return $browsers;
	}

  function AddToSearchIndex($response_id)
	{	
	// find browsers keyed to this
	$browsers = $this->GetFormBrowsersForForm();
	if (count($browsers) < 1)
		{
		return;
		}
	
	$module =& $this->module_ptr->GetModuleInstance('Search');
    if ($module != FALSE)
      {
		$submitstring = '';
		foreach ($this->Fields as $thisField)
			{
			if ($thisField->DisplayInSubmission())
				{
				$submitstring .= ' '.$thisField->GetHumanReadableValue($as_string=true);
				}
			}
		foreach ($browsers as $thisBrowser)
			{
			$module->AddWords( 'FormBrowser', $response_id, 'sub_'.$thisBrowser, $submitstring, null);	
			}
      }
	}

  function setFinishedFormSmarty($htmlemail=false)
	{
		$mod = $this->module_ptr;
	   
	    $theFields = $this->GetFields();
	    $unspec = $this->GetAttr('unspecified',$mod->Lang('unspecified'));

		$formInfo = array();
		
	    for($i=0;$i<count($theFields);$i++)
	      {
			$replVal = $unspec;
			$replVals = array();
			if ($theFields[$i]->DisplayInSubmission())
		  		{
		    		$replVal = $theFields[$i]->GetHumanReadableValue();
		    		if ($htmlemail)
		        		{
						// allow <BR> as delimiter or in content
						$replVal = preg_replace('/<br(\s)*(\/)*>/i','|BR|',$replVal);
						$replVal = preg_replace('/[\n\r]+/','|BR|',$replVal);
	            		$replVal = htmlspecialchars($replVal);
						$replVal = preg_replace('/\|BR\|/','<br />',$replVal);
	            		}
		    		if ($replVal == '')
		      			{
						$replVal = $unspec;
		      			}
		  		}
		
		 	$mod->smarty->assign($theFields[$i]->GetVariableName(),$replVal);
		 	$mod->smarty->assign('fld_'.$theFields[$i]->GetId(),$replVal);
			$fldobj = $theFields[$i]->ExportObject();
		 	$mod->smarty->assign($theFields[$i]->GetVariableName().'_obj',$fldobj);
		 	$mod->smarty->assign('fld_'.$theFields[$i]->GetId().'_obj',$fldobj);
			if ($theFields[$i]->GetAlias() != '')
				{
		    	$mod->smarty->assign($theFields[$i]->GetAlias(),$replVal);
		    	$mod->smarty->assign($theFields[$i]->GetAlias().'_obj',$fldobj);
				}
	      }
		// general form details
		$mod->smarty->assign('sub_form_name',$this->GetName());
	    $mod->smarty->assign('sub_date',date('r'));
	    $mod->smarty->assign('sub_host',$_SERVER['SERVER_NAME']);
	    $mod->smarty->assign('sub_source_ip',$_SERVER['REMOTE_ADDR']);
	  	$mod->smarty->assign('sub_url',(empty($_SERVER['HTTP_REFERER'])?$mod->Lang('no_referrer_info'):$_SERVER['HTTP_REFERER']));
	    $mod->smarty->assign('fb_version',$mod->GetVersion());
	    $mod->smarty->assign('TAB',"\t");
	} 
/*	Moved to FieldBase, see also self::Dispose()
	
	function manageFileUploads()
	{
		global $gCms;
		$theFields = $this->GetFields();
		$mod = $this->module_ptr;
    
		// build rename map
		$mapId = array();
		$eval_string = false;
		for($j=0;$j<count($theFields);$j++)
			{
	    $mapId[$theFields[$j]->GetId()] = $j;
      }

	    for($i=0;$i<count($theFields);$i++)
	      {
	  		if (strtolower(get_class($theFields[$i])) == 'fbfileuploadfield' )
	    		{
	 		      // Handle file uploads
			      // if the uploads module is found, and the option is checked in
			      // the field, then the file is added to the uploads module
			      // and a link is added to the results
			      // if the option is not checked, then the file is merely uploaded to
				  // the "uploads" directory

	      		$_id = $mod->module_id.'fbrp__'.$theFields[$i]->Id;
	      		if( isset( $_FILES[$_id] ) && $_FILES[$_id]['size'] > 0 )
	        		{
	    			$thisFile =& $_FILES[$_id];
						$thisExt = substr($thisFile['name'],strrpos($thisFile['name'],'.'));
	
						if ($theFields[$i]->GetOption('file_rename','') == '')
							{
							$destination_name = $thisFile['name'];
							}
						else
							{
				    	$flds = array();
				    	$destination_name = $theFields[$i]->GetOption('file_rename');
				    	preg_match_all('/\$fld_(\d+)/', $destination_name, $flds);
							foreach ($flds[1] as $tF)
	                {
	                if (isset($mapId[$tF]))
	                    {
	                    $ref = $mapId[$tF];
	                    $destination_name = str_replace('$fld_'.$tF,
	                         $theFields[$ref]->GetHumanReadableValue(),$destination_name);
	                    }
	                }
							$destination_name = str_replace('$ext',$thisExt,$destination_name);
							}
	
	    			if( $theFields[$i]->GetOption('sendto_uploads') )
	      				{
	        			// we have a file we can send to the uploads
	        			$uploads = $mod->GetModuleInstance('Uploads');
	        			if( !$uploads )
	          				{
	      					// no uploads module
	      					audit(-1, $mod->GetName(), $mod->Lang('submit_error'),$mail->GetErrorInfo());
	            			return array($res, $mod->Lang('nouploads_error'));
	          				}

	        			$parms = array();
	        			$parms['input_author'] = $mod->Lang('anonymous');
	        			$parms['input_summary'] = $mod->Lang('title_uploadmodule_summary');
	        			$parms['category_id'] = $theFields[$i]->GetOption('uploads_category');
	        			$parms['field_name'] = $_id;
							  $parms['input_destname'] = $destination_name;
								if ($theFields[$i]->GetOption('allow_overwrite','0') == '1')
									{
									$parms['input_replace'] = 1;	
									}
	        			$res = $uploads->AttemptUpload(-1,$parms,-1);
	        			
	        			if( $res[0] == false )
	          				{
	      					// failed upload kills the send.
	      					audit(-1, $mod->GetName(), $mod->Lang('submit_error',$res[1]));
	      					return array($res[0], $mod->Lang('uploads_error',$res[1]));
	          				}

	        			$uploads_destpage = $theFields[$i]->GetOption('uploads_destpage');
						$url = $uploads->CreateLink ($parms['category_id'], 'getfile', $uploads_destpage, '',
							array ('upload_id' => $res[1]), '', true);

						$url = str_replace('admin/moduleinterface.php?','index.php?',$url);
	
						$theFields[$i]->ResetValue();
	        			$theFields[$i]->SetValue($url);
	      				}
	    			else
	      				{
	        			// Handle the upload ourselves
						$src = $thisFile['tmp_name'];						
						$dest_path = $theFields[$i]->GetOption('file_destination',$gCms->config['uploads_path']);						
						
						// validated message before, now do it for the file itself
						$valid = true;
					    $ms = $theFields[$i]->GetOption('max_size');
					    $exts = $theFields[$i]->GetOption('permitted_extensions','');
					    if ($ms != '' && $thisFile['size'] > ($ms * 1024))
							{
							$valid = false;
							}
					    else if ($exts != '')
							{
							$match = false;
							$legalExts = explode(',',$exts);
							foreach ($legalExts as $thisExt)
								{
								if (preg_match('/\.'.trim($thisExt).'$/i',$thisFile['name']))
									{
									$match = true;
									}
								else if (preg_match('/'.trim($thisExt).'/i',$thisFile['type']))
									{
									$match = true;
									}
								}
							if (! $match)
								{
								$valid = false;
								}
							}
						if (! $valid)
							{
							unlink($src);
							audit(-1, $mod->GetName(), $mod->Lang('illegal_file',array($thisFile['name'],$_SERVER['REMOTE_ADDR'])));
		      				return array(false, '');
							}
						$dest = $dest_path.DIRECTORY_SEPARATOR.$destination_name;
						if (file_exists($dest) && $theFields[$i]->GetOption('allow_overwrite','0')=='0')
							{
							unlink($src);
							return array(false,$mod->Lang('file_already_exists', array($destination_name)));
							}
						if (! move_uploaded_file($src,$dest))
							{
							audit(-1, $mod->GetName(), $mod->Lang('submit_error',''));
		      				return array(false, $mod->Lang('uploads_error',''));
							}
						else
							{
							if (strpos($dest_path,$gCms->config['root_path']) !== FALSE)
								{
								$url = str_replace($gCms->config['root_path'],'',$dest_path).'/'.$destination_name;
								}
							else
								{
								$url = $mod->Lang('uploaded_outside_webroot',$destination_name);
								}
							//$theFields[$i]->ResetValue();
							//$theFields[$i]->SetValue(array($dest,$url));
							}
	      				}
	        		}
	    		}
	      	}
		return array(true,'');
	}
*/

} // end of class

?>
