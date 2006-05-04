<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

        $aeform = new fbForm($this, $params);
        $aefield = $aeform->NewField($params);
		$val = $aefield->AdminValidate();
        if ($val[0])
            {
	        $aefield->PostAdminSubmitCleanup();
            $aefield->Store(true);
            $params['message']=$params['op'];
            $this->DoAction('admin_add_edit_form', $id, $params);
            }
        else
        	{
            $aefield->LoadField($params);
            $params['message'] = $val[1];
			echo $aeform->AddEditField($id, $aefield, (isset($params['dispose_only'])?$params['dispose_only']:0), $returnid, isset($params['message'])?$params['message']:'');
        	}

?>
