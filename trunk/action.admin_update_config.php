<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->SetPreference('hide_errors',isset($params['hide_errors'])?$params['hide_errors']:0);
		$this->SetPreference('show_version',isset($params['show_version'])?$params['show_version']:0);
		$this->SetPreference('relaxed_email_regex',isset($params['relaxed_email_regex'])?$params['relaxed_email_regex']:0);
		$this->SetPreference('enable_fastadd',isset($params['enable_fastadd'])?$params['enable_fastadd']:0);
		$this->SetPreference('enable_antispam',isset($params['enable_antispam'])?$params['enable_antispam']:0);

		$params['message'] = $this->Lang('configuration_updated');
        $this->DoAction('defaultadmin', $id, $params);

?>