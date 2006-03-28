<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initialize();
		$this->mod_globals->ModuleInputPrefix = $id;
		
		$aeform = new fbForm($this, $params);
		$aefield = $aeform->NewField($params);
		if (isset($params['aef_upd']) ||
			(isset($params['aef_add']) && $aefield->GetFieldType() != ''))
			{
			// save the field.
			$this->DoAction('admin_store_field', $id, $params);
			return;
			}
		elseif (isset($params['aef_add']))
			{
			// should have got a field type definition, so give rest of the field options
			// reserve this space for special ops :)
			}
		elseif (isset($params['aef_optadd']))
			{
			// call the field's option add method, with all available parameters
			$aefield->DoOptionAdd($params);
			}
		elseif (isset($params['aef_optdel']))
			{
			// call the field's option delete method, with all available parameters
			$aefield->DoOptionDelete($params);
			}
		else
			{
			// new field, or implicit aef_add.
			// again, reserving the space for future endeavors
			}
		echo $aeform->AddEditField($id, $aefield, (isset($params['dispose_only'])?$params['dispose_only']:0), $returnid, isset($params['message'])?$params['message']:'');
		
?>
