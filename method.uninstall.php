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

		$db->DropSequence(cms_db_prefix().'module_fb_resp_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_val_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_attr_seq');

		$this->RemovePermission('Modify Forms');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>