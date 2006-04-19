<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionEmailBase extends fbFieldBase {

    var $sampleTemplateCode;
	
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
    	if ($this->GetOption('email_template',$this->Lang('email_default_template')) ==
    		$this->Lang('email_default_template'))
    		{
    		return $this->Lang('email_template_not_set');
    		}
    }

	// override me!
	function DisposeForm($formName, &$config, $results)
	{
	}


	function MakeVar($string)
	{
		$maxvarlen = 24;
		$string = strtolower(preg_replace('/\s+/','_',$string));
		//$string = preg_replace('/\W/','',$string);
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
    	$ret = $this->Lang('email_default_template');
		$others = $this->form_ptr->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->GetFieldType() != 'PageBreak' && $others[$i]->GetFieldType() != 'FileUpload')
				{
                $ret .= $others[$i]->GetName() . ': {$' . $this->MakeVar($others[$i]->GetName()) . "}\n";
                }
        	}
        return $ret;
    }

    // Send off those emails
	function SendForm($formName, &$config, $results)
	{
		global $gCms;

		$message = $this->GetOption('email_template',$this->Lang('email_default_template'));

        if ($message == '')
            {
            $message = $this->createSampleTemplate();
            }
        $this->smarty->assign('sub_form_name',$formName);
        $this->smarty->assign('sub_date',date('r'));
        $this->smarty->assign('sub_host',$_SERVER['SERVER_NAME']);
        $this->smarty->assign('sub_source_ip',$_SERVER['REMOTE_ADDR']);
/*        $message = preg_replace('/\$sub_form_name/',$formName, $message);
        $message = preg_replace('/\$sub_date/',date('r'), $message);
        $message = preg_replace('/\$sub_host/',$_SERVER['SERVER_NAME'], $message);
        $message = preg_replace('/\$sub_source_ip/',$_SERVER['REMOTE_ADDR'], $message);
*/		
		foreach ($results as $res)
			{
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
				$replVal = $this->Lang('email_value_unspecified');
				}
			$this->smarty->assign($this->MakeVar($res[0]),$replVal);
			//$message = preg_replace($replKey,$replVal,$message);
			}

		// send the message...
		$mail = $this->mod_globals->selfptr->GetModuleInstance('CMSMailer');
		if ($mail == FALSE)
			{
			if (! $config->HideErrors)
				{
				echo '<hr />'.$this->mod_globals->Lang('missing_cms_mailer'). '<hr />';
				} 
			audit(-1, (isset($name)?$name:""), 'Feedback Form Error: '.$this->mod_globals->Lang('missing_cms_mailer'));

			}
		$mail->reset();
		$mail->SetFrom($config->FromAddress);
		$mail->SetFromName($config->FromName);
		
		$subj = $this->GetOptionByKind('subject');
		$mail->SetSubject(ffUtilityFunctions::def($subj[0]->Value)?$subj[0]->Value:$this->mod_globals->Lang('submission_subject').": ".$formName);
		$mail->SetBody(html_entity_decode($message));
		$mail->SetCharSet('utf-8');

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

	function PrePopulateAdminFormBase($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$message = $this->GetOption('email_template',$mod->Lang('email_default_template'));
        $ret = '<table border=1><tr><th colspan="2">Variables For Template</th></tr>';
        $ret .= '<tr><td>$sub_form_name</td><td>Form Name</td></tr>';
        $ret .= '<tr><td>$sub_date</td><td>Date of Submission Date</td></tr>';
        $ret .= '<tr><td>$sub_host</td><td>Your server</td></tr>';
        $ret .= '<tr><td>$sub_source_ip</td><td>IP address of person using form</td></tr>';
		$others = $this->form_ptr->GetFields();
		for($i=0;$i<count($others);$i++)
			{
			if ($others[$i]->GetFieldType() != 'PageBreak' && $others[$i]->GetFieldType() != 'FileUpload')
				{
				//$ret .= '<tr><td>$'.$this->MakeVar($others[$i]->GetName()) . '</td><td>';
				//$ret .= $others[$i]->GetName() .'</td></tr>';
                
                $ret .= '<tr><td>$'.$this->MakeVar($others[$i]->GetName()) .'</td><td>' .$others[$i]->GetName() . '</td></tr>';
                }
        	}
       	
        $ret .= '<tr><td colspan="2">Other fields will be available as you add them to the form.</td></tr>';
        
	   $ret .= '</table>';
/*	   $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate());
       $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
	   $this->sampleTemplateCode = preg_replace('/TEMPLATE/',"'".$escapedSample."'",$this->sampleTemplateCode);
	   $this->sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $this->sampleTemplateCode);
*/
       return array(array(),array(array($mod->Lang('title_email_template'),
       	array($mod->CreateTextArea(false, $formDescriptor,
        	htmlentities($message),'opt_email_template', '', '','',30,15),
        		//$this->mod_globals->Lang('title_sample_template')=>$this->sampleTemplateCode
        		$ret))
            ));
           return array(array(array('a',array('b','c'))),array());
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
