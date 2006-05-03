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
		
        $aeform = new fbForm($this, $params, true);
        $aeform->Store();
        if ($params['submit'] == $this->Lang('save'))
            {
            $params['message'] = $this->Lang('form',$params['form_op']);
            $this->DoAction('defaultadmin', $id, $params);
            }
        else
        	{
			echo $aeform->AddEditForm($id, $returnid,$this->Lang('form',$params['form_op']));
			}
?>
