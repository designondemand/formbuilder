<?php
/*
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder

   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionEmailBase extends fbFieldBase
{

  function fbDispositionEmailBase(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->IsDisposition = true;
    $this->ValidationTypes = array();

  }

  // override me!
  function StatusInfo()
  {
  }

  function TemplateStatus()
  {
    $mod = &$this->form_ptr->module_ptr;
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
    $mod = &$this->form_ptr->module_ptr;
    $form = &$this->form_ptr;

   if ($mod->GetPreference('enable_antispam',1))
     {
    $db =& $gCms->GetDb();
    $query = 'select count(src_ip) as sent from '.cms_db_prefix().
      'module_fb_ip_log where src_ip=? AND sent_time > ?';

    $dbresult = $db->GetOne($query, array($_SERVER['REMOTE_ADDR'],
               trim($db->DBTimeStamp(time() - 3600),"'")));

    if ($dbresult && $dbresult['sent'] > 9)
      {
      // too many from this IP address. Kill it.
        $msg = '<hr />'.$mod->Lang('suspected_spam'). '<hr />';
      audit(-1, $mod->GetName(),$mod->Lang('log_suspected_spam',$_SERVER['REMOTE_ADDR']));
      return array(false,$msg);
      }
    }

    $mail =& $mod->GetModuleInstance('CMSMailer');
    if ($mail == FALSE)
      {
  $msg = '';
  if (! $mod->GetPreference('hide_errors',0))
    {
      $msg = '<hr />'.$mod->Lang('missing_cms_mailer'). '<hr />';
    }
  audit(-1, $mod->GetName(),$mod->Lang('missing_cms_mailer'));
  return array(false,$msg);
      }
    $mail->reset();
    if ($this->SetFromAddress())
      {
  	  //$mail->SetFrom($this->GetOption('email_from_address'));
      $mail->AddReplyTo($this->GetOption('email_from_address'),$this->SetFromName()?$this->GetOption('email_from_name'):'');
      }
    if ($this->SetFromName())
      {
  $mail->SetFromName($this->GetOption('email_from_name'));
      }
    $mail->SetCharSet($this->GetOption('email_encoding','utf-8'));

    $message = $this->GetOption('email_template','');
    $htmlemail = ($this->GetOption('html_email','0') == '1');
    if ($htmlemail)
     {
    $mail->IsHTML(true);
    }
   if (strlen($message) < 1)
      {
    $message = $form->createSampleTemplate(false);
      if ($htmlemail)
       {
      $message2 = $form->createSampleTemplate(true);
      }
      }
    elseif ($htmlemail)
      {
    $message2 = $message;
    }
    $form->setFinishedFormSmarty();

    $theFields = &$form->GetFields();

    for($i=0;$i<count($theFields);$i++)
		{
 		if (strtolower(get_class($theFields[$i])) == 'fbfileuploadfield' )
    		{
    		if(! $theFields[$i]->GetOption('sendto_uploads') )
      			{
        		// we have a file we wish to attach
				$thisAtt = $theFields[$i]->GetHumanReadableValue(false);
			
				if (is_array($thisAtt))
					{
					if (function_exists('finfo_open'))
						{
						$finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
						$thisType = finfo_file($finfo, $thisAtt[0]);
						finfo_close($finfo);
						}
					else if (function_exists('mime_content_type'))
						{
						$thisType = mime_content_type($thisAtt[0]);
						}
					else
						{
						$thisType = 'application/octet-stream';
						}
					$thisNames = split('[/:\\]',$thisAtt[0]);
					$thisName = array_pop($thisNames);
    				if (! $mail->AddAttachment($thisAtt[0], $thisName, "base64", $thisType))
          				{
      					// failed upload kills the send.
      					audit(-1, (isset($name)?$name:""), $mod->Lang('submit_error',$mail->GetErrorInfo()));
      					return array($res, $mod->Lang('upload_attach_error',
                  				array($thisAtt[0],$thisAtt[0] ,$thisType)));
          				}
					}
      			}
     		}
    	}


    $message = $mod->ProcessTemplateFromData( $message );
    $subject = $mod->ProcessTemplateFromData( $subject );
    $mail->SetSubject($subject);
     if ($htmlemail)
     {
    $message2 = $mod->ProcessTemplateFromData($message2);
    $mail->SetAltBody(strip_tags(html_entity_decode($message)));
    $mail->SetBody($message2);
    }
   else
     {
     $mail->SetBody(html_entity_decode($message));
    }
    // send the message...
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
    else if ($mod->GetPreference('enable_antispam',1))
     {
    $db =& $gCms->GetDb();

    $rec_id = $db->GenID(cms_db_prefix().'module_fb_ip_log_seq');
    $query = 'INSERT INTO '.cms_db_prefix().
      'module_fb_ip_log (sent_id, src_ip, sent_time) VALUES (?, ?, ?)';

    $dbresult = $db->Execute($query, array($rec_id, $_SERVER['REMOTE_ADDR'],
               trim($db->DBTimeStamp(time()),"'")));
    }
    return array($res, $mail->GetErrorInfo());
  }

  function PrePopulateAdminFormBase($formDescriptor)
  {
    $mod = &$this->form_ptr->module_ptr;
    $message = $this->GetOption('email_template','');

	$parm = array();
	$parm['opt_email_template']['html_button'] = true;
	$parm['opt_email_template']['text_button'] = true;
	$parm['opt_email_template']['is_email'] = true;
    return array(
     array(
           array($mod->Lang('title_email_subject'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_email_subject',$this->GetOption('email_subject',''),50).'<br/>'.$mod->Lang('canuse_smarty')),
           array($mod->Lang('title_email_from_name'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_email_from_name',$this->GetOption('email_from_name',$mod->Lang('friendlyname')),25,128)),
           array($mod->Lang('title_email_from_address'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_email_from_address',$this->GetOption('email_from_address',''),25,128).'<br />'.
		$mod->Lang('email_from_addr_help',array($_SERVER['SERVER_NAME']))),
           ),
     array(
          array($mod->Lang('title_html_email'),
            $mod->CreateInputHidden($formDescriptor,'fbrp_opt_html_email','0').$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_html_email',
                '1',$this->GetOption('html_email','0'))),
           array($mod->Lang('title_email_template'),
           array($mod->CreateTextArea(false, $formDescriptor,
              ($this->GetOption('html_email','0')=='1'?$message:htmlspecialchars($message)),'fbrp_opt_email_template', 'module_fb_area_wide', '','',0,0),
              $this->form_ptr->AdminTemplateHelp($formDescriptor,$parm))),
           array($mod->Lang('title_email_encoding'),$mod->CreateInputText($formDescriptor, 'fbrp_opt_email_encoding',$this->GetOption('email_encoding','utf-8'),25,128))
           )
     );
  }

}

// EOF
?>
