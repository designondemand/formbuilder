<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$db =& $gCms->GetDb();
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_form_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form_attr');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_form_attr_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_field_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field_opt');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_field_opt_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_flock');
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_val');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_attr');
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_ip_log');
		$dict->ExecuteSQLArray($sqlarray);
		
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_formbrowser');
		$dict->ExecuteSQLArray($sqlarray);
		

		$db->DropSequence(cms_db_prefix().'module_fb_resp_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_val_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_attr_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_ip_log_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_formbrowser_seq');

		$this->RemovePermission('Modify Forms');
		
		$this->RemoveEvent( 'OnFormBuilderFormSubmit' );
		$this->RemoveEvent( 'OnFormBuilderFormDisplay' );
		$this->RemoveEvent( 'OnFormBuilderFormSubmitError' );

		$this->RemovePreference('hide_errors');
		$this->RemovePreference('show_version');
		$this->RemovePreference('relaxed_email_regex');
		$this->RemovePreference('enable_fastadd');
		$this->RemovePreference('enable_antispam');
		$this->RemovePreference('require_fieldnames');
		$this->RemovePreference('unique_fieldnames');


		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>