<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffFileUpload extends ffInput {

	function ffFileUpload(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'FileUpload';
		$this->DisplayType = $this->mod_globals->Lang('field_type_file_upload');
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none');
		if (ffUtilityFunctions::def($params['max_size']))
		  {
		  $this->AddOption('max_size', 'max_size', $params['max_size']);
          }
		if (ffUtilityFunctions::def($params['permitted_extensions']))
		  {
		  $this->AddOption('permitted_extensions', 'permitted_extensions', $params['permitted_extensions']);
          }
		if (ffUtilityFunctions::def($params['show_details']))
		  {
		  $this->AddOption('show_details', 'show_details', $params['show_details']);
          }

	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
        $show = $this->GetOptionByKind('show_details');
        if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		echo CMSModule::CreateFileUploadInput($id, $this->Alias,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
        if (ffUtilityFunctions::def($show[0]->Value))
            {
            $ms = $this->GetOptionByKind('max_size');
            $exts = $this->GetOptionByKind('permitted_extensions');
            if (ffUtilityFunctions::def($ms[0]->Value))
                {
                echo $this->mod_globals->Lang('maximum_size').': '.$ms[0]->Value.'kb';
                }
	        if (ffUtilityFunctions::def($exts[0]->Value))
	           {
                if (ffUtilityFunctions::def($ms[0]->Value))
	   	           {
	   	           echo ', ';
	   	           }
	            echo $this->mod_globals->Lang('permitted_filetypes') . ': '.$exts[0]->Value;
		        }
            }
        if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}


	function StatusInfo()
	{
	 $ms = $this->GetOptionByKind('max_size');
     $exts = $this->GetOptionByKind('permitted_extensions');
	 $ret = '';
	 if (ffUtilityFunctions::def($ms[0]->Value))
	   {
	    $ret .= $this->mod_globals->Lang('maximum_size').': '.$ms[0]->Value.'kb';
		}
	 if (ffUtilityFunctions::def($exts[0]->Value))
	   {
	   if (ffUtilityFunctions::def($ms[0]->Value))
	   	  {
	   	  $ret .= ', ';
	   	  }
	    $ret .= $this->mod_globals->Lang('permitted_extensions') . ': '.$exts[0]->Value;
		}
	}


	function RenderAdminForm($formDescriptor)
	{
	 $ms = $this->GetOptionByKind('max_size');
     $exts = $this->GetOptionByKind('permitted_extensions');
     $show = $this->GetOptionByKind('show_details');

	 return array($this->mod_globals->Lang('title_maximum_size').':'=>
            CMSModule::CreateInputText($formDescriptor, 'max_size',
            	ffUtilityFunctions::def($ms[0]->Value)?$ms[0]->Value:($this->mod_globals->MaxUploadSize/1000),25,25),
            	$this->mod_globals->Lang('title_permitted_extensions').':'=>
            CMSModule::CreateInputText($formDescriptor, 'permitted_extensions',
            	ffUtilityFunctions::def($exts[0]->Value)?$exts[0]->Value:'',25,80).'<br />'.
            	$this->mod_globals->Lang('title_permitted_extensions_long'),
            $this->mod_globals->Lang('title_show_limitations').':'=>CMSModule::CreateInputCheckbox($formDescriptor, 'show_details', 'true', ffUtilityFunctions::def($show[0]->Value)?$show[0]->Value:'').
			' '.$this->mod_globals->Lang('title_show_limitations_long')
                );
	}


	function Validate()
	{
		$result = true;
		$message = '';
	 	$ms = $this->GetOptionByKind('max_size');
     	$exts = $this->GetOptionByKind('permitted_extensions');
		$fullAlias = $this->mod_globals->ModuleInputPrefix.$this->Alias;
		if ($_FILES[$fullAlias]['size'] < 1 && ! $this->Required)
			{
			return array(true,'');
			}
		if (ffUtilityFunctions::def($ms[0]->Value) && $_FILES[$fullAlias]['size'] > ($ms[0]->Value * 1000))
			{
			$message = $this->mod_globals->Lang('file_too_large'). ' '.($ms[0]->Value * 1000).'kb';
			$result = false;
			}
		if (ffUtilityFunctions::def($exts[0]->Value))
			{
			$match = false;
			$legalExts = explode(',',$exts[0]->Value);
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
				$message = $this->mod_globals->Lang('illegal_file_type');
				$result = false;
				}
			}
		return array($result, $message);
	}

}

?>
