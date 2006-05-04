<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionEmailBase extends fbFieldBase {

    var $sampleTemplateCode;
    var $templateVariables;
	
	function fbDispositionEmailBase(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->IsDisposition = true;
		$this->ValidationTypes = array(
            );

        $this->sampleTemplateCode = "<script type=\"text/javascript\">\n
function populate(formname)
    {
    var fname = 'IDopt_email_template';
    formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_template')."\" onClick=\"javascript:populate(this.form)\" />";

		$this->templateVariables = Array(
        '{$sub_form_name}'=>$mod->Lang('title_form_name'),
        '{$sub_date}'=>$mod->Lang('help_submission_date'),
        '{$sub_host}'=>$mod->Lang('help_server_name'),
        '{$sub_source_ip}'=>$mod->Lang('help_sub_source_ip'),
        '{$sub_url}'=>$mod->Lang('help_sub_url')
		);
    }

	// override me!
    function StatusInfo()
	{
	}

    function TemplateStatus()
    {
    	$mod = $this->form_ptr->module_ptr;
    	if ($this->GetOption('email_template','') == '')
    		{
    		return $mod->Lang('email_template_not_set');
    		}
    }

	// override me!
	function DisposeForm()
	{
		return array(true,'');
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

    function createSampleTemplate()
    {
    	$mod = $this->form_ptr->module_ptr;
    	$ret = $mod->Lang('email_default_template');
    	foreach($this->templateVariables as $thisKey=>$thisVal)
    		{
    		$ret .= $thisVal.': '.$thisKey."\n";
    		}
    	$ret .= "\n-------------------------------------------------\n";
		$others = $this->form_ptr->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->DisplayInSubmission())
				{
                $ret .= $others[$i]->GetName() . ': {$' . $this->MakeVar($others[$i]->GetName()) . "}\n";
                }
        	}
        return $ret;
    }

	// override me as necessary
	function SetFromAddress()
	{
		return true;
	}

	// override me as necessary
	function SetFromName()
	{
		return true;
	}

    // Send off those emails
	function SendForm($destination_array, $subject)
	{
		global $gCms;
		$mod = $this->form_ptr->module_ptr;
		$message = $this->GetOption('email_template','');
		if ($message == '')
			{
			$message = $this->createSampleTemplate();
			}

        $mod->smarty->assign('sub_form_name',$this->form_ptr->GetName());
        $mod->smarty->assign('sub_date',date('r'));
        $mod->smarty->assign('sub_host',$_SERVER['SERVER_NAME']);
        $mod->smarty->assign('sub_source_ip',$_SERVER['REMOTE_ADDR']);
        if (empty($_SERVER['HTTP_REFERER']))
        	{
        	$mod->smarty->assign('sub_url',$mod->Lang('no_referrer_info'));
        	}
        else
        	{
        	$mod->smarty->assign('sub_url',$_SERVER['HTTP_REFERER']);
			}
		$others = $this->form_ptr->GetFields();
		$unspec = $this->form_ptr->GetAttr('unspecified',$mod->Lang('unspecified'));
		
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
        	$mod->smarty->assign($this->MakeVar($others[$i]->GetName()),$replVal);
        	}
		$message = $mod->ProcessTemplateFromData( $message );
		// send the message...
		$mail = $mod->GetModuleInstance('CMSMailer');
		if ($mail == FALSE)
			{
			$msg = '';
			if (! $mod->GetPreference('hide_errors',0))
				{
				$msg = '<hr />'.$mod->Lang('missing_cms_mailer'). '<hr />';
				} 
			audit(-1, (isset($name)?$name:""),$mod->Lang('missing_cms_mailer'));
			return array(false,$msg);
			}
		$mail->reset();
		if ($this->SetFromAddress())
			{
			$mail->SetFrom($this->GetOption('email_from_address'));
			}
		if ($this->SetFromName())
			{
			$mail->SetFromName($this->GetOption('email_from_name'));
			}
		$mail->SetSubject($subject);
		$mail->SetBody(html_entity_decode($message));
		$mail->SetCharSet($this->GetOption('email_encoding','utf-8'));

		if (count($_FILES) > 0)
			{
			foreach ($_FILES as $thisFile)
				{
				if ($thisFile['size'] < 1)
					{
					continue;
					}
				if (! $mail->AddAttachment($thisFile['tmp_name'], $thisFile['name'], "base64", $thisFile['type']))
					{
					// failed upload kills the send.
					audit(-1, (isset($name)?$name:""), $mod->Lang('submit_error',$mail->GetErrorInfo()));
					return array($res, $mod->Lang('upload_attach_error',
						array($thisFile['name'],$thisFile['tmp_name'] ,$thisFile['type'])));
					}
				}
			}
		if (! is_array($destination_array))
			{
			$destination_array = array($destination_array);
			}
		foreach ($destination_array as $thisDest)
		  {
          $mail->AddAddress($thisDest);
          }

		$res = $mail->Send();
		if ($res === false)
			{
			audit(-1, (isset($name)?$name:""), $mod->Lang('submit_error',$mail->GetErrorInfo()));
			}
		return array($res, $mail->GetErrorInfo());
	}

	function PrePopulateAdminFormBase($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$message = $this->GetOption('email_template','');
        $ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_template').'</th></tr>';
        $ret .= '<tr><th>'.$mod->Lang('help_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
    	foreach($this->templateVariables as $thisKey=>$thisVal)
    		{
    		$ret .= '<tr><td>'.$thisKey.'</td><td>'.$thisVal.'</td></tr>';
    		}

/*
        $ret .= '<tr><td>{$sub_form_name}</td><td>'.$mod->Lang('title_form_name').'</td></tr>';
        $ret .= '<tr><td>{$sub_date}</td><td>'.$mod->Lang('help_submission_date').'</td></tr>';
        $ret .= '<tr><td>{$sub_host}</td><td>'.$mod->Lang('help_server_name').'</td></tr>';
        $ret .= '<tr><td>{$sub_source_ip}</td><td>'.$mod->Lang('help_sub_source_ip').'</td></tr>';
        $ret .= '<tr><td>{$source_url}</td><td>'.$mod->Lang('help_sub_url').'</td></tr>';
*/
		$others = $this->form_ptr->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->DisplayInSubmission())
				{                
                $ret .= '<tr><td>{$'.$this->MakeVar($others[$i]->GetName()) .'}</td><td>' .$others[$i]->GetName() . '</td></tr>';
                }
        	}
       	
        $ret .= '<tr><td colspan="2">'.$mod->Lang('help_other_fields').'</td></tr>';
        
	   $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate());
       $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
	   $this->sampleTemplateCode = preg_replace('/TEMPLATE/',"'".$escapedSample."'",$this->sampleTemplateCode);
	   $this->sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $this->sampleTemplateCode);
	   $ret .= '<tr><td colspan="2">'.$this->sampleTemplateCode.'</td></tr>';
	   $ret .= '</table>';

       return array(
       		array(
               		array($mod->Lang('title_email_subject'),$mod->CreateInputText($formDescriptor, 'opt_email_subject',$this->GetOption('email_subject',''),25,128)),
               		array($mod->Lang('title_email_from_name'),$mod->CreateInputText($formDescriptor, 'opt_email_from_name',$this->GetOption('email_from_name',$mod->Lang('friendlyname')),25,128)),
               		array($mod->Lang('title_email_from_address'),$mod->CreateInputText($formDescriptor, 'opt_email_from_address',$this->GetOption('email_from_address',''),25,128)),
					),
			array(
					array($mod->Lang('title_email_template'),
       					array($mod->CreateTextArea(false, $formDescriptor,
        					htmlspecialchars($message),'opt_email_template', 'module_fb_area_wide', '','',0,0),$ret)),
        			array($mod->Lang('title_email_encoding'),$mod->CreateInputText($formDescriptor, 'opt_email_encoding',$this->GetOption('email_encoding','utf-8'),25,128))
            		)
            );
	}

}

?>
