<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;

		// and a list of all the extant forms.
        $forms = $this->GetForms();
		$num_forms = count($forms);
        
        $this->smarty->assign('tabheaders', $this->StartTabHeaders() .
			$this->SetTabHeader('forms',$this->Lang('forms')) .
			$this->SetTabHeader('config',$this->Lang('configuration')) .
			$this->EndTabHeaders().
			$this->StartTabContent());
		$this->smarty->assign('start_formtab',$this->StartTab("forms"));
		$this->smarty->assign('start_configtab',$this->StartTab("config"));
		$this->smarty->assign('end_tab',$this->EndTab());
		$this->smarty->assign('end_tabs',$this->EndTabContent());
		$this->smarty->assign('title_form_name',$this->Lang('title_form_name'));
        $this->smarty->assign('title_form_alias',$this->Lang('title_form_alias'));
        $this->smarty->assign('start_configform',$this->CreateFormStart($id,
			'admin_update_config', $returnid));
        $this->smarty->assign('message', isset($params['message'])?$params['message']:'');

		$formArray = array();
		$currow = "row1";
		foreach ($forms as $thisForm)
    		{
			$oneset = new stdClass();
			$oneset->rowclass = $currow;
			if ($this->CheckPermission('Modify Forms'))
				{
				$oneset->name = $this->CreateLink($id,
				 	'admin_add_edit_form', '',
            		$thisForm['name'], array('form_id'=>$thisForm['form_id']));
				$oneset->editlink = $this->CreateLink($id,
					'admin_add_edit_form', '',
					$gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif','edit','','','systemicon'),
						array('form_id'=>$thisForm['form_id']));
				$oneset->deletelink = $this->CreateLink($id,
					'admin_delete_form', '',
					$gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif','delete','','','systemicon'),
					array('form_id'=>$thisForm['form_id']),
					$this->Lang('are_you_sure_delete_form',$thisForm['name']));

				}
			else
				{
				$oneset->name=$thisForm['name'];
				$oneset->editlink = '';
				$oneset->deletelink = '';
				}
			$oneset->usage = $thisForm['alias'];
			array_push($formArray,$oneset);
			($currow == "row1"?$currow="row2":$currow="row1");
			}
		if ($this->CheckPermission('Modify Forms'))
			{
			$this->smarty->assign('addlink',$this->CreateLink($id,
				'admin_add_edit_form', '',
				$gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('title_add_new_form'),'',
					'','systemicon'), array()));
			$this->smarty->assign('addform',$this->CreateLink($id,
				'admin_add_edit_form', '', $this->Lang('title_add_new_form'),
				array()));
			$this->smarty->assign('may_config',1);
			}
		else
			{
			$this->smarty->assign('no_permission',
				$this->Lang('lackpermission'));
			}
	
		$this->smarty->assign('title_hide_errors',$this->Lang('title_hide_errors'));		
		$this->smarty->assign('input_hide_errors',$this->CreateInputCheckbox($id, 'hide_errors', 1, $this->GetPreference('hide_errors','1')). $this->Lang('title_hide_errors_long'));		
		$this->smarty->assign('title_enable_fastadd',$this->Lang('title_enable_fastadd'));		
		$this->smarty->assign('input_enable_fastadd',$this->CreateInputCheckbox($id, 'enable_fastadd', 1, $this->GetPreference('enable_fastadd','1')). $this->Lang('title_enable_fastadd_long'));		
		$this->smarty->assign('title_show_version',$this->Lang('title_show_version'));		
		$this->smarty->assign('input_show_version',$this->CreateInputCheckbox($id, 'show_version', 1, $this->GetPreference('show_version','1')). $this->Lang('title_show_version_long'));				
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('save')));
		$this->smarty->assign('config_formend',$this->CreateFormEnd());
		
        $this->smarty->assign_by_ref('forms', $formArray);			
        echo $this->ProcessTemplate('AdminMain.tpl');
?>
