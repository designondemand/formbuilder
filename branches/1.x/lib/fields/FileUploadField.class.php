<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbFileUploadField extends fbFieldBase {

  function __construct(fbForm &$FormInstance, &$params)
  {
    parent::__construct($FormInstance, $params);
		$this->Type = 'FileUploadField';
		$this->sortable = false;
		$this->IsFileUpload = true;	 
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this;	
		$js = $this->GetOption('javascript','');
		$txt = '';
		if($this->Value != '') $txt .= $this->GetHumanReadableValue()."<br />";	// Value line
		$txt .= $mod->CreateFileUploadInput($id,'fbrp__'.$this->Id,$js.$this->GetCSSIdTag()); // Input line
		if($this->Value != '') $txt .= $mod->CreateInputCheckbox($id, 'fbrp_delete__'.$this->Id, -1).'&nbsp;'.$mod->Lang('delete')."<br />"; // Delete line

		// Extras
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

	function Load(&$params, $loadDeep=false)
	{
		$mod = &$this;	
		parent::Load($params,$loadDeep);

		if(isset($_FILES) && isset($_FILES[$mod->module_id.'fbrp__'.$this->Id]) && $_FILES[$mod->module_id.'fbrp__'.$this->Id]['size'] > 0) {

			$this->SetValue($_FILES[$mod->module_id.'fbrp__'.$this->Id]['name']);
		}
	}

	function GetHumanReadableValue($as_string=true)
	{
		if ($this->GetOption('suppress_filename','0') != '0') {
		
			return '';
		}
		
		$mod = &$this;	
		if ($as_string && is_array($this->Value) && isset($this->Value[1])) {
		
			return $this->Value[1];
		} else {
		
			return $this->Value;
		}
	}

	function StatusInfo()
	{
		$mod = &$this;	
		$ms = $this->GetOption('max_size','');
		$exts = $this->GetOption('permitted_extensions','');
		$ret = '';
		if ($ms != '') {
		
			$ret .= $mod->Lang('maximum_size').': '.$ms.'kb, ';
		}
		
		if ($exts != '') {
		
			$ret .= $mod->Lang('permitted_extensions') . ': '.$exts.', ';
		}
		
		if ($this->GetOption('file_destination','') != '') {
		
			$ret .= $this->GetOption('file_destination','');
		}
		
		if ($this->GetOption('allow_overwrite','0') != '0') {
		
			$ret .= ' '.$mod->Lang('overwrite');
		} else {
		
			$ret .= ' '.$mod->Lang('nooverwrite');
		}
		
		return $ret;
	}
  
	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->getModuleInstance();	
		$form = $this->getFormInstance();
		$config = cmsms()->GetConfig();

		$ms = $this->GetOption('max_size');
		$exts = $this->GetOption('permitted_extensions');
		$show = $this->GetOption('show_details','0');

		$file_rename_help = $mod->Lang('file_rename_help'). $form->fieldValueTemplate().'<tr><td>$ext</td><td>'.$mod->Lang('original_file_extension').'</td></tr></table>';
		
		// Init main tab
		$main = array(
			array($mod->Lang('title_maximum_size'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_max_size', $ms, 5, 5).' '.$mod->Lang('title_maximum_size_long')),
			array($mod->Lang('title_permitted_extensions'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_permitted_extensions',$exts,25,80).'<br/>'.$mod->Lang('title_permitted_extensions_long')),
			array($mod->Lang('title_show_limitations'),$mod->CreateInputHidden($formDescriptor,'fbrp_opt_show_details','0').$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_show_details', '1', $show).' '.$mod->Lang('title_show_limitations_long')),
			array($mod->Lang('title_allow_overwrite'),$mod->CreateInputHidden($formDescriptor,'fbrp_opt_allow_overwrite','0').$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_allow_overwrite', '1', $this->GetOption('allow_overwrite','0')).' '.$mod->Lang('title_allow_overwrite_long')),
			array($mod->Lang('title_remove_file_from_server'), $mod->CreateInputHidden($formDescriptor,'fbrp_opt_remove_file','0'). $mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_remove_file', '1', $this->GetOption('remove_file','0')).$mod->Lang('help_ignored_if_upload')),
			array($mod->Lang('title_file_destination'), $mod->CreateInputText($formDescriptor,'fbrp_opt_file_destination', $this->GetOption('file_destination',$config['uploads_path']),60,255). $mod->Lang('help_ignored_if_upload'))			
		);

		// Init advanced tab
		$adv = array(
				
			array($mod->Lang('title_file_rename'),$mod->CreateInputText($formDescriptor,'fbrp_opt_file_rename',$this->GetOption('file_rename',''),60,255).$file_rename_help),
			array($mod->Lang('title_suppress_filename'),$mod->CreateInputHidden($formDescriptor,'fbrp_opt_suppress_filename','0').$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_suppress_filename', '1', $this->GetOption('suppress_filename','0'))),
			array($mod->Lang('title_suppress_attachment'),$mod->CreateInputHidden($formDescriptor,'fbrp_opt_suppress_attachment',0).$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_suppress_attachment', 1, $this->GetOption('suppress_attachment',1)))

		);
		
		$uploads = $mod->GetModuleInstance('Uploads');
		if(is_object($uploads)) {
		
			$sendto_uploads = $this->GetOption('sendto_uploads','false');
			$uploads_category = $this->GetOption('uploads_category');
			$uploads_destpage = $this->GetOption('uploads_destpage');		
			$sendto_uploads_list = array($mod->Lang('no')=>0,$mod->Lang('yes')=>1);
			$categorylist = $uploads->getCategoryList();
			
			// Push to advanced tab array
			array_push($adv,array($mod->Lang('title_sendto_uploads'), $mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_sendto_uploads',$sendto_uploads_list, $sendto_uploads)));
			array_push($adv,array($mod->Lang('title_uploads_category'), $mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_uploads_category',$categorylist,'', $uploads_category)));
			array_push($adv,array($mod->Lang('title_uploads_destpage'), $mod->CreatePageDropdown($formDescriptor, 'opt_uploads_destpage',$uploads_destpage)));
		}

		return array('main'=>$main,'adv'=>$adv);
	} 
  
	function PostDispositionAction()
	{
		if ($this->GetOption('remove_file','0') == '1') {
		
			if (is_array($this->Value)) {
			
				$dest = $this->Value[0];	
				if (file_exists($dest)) {
				
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
		$exts = $this->GetOption('permitted_extensions','');
		$mod = &$this;	
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
		$this->validationErrorText = $mod->Lang('file_too_large'). ' '.$ms.'kb';//($ms * 1024).'kb'; // Stikki mods
		$this->validated = false;
		  }
		else if ($exts != '')
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
  
	function HandleFileUpload()
	{

		$mod = &$this;	
		$_id = $mod->module_id.'fbrp__'.$this->Id;
		
		if(isset($_FILES[$_id]) && $_FILES[$_id]['size'] > 0) {
		
			$thisFile =& $_FILES[$_id];
			$thisExt = substr($thisFile['name'],strrpos($thisFile['name'],'.'));

			if ($this->GetOption('file_rename','') == '') {
				
				$destination_name = $thisFile['name'];
			} else {
				
				$flds = array();
				$destination_name = $this->GetOption('file_rename');
				preg_match_all('/\$fld_(\d+)/', $destination_name, $flds);
				foreach ($flds[1] as $tF) {
				
					if (isset($mapId[$tF])) {
					
						$ref = $mapId[$tF];
						$destination_name = str_replace('$fld_'.$tF,$theFields[$ref]->GetHumanReadableValue(),$destination_name);
					}
				}
				
				$destination_name = str_replace('$ext',$thisExt,$destination_name);
			}

			if( $this->GetOption('sendto_uploads') ) {
			
				// we have a file we can send to the uploads
				$uploads = $mod->GetModuleInstance('Uploads');
				if(!$uploads) {
				
					// no uploads module
					audit(-1, $mod->GetName(), $mod->Lang('submit_error'),$mail->GetErrorInfo());
					return array($res, $mod->Lang('nouploads_error'));
				}

				$parms = array();
				$parms['input_author'] = $mod->Lang('anonymous');
				$parms['input_summary'] = $mod->Lang('title_uploadmodule_summary');
				$parms['category_id'] = $this->GetOption('uploads_category');
				$parms['field_name'] = $_id;
				$parms['input_destname'] = $destination_name;
				
				if ($this->GetOption('allow_overwrite','0') == '1') {
				
					$parms['input_replace'] = 1;	
				}
				
				$res = $uploads->AttemptUpload(-1,$parms,-1);
				
				if( $res[0] == false ) {
				
					// failed upload kills the send.
					audit(-1, $mod->GetName(), $mod->Lang('submit_error',$res[1]));
					return array($res[0], $mod->Lang('uploads_error',$res[1]));
				}

				$uploads_destpage = $this->GetOption('uploads_destpage');
				$url = $uploads->CreateLink ($parms['category_id'], 'getfile', $uploads_destpage, '', array ('upload_id' => $res[1]), '', true);
				$url = str_replace('admin/moduleinterface.php?','index.php?',$url);

				$this->ResetValue();
				$this->SetValue($url);
				
			} else {
			
				// Handle the upload ourselves
				$src = $thisFile['tmp_name'];						
				$dest_path = $this->GetOption('file_destination',cmsms()->config['uploads_path']);						
				
				// validated message before, now do it for the file itself
				$valid = true;
				$ms = $this->GetOption('max_size');
				$exts = $this->GetOption('permitted_extensions','');
				if ($ms != '' && $thisFile['size'] > ($ms * 1024)) {
				
					$valid = false;
				} else if ($exts != '') {
				
					$match = false;
					$legalExts = explode(',',$exts);
					foreach ($legalExts as $thisExt) {
					
						if (preg_match('/\.'.trim($thisExt).'$/i',$thisFile['name'])) {
						
							$match = true;
						} else if (preg_match('/'.trim($thisExt).'/i',$thisFile['type'])) {
						
							$match = true;
						}
					}
					
					if (!$match) $valid = false;
					
				}
				
				if (!$valid) {
				
					unlink($src);
					audit(-1, $mod->GetName(), $mod->Lang('illegal_file',array($thisFile['name'],$_SERVER['REMOTE_ADDR'])));
					return array(false, '');
				}
				
				$dest = $dest_path.DIRECTORY_SEPARATOR.$destination_name;
				if (file_exists($dest) && $this->GetOption('allow_overwrite','0')=='0') {
				
					unlink($src);
					return array(false,$mod->Lang('file_already_exists', array($destination_name)));
				}
				
				if (! move_uploaded_file($src,$dest)) {
				
					audit(-1, $mod->GetName(), $mod->Lang('submit_error',''));
					return array(false, $mod->Lang('uploads_error',''));
				} else {
				
					if (strpos($dest_path,cmsms()->config['root_path']) !== FALSE) {
					
						$url = str_replace(cmsms()->config['root_path'],'',$dest_path).'/'.$destination_name;
					} else {
					
						$url = $mod->Lang('uploaded_outside_webroot',$destination_name);
					}
					
					$this->ResetValue();
					$this->SetValue(array($dest,$url));
				}
			}
		}
	    			
		return array(true,'');
	}    
  
  
} // end of class

?>
