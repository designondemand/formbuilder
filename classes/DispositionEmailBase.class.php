<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffDispositionEmailBase extends ffInput {

    var $sampleTemplateCode;

	function ffDispositionEmailBase(&$mod_globals, $formRef, $params=array())
	{
		$this->ffInput($mod_globals, $formRef, $params);
		$this->IsDisposition = true;
		$this->IsRequired= true;
		$this->DisplayInForm = false;
        if (ffUtilityFunctions::def($params['email_template']))
            {
            $this->AddOption('email_template','email_template',$params['email_template']);
            }
        $this->defaultTemplate = "Form Name: \$sub_form_name\n" .
        		"Submission Date: \$sub_date\nSubmission Host: \$sub_host\n" .
        		"Submission Source: \$sub_source_ip\n-------------------------------\n";
        $this->sampleTemplateCode = "<script type=\"text/javascript\">\n
function populate(formname)
    {
    var fname = 'IDemail_template';
    formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"Create Sample Template\" onClick=\"javascript:populate(this.form)\" />";
    }

	// override me!
    function StatusInfo()
	{
	}

    function TemplateStatus()
    {
    	$opt = $this->GetOptionByKind('email_template');

		$message = '';
        if (ffUtilityFunctions::def($opt[0]->Value))
            {
            $message = $opt[0]->Value;
            }
		if ($message == '' || $this->defaultTemplate == $message)
		  {
          return '<br /><strong>Email Template has not been set!</strong>';
		  }
    }

	// override me!
	function DisposeForm($formName, &$config, $results)
	{
	}


	function MakeVar($string)
	{
		$string = strtolower(preg_replace('/\s+/','_',$string));
		$string = preg_replace('/\W/','',$string);
		if (strlen($string) > 12)
			{
			$string = substr($string,0,12);
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
    	$ret = $this->defaultTemplate;
		$others = $this->formRef->ListSavedFields($this->FormId);
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]['type'] != 'PageBreak' && $others[$i]['type'] != 'FileUpload')
				{
                $ret .= $others[$i]['name'] . ': $' . $this->MakeVar($others[$i]['name']) . "\n";
                }
        	}
        return $ret;
    }

    // Send off those emails
	function SendForm($formName, &$config, $results)
	{
		global $gCms;
	    $opt = $this->GetOptionByKind('email_template');

		$message = $opt[0]->Value;

        if ($message == '')
            {
            $message = $this->createSampleTemplate();
            }
        $message = preg_replace('/\$sub_form_name/',$formName, $message);
        $message = preg_replace('/\$sub_date/',date('r'), $message);
        $message = preg_replace('/\$sub_host/',$_SERVER['SERVER_NAME'], $message);
        $message = preg_replace('/\$sub_source_ip/',$_SERVER['REMOTE_ADDR'], $message);
		
		foreach ($results as $res)
			{
			$replKey = '/\$'.$this->MakeVar($res[0]).'/i';
			$replVal = '';
			if (is_array($res[1]))
				{
				foreach($res[1] as $elem)
					{
					$replVal .= $elem . ", ";
					}
				$replVal = rtrim($replVal,", ");
				}
			else
				{
				$replVal = $res[1];
				}
			if ($replVal == '')
				{
				$replVal = 'Unspecified';
				}
			$message = preg_replace($replKey,$replVal,$message);
			}

		// send the message...
              
		$mail = new PHPMailer();
		$mail->CharSet	= 'utf-8';
		
		$mail->From     = $config->FromAddress;
		$mail->FromName = $config->FromName;
		$mail->Host     = $config->MailHost;
		$mail->Mailer   = "smtp";
				
		$mail->SetLanguage("en", $gCms->config['root_path']."modules/FeedbackForm/phpmailer/language/");

		$subj = $this->GetOptionByKind('subject');
		$mail->Subject = ffUtilityFunctions::def($subj[0]->Value)?$subj[0]->Value:$this->mod_globals->Lang('submission_subject').": ".$formName;

		$mail->Body = html_entity_decode($message);
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
					echo '<hr />Upload Attachment Error!';
					echo $thisFile['tmp_name'] . '<br>';
					echo $thisFile['name'] . '<br>';
					echo $thisFile['type'] . '<hr>';
					}
				}
			}
		$opt = $this->GetOptionByKind('address');
		foreach ($opt as $thisDest)
		  {
          $mail->AddAddress($thisDest->Value);
          }

		$res = $mail->Send();
		if (! $res)
			{
			echo $this->mod_globals->Lang('submission_error');
			if (! $config->HideErrors)
				{
				echo '<hr />'.$mail->ErrorInfo. '<hr />';
				} 
			audit(-1, (isset($name)?$name:""), 'Feedback Form Error: '.$mail->ErrorInfo);
			}
		return $res;
	}


	function RenderAdminFormBase($formDescriptor)
	{
        $opt = $this->GetOptionByKind('email_template');
        $ret = '<table border=1><tr><th colspan="2">Variables For Template</th></tr>';
        $ret .= '<tr><td>$sub_form_name</td><td>Form Name</td></tr>';
        $ret .= '<tr><td>$sub_date</td><td>Date of Submission Date</td></tr>';
        $ret .= '<tr><td>$sub_host</td><td>Your server</td></tr>';
        $ret .= '<tr><td>$sub_source_ip</td><td>IP address of person using form</td></tr>';
		$others = $this->formRef->ListSavedFields($this->FormId);
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]['type'] != 'PageBreak' && $others[$i]['type'] != 'FileUpload')
			    {
        		$ret .= '<tr><td>$'.$this->MakeVar($others[$i]['name']) . '</td><td>Field: '. $others[$i]['name'] . '</td></tr>';
                }
        	}
        $ret .= '<tr><td colspan="2">Other fields will be available as you add them to the form.</td></tr>';
        
	   $ret .= '</table>';
	   $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate());
       $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
	   $this->sampleTemplateCode = preg_replace('/TEMPLATE/',"'".$escapedSample."'",$this->sampleTemplateCode);
	   $this->sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $this->sampleTemplateCode);
       return array($this->mod_globals->Lang('title_email_template').':<br/>'.$ret=>CMSModule::CreateTextArea(false, $formDescriptor,
        	ffUtilityFunctions::def($opt[0]->Value)?$opt[0]->Value:'',
        		'email_template', '', '','',30,15),
        		$this->mod_globals->Lang('title_sample_template')=>$this->sampleTemplateCode
            );
	}


	function AdminValidate()
    {
    }

	function Validate()
	{
		return array(true, '');
	}

}

?>
