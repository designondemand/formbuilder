<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionFile extends  fbFieldBase {

    var $sampleTemplateCode;
    var $sampleHeader;

	function fbDispositionFile(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'DispositionFile';
		$this->IsDisposition = true;
		$this->NonRequirableField = true;
		$this->DisplayInForm = false;
        $this->sampleTemplateCode = "<script type=\"text/javascript\">\n
function populate_file(formname)
    {
    var fname = 'IDopt_file_template';
    formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_template')."\" onClick=\"javascript:populate_file(this.form)\" />";
        $this->sampleHeader = "<script type=\"text/javascript\">\n
function populate_header(formname)
    {
    var fname = 'IDopt_file_header';
    formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_header')."\" onClick=\"javascript:populate_header(this.form)\" />";
	}

    function StatusInfo()
	{
		$mod=$this->form_ptr->module_ptr;
		return $this->GetOption('filespec',$mod->Lang('unspecified'));
	}

function DisposeForm()
	{
		$mod=$this->form_ptr->module_ptr;
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

        $filespec = $mod->config['root_path'].'/modules/FormBuilder/output/'.
        	preg_replace("/[^\w\d\.]|\.\./", "_", $this->GetOption('filespec','form_submissions.txt'));

		$line = '';
        if (! file_exists($filespec))
        	{
        	$header = $this->GetOption('file_header','');
        	if ($header == '')
        		{
        		$header = $this->createSampleHeader();
        		} 
        	$header .= "\n";
        	}
		$template = $this->GetOption('file_template','');
		if ($template == '')
			{
			$template = $this->createSampleTemplate();
			}
		$line = $header.$template;
        $mod->smarty->assign('sub_form_name',$this->form_ptr->GetName());
        $mod->smarty->assign('sub_date',date('r'));
        $mod->smarty->assign('sub_host',$_SERVER['SERVER_NAME']);
        $mod->smarty->assign('sub_source_ip',$_SERVER['REMOTE_ADDR']);
        $mod->smarty->assign('sub_url',$_SERVER['HTTP_REFERER']);
		$mod->smarty->assign('TAB',"\t");
		$others = $this->form_ptr->GetFields();
		$unspec = $this->form_ptr->GetAttr('unspecified',$mod->Lang('unspecified'));
		
		for($i=0;$i<count($others);$i++)
			{
			$replVal = '';
			if ($others[$i]->DisplayInForm())
				{
				$replVal = $others[$i]->GetHumanReadableValue();
				if ($replVal == '')
					{
					$replVal = $unspec;
					}
                }
        	$mod->smarty->assign($this->MakeVar($others[$i]->Getname()),$replVal);
        	}

		$newline = $mod->ProcessTemplateFromData( $line );
		if (substr($newline,-1,1) != "\n")
			{
			$newline .= "\n";
			}
		$f2 = fopen($filespec,"a");
		fwrite($f2,$newline);
		fclose($f2); 
        $mod->ReturnFileLock();
        return array(true,'');        
	}

	function RenderAdminForm($formDescriptor)
	{
        $fmt = $this->GetOptionByName('format');
        $spec = $this->GetOptionByName('filespec');
        return array($this->mod_globals->Lang('title_file_format').':'=>CMSModule::CreateInputDropdown($formDescriptor, 'format',
            array($this->mod_globals->Lang('title_file_format_tab')=>'tab',
                $this->mod_globals->Lang('title_file_format_tab_header')=>'tabhead',
                $this->mod_globals->Lang('title_file_format_page')=>'page'), -1,
                ffUtilityFunctions::def($fmt[0]->Value)?$this->NerfHTML($fmt[0]->Value):''),
            $this->mod_globals->Lang('title_file_name').':'=>CMSModule::CreateInputText($formDescriptor, 'filespec',
				ffUtilityFunctions::def($spec[0]->Value)?$this->NerfHTML($spec[0]->Value):'',25),
			$this->mod_globals->Lang('title_erase_filelock').':'=>CMSModule::CreateInputCheckbox($formDescriptor, 'clear_flock', 'clear', '').
			' '.$this->mod_globals->Lang('title_erase_usage'),
			$this->mod_globals->Lang('note').':'=>$this->mod_globals->Lang('title_file_note')
            );
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


    function createSampleHeader()
    {
    	$mod = $this->form_ptr->module_ptr;
		$others = $this->form_ptr->GetFields();
		$fields = array();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->DisplayInForm())
				{
                array_push($fields,$others[$i]->GetName());
                }
        	}
        return implode('{$TAB}',$fields);
    }


    function createSampleTemplate()
    {
    	$mod = $this->form_ptr->module_ptr;
		$others = $this->form_ptr->GetFields();
		$fields = array();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->DisplayInForm())
				{
                array_push($fields,'{$' . $this->MakeVar($others[$i]->GetName()) . '}');
                }
        	}
        return implode('{$TAB}',$fields);
    }


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
        $ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_template').'</th></tr>';
        $ret .= '<tr><th>'.$mod->Lang('help_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
        $ret .= '<tr><td>{$sub_form_name}</td><td>'.$mod->Lang('title_form_name').'</td></tr>';
        $ret .= '<tr><td>{$sub_date}</td><td>'.$mod->Lang('help_submission_date').'</td></tr>';
        $ret .= '<tr><td>{$sub_host}</td><td>'.$mod->Lang('help_server_name').'</td></tr>';
        $ret .= '<tr><td>{$sub_source_ip}</td><td>'.$mod->Lang('help_sub_source_ip').'</td></tr>';
        $ret .= '<tr><td>{$source_url}</td><td>'.$mod->Lang('help_sub_url').'</td></tr>';
		$others = $this->form_ptr->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->DisplayInForm())
				{                
                $ret .= '<tr><td>${'.$this->MakeVar($others[$i]->GetName()) .'}</td><td>' .$others[$i]->GetName() . '</td></tr>';
                }
        	}

       $ret .= '<tr><td>{$TAB}</td><td>'.$mod->Lang('help_tab_symbol').'</td></tr>';       	
       $ret .= '<tr><td colspan="2">'.$mod->Lang('help_other_fields').'</td></tr>';
        
	   $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate());
       $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
	   $this->sampleTemplateCode = preg_replace('/TEMPLATE/',"'".$escapedSample."'",$this->sampleTemplateCode);
	   $this->sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $this->sampleTemplateCode);
	   
	   $escapedHeader = preg_replace('/\'/',"\\'",$this->createSampleHeader());
       $escapedHeader = preg_replace('/\n/',"\\n'+\n'", $escapedHeader);
	   $this->sampleHeader = preg_replace('/TEMPLATE/',"'".$escapedHeader."'",$this->sampleHeader);
	   $this->sampleHeader = preg_replace('/ID/',$formDescriptor, $this->sampleHeader);
	   
	   $ret .= '<tr><td colspan="2">'.$this->sampleHeader.'</td></tr>';
	   $ret .= '<tr><td colspan="2">'.$this->sampleTemplateCode.'</td></tr>';

	   $ret .= '</table>';


		$main = array();
		$adv = array();
        array_push($main,array($mod->Lang('title_file_name'),
        	$mod->CreateInputText($formDescriptor, 'opt_filespec',
				$this->GetOption('filespec','form_submissions.txt'),25,128)));
		array_push($adv,array($mod->Lang('title_file_template'),
       					array($mod->CreateTextArea(false, $formDescriptor,
        					htmlspecialchars($this->GetOption('file_template','')),'opt_file_template', 'module_fb_area_short', '','',0,0),$ret)));
		array_push($adv,array($mod->Lang('title_file_header'),
       					$mod->CreateTextArea(false, $formDescriptor,
        					htmlspecialchars($this->GetOption('file_header','')),'opt_file_header', 'module_fb_area_short', '','',0,0)));

		return array('main'=>$main,'adv'=>$adv);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$this->HiddenDispositionFields($mainArray, $advArray);
	}


}

?>
