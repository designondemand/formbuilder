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

    $mail = $mod->GetModuleInstance('CMSMailer');
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
	$mail->SetFrom($this->GetOption('email_from_address'));
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
    $mod->smarty->assign('sub_form_name',$form->GetName());
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
    $others = &$form->GetFields();
    $unspec = $form->GetAttr('unspecified',$mod->Lang('unspecified'));
		
    for($i=0;$i<count($others);$i++)
      {
	$field =& $others[$i];
	$replVal = '';
	if ($field->DisplayInSubmission())
	  {
	    $replVal = $field->GetHumanReadableValue();
	    if ($replVal == '')
	      {
		$replVal = $unspec;
	      }
	  }
	if( get_class($others[$i]) == 'fbFileUploadField' )
	  {
	    //
	    // Handle file uploads
	    // if the uploads module is found, and the option is checked in
	    // the field, then the file is added to the uploads module
	    // and a link is added to the results
	    // if the option is not checked, then the file is added as
	    // an attachment
	    //
	    $_id = $field->GetValue();
	    if( isset( $_FILES[$_id] ) && $_FILES[$_id]['size'] > 0 )
	      {
		$thisFile =& $_FILES[$_id];

		if( $field->GetOption('sendto_uploads') )
		  {
		    // we have a file we can send to the uploads
		    $uploads = $mod->GetModuleInstance('Uploads');
		    if( !$uploads )
		      {
			// no uploads module
			audit(-1, $mod->GetName(), $mod->Lang('submit_error'),$mail->GetErrorInfo());
			echo "DEBUG: could not get uploads module<br/>";
		        return array($res, $mod->Lang('nouploads_error'));

		      }

		    $parms = array();
		    $parms['input_author'] = $mod->Lang('anonymous');
		    $parms['input_summary'] = $mod->Lang('title_uploadmodule_summary');
		    $parms['category_id'] = $field->GetOption('uploads_category');
		    $parms['field_name'] = $_id;
		    $res = $uploads->AttemptUpload(-1,$parms,-1);
		    if( $res[0] == false )
		      {
			// failed upload kills the send.
			audit(-1, $mod->GetName(), $mod->Lang('submit_error',$mail->GetErrorInfo()));
			return array($res[0], $mod->Lang('uploads_error',$res[1]));
		      }

		    $uploads_destpage = $field->GetOption('uploads_destpage');
		    $url = $uploads->CreateLink (-1, 'getfile', $uploads_destpage, '',
						 array ('upload_id' => $row['upload_id']), '', true);
		    $replVal = $url;
		  }
		else
		  {
		    // we have a file we can attach
		    if (! $mail->AddAttachment($thisFile['tmp_name'], $thisFile['name'], "base64", $thisFile['type']))
		      {
			// failed upload kills the send.
			audit(-1, (isset($name)?$name:""), $mod->Lang('submit_error',$mail->GetErrorInfo()));
			return array($res, $mod->Lang('upload_attach_error',
						      array($thisFile['name'],$thisFile['tmp_name'] ,$thisFile['type'])));
		      }

		    $replVal = $thisFile['name'];
		  }
	      }
	  }

	if( $replVal != '' )
	  {
	    $mod->smarty->assign($form->MakeVar($field->GetName()),$replVal);
	    $mod->smarty->assign('fld_'.$field->GetId(),$replVal);
	  }
      }

    $message = $mod->ProcessTemplateFromData( $message );
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

    return array(
		 array(
		       array($mod->Lang('title_email_subject'),$mod->CreateInputText($formDescriptor, 'opt_email_subject',$this->GetOption('email_subject',''),25,128)),
		       array($mod->Lang('title_email_from_name'),$mod->CreateInputText($formDescriptor, 'opt_email_from_name',$this->GetOption('email_from_name',$mod->Lang('friendlyname')),25,128)),
		       array($mod->Lang('title_email_from_address'),$mod->CreateInputText($formDescriptor, 'opt_email_from_address',$this->GetOption('email_from_address',''),25,128)),
		       ),
		 array(
		 		 array($mod->Lang('title_html_email'),
		 		 	$mod->CreateInputHidden($formDescriptor,'opt_html_email','0').$mod->CreateInputCheckbox($formDescriptor, 'opt_html_email',
            		'1',$this->GetOption('html_email','0'))),
		       array($mod->Lang('title_email_template'),
			     array($mod->CreateTextArea(false, $formDescriptor,
							($this->GetOption('html_email','0')=='1'?$message:htmlspecialchars($message)),'opt_email_template', 'module_fb_area_wide', '','',0,0),
							$this->form_ptr->AdminTemplateHelp($formDescriptor))),
		       array($mod->Lang('title_email_encoding'),$mod->CreateInputText($formDescriptor, 'opt_email_encoding',$this->GetOption('email_encoding','utf-8'),25,128))
		       )
		 );
  }

}

// EOF
?>