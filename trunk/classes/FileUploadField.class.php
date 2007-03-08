<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbFileUploadField extends fbFieldBase {

  function ffFileUpload(&$form_ptr, &$params)
  {
    $this->ffFieldBase($form_ptr, $params);
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
    $ms = $this->GetOption('max_size');
    $exts = $this->GetOption('permitted_extensions');
    $show = $this->GetOption('show_details');
    $sendto_uploads = $this->GetOption('sendto_uploads');
    $uploads_category = $this->GetOption('uploads_category');
    $uploads_destpage = $this->GetOption('uploads_destpage');

    $mod = &$this->form_ptr->module_ptr;
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
    if( $uploads )
      {
	$categorylist = $uploads->getCategoryList();
	$adv = array(
		     array($mod->Lang('title_sendto_uploads').':',
			   $mod->CreateInputCheckbox($formDescriptor,
						     'opt_sendto_uploads','true',
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
    $ms = $this->GetOptionByKind('max_size');
    $exts = $this->GetOptionByKind('permitted_extensions');
    $mod = &$this->form_ptr->module_ptr;
    $fullAlias = '_'.$this->Id;
    if ($_FILES[$fullAlias]['size'] < 1 && ! $this->Required)
      {
	return array(true,'');
      }
    if ($ms != '' && $_FILES[$fullAlias]['size'] > ($ms * 1000))
      {
	$message = $mod->Lang('file_too_large'). ' '.($ms * 1000).'kb';
	$result = false;
      }
    if ($exts)
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
