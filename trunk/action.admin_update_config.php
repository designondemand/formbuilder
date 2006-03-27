<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->mod_globals->ModuleInputPrefix = $id;
		$this->SetPreference('hide_errors',isset($params['hide_errors'])?$params['hide_errors']:0);
		$this->SetPreference('show_version',isset($params['show_version'])?$params['show_version']:0);

		$params['message'] = $this->Lang('configuration_updated');
        $this->DoAction('defaultadmin', $id, $params);

?>