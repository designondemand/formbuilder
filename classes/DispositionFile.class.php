<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionFile extends  fbFieldBase 
{

  var $sampleTemplateCode;
  var $sampleHeader;

  function fbDispositionFile(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'DispositionFile';
    $this->IsDisposition = true;
    $this->NonRequirableField = true;
    $this->DisplayInForm = false;
    $this->sortable = false;
    $this->sampleTemplateCode = "<script type=\"text/javascript\">\n
		/* <![CDATA[ */
function populate_file(formname)
    {
    var fname = 'IDfbrp_opt_file_template';
    formname[fname].value=TEMPLATE;
    }
    /* ]]> */
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_template')."\" onclick=\"javascript:populate_file(this.form)\" />";
    $this->sampleHeader = "<script type=\"text/javascript\">\n
		/* <![CDATA[ */
function populate_header(formname)
    {
    var fname = 'IDfbrp_opt_file_header';
    formname[fname].value=TEMPLATE;
    }
    /* ]]> */
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_header')."\" onclick=\"javascript:populate_header(this.form)\" />";
  }

  function StatusInfo()
  {
    $mod=&$this->form_ptr->module_ptr;
    return $this->GetOption('filespec',$mod->Lang('unspecified'));
  }

  function DisposeForm($returnid)
  {
	global $gCms;
	$options = $gCms->GetConfig();
    $mod=&$this->form_ptr->module_ptr;
    $form=&$this->form_ptr;
    $count = 0;
    while (! $mod->GetFileLock() && $count<200)
      {
	$count++;
	usleep(500);
      }
    if ($count == 200)
      {
	return array(false, $mod->Lang('submission_error_file_lock'));
      }

    $filespec = $this->GetOption('fileroot',$options['uploads_root']).'/'.
      preg_replace("/[^\w\d\.]|\.\./", "_", $this->GetOption('filespec','form_submissions.txt'));

    $form->setFinishedFormSmarty();

    $line = '';
    if (! file_exists($filespec))
      {
	$header = $this->GetOption('file_header','');
	if ($header == '')
	  {
	    $header = $form->createSampleTemplate(false,false,false,true);
	  } 
	$header = $this->ProcessTemplateFromData( $header );
	$header .= "\n";
      }
    $template = $this->GetOption('file_template','');
    if ($template == '')
      {
	$template = $form->createSampleTemplate();
      }
    $line = $template;

    $newline = $mod->ProcessTemplateFromData( $line );
	$replchar = $this->GetOption('newlinechar','');
	if ($replchar != '')
		{
		$newline = rtrim($newline,"\r\n");
    	$newline = preg_replace('/[\n\r]/',$replchar,$newline);
		}
    if (substr($newline,-1,1) != "\n")
      {
	  $newline .= "\n";
      }
    $f2 = fopen($filespec,"a");
    fwrite($f2,$header.$newline);
    fclose($f2); 
    $mod->ReturnFileLock();
    return array(true,'');        
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


  function PrePopulateAdminForm($formDescriptor)
  {
	global $gCms;
    $mod = &$this->form_ptr->module_ptr;
	$config = $gCms->GetConfig();

    $main = array();
    $adv = array();
    $parmMain = array();
    $parmMain['opt_file_template']['is_oneline']=true;
    $parmMain['opt_file_header']['is_oneline']=true;
    $parmMain['opt_file_header']['is_header']=true;
    array_push($main,array($mod->Lang('title_file_root'),
			   $mod->CreateInputText($formDescriptor, 'fbrp_opt_fileroot',
						 $this->GetOption('fileroot',$config['uploads_path']),45,255).'<br />'.
				$mod->Lang('title_file_root_help')));
    array_push($main,array($mod->Lang('title_file_name'),
			   $mod->CreateInputText($formDescriptor, 'fbrp_opt_filespec',
						 $this->GetOption('filespec','form_submissions.txt'),25,128)));
    array_push($adv,array($mod->Lang('title_file_template'),
			  array($mod->CreateTextArea(false, $formDescriptor,
						     htmlspecialchars($this->GetOption('file_template','')),'fbrp_opt_file_template', 'module_fb_area_short', '','',0,0),$this->form_ptr->AdminTemplateHelp($formDescriptor,$parmMain))));
												
    array_push($adv,array($mod->Lang('title_file_header'),
			  $mod->CreateTextArea(false, $formDescriptor,
					       htmlspecialchars($this->GetOption('file_header','')),'fbrp_opt_file_header', 'module_fb_area_short', '','',0,0)));
    array_push($main,array($mod->Lang('title_newline_replacement'),
			   $mod->CreateInputText($formDescriptor, 'fbrp_opt_newlinechar',
						 $this->GetOption('newlinechar',''),5,15).'<br />'.
						$mod->Lang('title_newline_replacement_help')));

    return array('main'=>$main,'adv'=>$adv);
  }

  function PostPopulateAdminForm(&$mainArray, &$advArray)
  {
    $this->HiddenDispositionFields($mainArray, $advArray);
  }
}

?>
