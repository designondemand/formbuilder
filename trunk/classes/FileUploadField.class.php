<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbFileUploadField extends fbFieldBase {

  function fbFileUploadField(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = $form_ptr->module_ptr;
    $this->Type = 'FileUploadField';
    //    $this->DisplayType = $mod->Lang('field_type_file_upload');
    $this->ValidationTypes = array(
				   $mod->Lang('validation_none')=>'none');
	 $this->sortable = false;
  }

  function GetFieldInput($id, &$params, $returnid)
  {
    $mod = $this->form_ptr->module_ptr;
	$js = $this->GetOption('javascript','');
    $txt = $mod->CreateFileUploadInput($id,'fbrp__'.$this->Id,$js.$this->GetCSSIdTag());
	if ($this->GetOption('show_details','0') == '1')
		{
		 $ms = $this->GetOption('max_size');
		 if ($ms != '')
			{
			$txt .= ' '.$mod->Lang('maximum_size').': '.$ms.'kb';	
			}
		 $exts = $this->GetOption('permitted_extensions');
		 if ($exts != '')
			{
	    	$txt .= ' '.$mod->Lang('permitted_extensions') . ': '.$exts;
			}
		}
    return $txt;
  }

  function Load($id, &$params, $loadDeep=false)
  {
    $mod = $this->form_ptr->module_ptr;
    parent::Load($id,$params,$loadDeep);
    if( isset( $_FILES ) && isset( $_FILES[$mod->module_id.'fbrp__'.$this->Id] ) &&
      $_FILES[$mod->module_id.'fbrp__'.$this->Id]['size'] > 0 )
      {
		// Okay, a file was uploaded
		$this->SetValue($mod->module_id.'fbrp__'.$this->Id);
      }
  }

  function GetHumanReadableValue($as_string=true)
	{
	    $mod = $this->form_ptr->module_ptr;
		if ($as_string && is_array($this->Value) && isset($this->Value[1]))
			{
			return $this->Value[1];
			}
		else
			{
			return $this->Value;
			}
	}

  function StatusInfo()
  {
    $mod = $this->form_ptr->module_ptr;
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $ret = '';
    $ret .= $mod->Lang('maximum_size').': '.$ms.'kb';
    $ret .= ', ';
    $ret .= $mod->Lang('permitted_extensions') . ': '.$exts;
    return $ret;
  }
  
  
  function PrePopulateAdminForm($formDescriptor)
  {
    $mod = $this->form_ptr->module_ptr;
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $show = $this->GetOption('show_details','0');
    $sendto_uploads = $this->GetOption('sendto_uploads','false');
    $uploads_category = $this->GetOption('uploads_category');
    $uploads_destpage = $this->GetOption('uploads_destpage');

    $main = array(
		  array($mod->Lang('title_maximum_size'),
			$mod->CreateInputText($formDescriptor, 
					      'fbrp_opt_max_size', $ms, 5, 5).
			' '.$mod->Lang('title_maximum_size_long')),
		  array($mod->Lang('title_permitted_extensions'),
			$mod->CreateInputText($formDescriptor, 
					      'fbrp_opt_permitted_extensions',
					      $exts,25,80).'<br/>'.
			$mod->Lang('title_permitted_extensions_long')),
		  array($mod->Lang('title_show_limitations'),
			$mod->CreateInputHidden($formDescriptor,'fbrp_opt_show_details','0').
			$mod->CreateInputCheckbox($formDescriptor, 
						  'fbrp_opt_show_details', '1', $show).
			' '.$mod->Lang('title_show_limitations_long'))
		 );

    $uploads = $mod->GetModuleInstance('Uploads');
    $sendto_uploads_list = array($mod->Lang('no')=>0,
				 $mod->Lang('yes')=>1);
    if( $uploads )
      {
		$categorylist = $uploads->getCategoryList();
		$adv = array(
		     array($mod->Lang('title_sendto_uploads'),
			   $mod->CreateInputDropdown($formDescriptor,
						     'fbrp_opt_sendto_uploads',$sendto_uploads_list,
						     $sendto_uploads)),
		     array($mod->Lang('title_uploads_category'),
			   $mod->CreateInputDropdown($formDescriptor,
						     'fbrp_opt_uploads_category',$categorylist,
						     $uploads_category)),
		     array($mod->Lang('title_uploads_destpage'),
			   $mod->CreatePageDropdown($formDescriptor,
						    'fbrp_opt_uploads_destpage',$uploads_destpage))
						  
		     );
      }
	else
		{
		$adv = array();
		array_push($main, array($mod->Lang('title_remove_file_from_server'),
			$mod->CreateInputHidden($formDescriptor,'fbrp_opt_remove_file','0').
			$mod->CreateInputCheckbox($formDescriptor, 
					  'fbrp_opt_remove_file', '1', 
					  $this->GetOption('remove_file','0'))));
		}

    return array('main'=>$main,'adv'=>$adv);
  }


  function PostDispositionAction()
  {
	if ($this->GetOption('remove_file','0') == '1')
		{
		if (is_array($this->Value))
			{
			$dest = $this->Value[0];	
			if (file_exists($dest))
				{
				unlink($dest);
				}
			}
		}
  }

  function Validate()
  {
  	$this->validated = true;
  	$this->validationErrorText = '';
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $mod = $this->form_ptr->module_ptr;
    //$fullAlias = $this->GetValue(); -- Stikki modifys: Now gets correct alias
    $fullAlias = $mod->module_id.'fbrp__'.$this->Id;
    if ($_FILES[$fullAlias]['size'] < 1 && ! $this->Required)
      {
	return array(true,'');
      }
    if ($_FILES[$fullAlias]['size'] < 1 && $this->Required )
      {
 	$this->validated = false;
 	$this->validationErrorText = $mod->Lang('required_field_missing');
      }
    else if ($ms != '' && $_FILES[$fullAlias]['size'] > ($ms * 1024))
      {
	$this->validationErrorText = $mod->Lang('file_too_large'). ' '.($ms * 1024).'kb';
	$this->validated = false;
      }
    else if ($exts)
      {
	$match = false;
	$legalExts = explode(',',$exts);
	foreach ($legalExts as $thisExt)
	  {
	    if (preg_match('/\.'.trim($thisExt).'$/i',$_FILES[$fullAlias]['name']))
	      {
		$match = true;
	      }
	    else if (preg_match('/'.trim($thisExt).'/i',$_FILES[$fullAlias]['type']))
	      {
		$match = true;
	      }
	  }
	if (! $match)
	  {
	    $this->validationErrorText = $mod->Lang('illegal_file_type');
	    $this->validated = false;
	  }
      }
    return array($this->validated, $this->validationErrorText);
  }
  
}

?>
