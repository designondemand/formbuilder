<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbFileUploadField extends fbFieldBase {

  function fbFileUpload(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'FileUpload';
    //    $this->DisplayType = $mod->Lang('field_type_file_upload');
    $this->ValidationTypes = array(
				   $mod->Lang('validation_none')=>'none');
  }

  function GetFieldInput($id, &$params, $returnid)
  {
    $mod = &$this->form_ptr->module_ptr;
    $txt = $mod->CreateFileUploadInput($id,'_'.$this->Id);
    return $txt;
  }


  function Load($id, &$params, $loadDeep=false)
  {
    $mod = &$this->form_ptr->module_ptr;
    parent::Load($id,$params,$loadDeep);
    if( isset( $_FILES ) && isset( $_FILES[$mod->module_id.'_'.$this->Id] ) )
      {
	// Okay, a file was uploaded
	$this->SetValue($mod->module_id.'_'.$this->Id);
      }
  }

  function StatusInfo()
  {
    $mod = &$this->form_ptr->module_ptr;
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
    $mod = &$this->form_ptr->module_ptr;
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $show = $this->GetOption('show_details');
    $sendto_uploads = $this->GetOption('sendto_uploads','false');
    $uploads_category = $this->GetOption('uploads_category');
    $uploads_destpage = $this->GetOption('uploads_destpage');

    $main = array(
		  array($mod->Lang('title_maximum_size').':',
			$mod->CreateInputText($formDescriptor, 
					      'opt_max_size', $ms, 5, 5).
			' '.$mod->Lang('title_maximum_size_long')),
		  array($mod->Lang('title_permitted_extensions').':',
			$mod->CreateInputText($formDescriptor, 
					      'opt_permitted_extensions',
					      $exts,25,80).'<br/>'.
			$mod->Lang('title_permitted_extensions_long')),
		  array($mod->Lang('title_show_limitations').':',
			$mod->CreateInputCheckbox($formDescriptor, 
						  'opt_show_details', 'true', 
						  $show).
			' '.$mod->Lang('title_show_limitations_long'))
		 );

    $uploads = $mod->GetModuleInstance('Uploads');
    $sendto_uploads_list = array($mod->Lang('no')=>0,
				 $mod->Lang('yes')=>1);
    if( $uploads )
      {
	$categorylist = $uploads->getCategoryList();
	$adv = array(
		     array($mod->Lang('title_sendto_uploads').':',
			   $mod->CreateInputDropdown($formDescriptor,
						     'opt_sendto_uploads',$sendto_uploads_list,
						     $sendto_uploads)),
		     array($mod->Lang('title_uploads_category').':',
			   $mod->CreateInputDropdown($formDescriptor,
						     'opt_uploads_category',$categorylist,
						     $uploads_category)),
		     array($mod->Lang('title_uploads_destpage').':',
			   $mod->CreatePageDropdown($formDescriptor,
						    'opt_uploads_destpage',$uploads_destpage))
						  
		     );
      }

    return array('main'=>$main,'adv'=>$adv);
  }


  function Validate()
  {
    $result = true;
    $message = '';
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $mod = &$this->form_ptr->module_ptr;
    $fullAlias = $this->GetValue();
    if ($_FILES[$fullAlias]['size'] < 1 && ! $this->Required)
      {
	return array(true,'');
      }
    if ($_FILES[$fullAlias]['size'] < 1 && $this->Required )
      {
 	$result = false;
 	$message = $mod->Lang('required_field_missing');
      }
    else if ($ms != '' && $_FILES[$fullAlias]['size'] > ($ms * 1000))
      {
	$message = $mod->Lang('file_too_large'). ' '.($ms * 1000).'kb';
	$result = false;
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
	    $message = $mod->Lang('illegal_file_type');
	    $result = false;
	  }
      }
    return array($result, $message);
  }
  
}

?>
