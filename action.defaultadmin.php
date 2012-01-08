<?php
#-------------------------------------------------------------------------
# Module: FormBuilder
# Version: 1.0, released 2012
#
# Copyright (c) 2007, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple Forge:
# http://dev.cmsmadesimple.org/projects/formbuilder/
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Forms')) exit;

#---------------------
# Get all forms
#---------------------

$forms = $this->GetForms();

$formArray = array();
foreach ($forms as $thisForm) {

	$oneset = new stdClass();

	$oneset->name = $this->CreateLink($id,'admin_add_edit_form', '',$thisForm['name'], array('form_id'=>$thisForm['form_id']));
	$oneset->xml = $this->CreateLink($id,'exportxml','',"<img src=\"".$config['root_url']."/modules/".$this->GetName()."/images/xml_rss.gif\" class=\"systemicon\" alt=\"Export Form as XML\" />",array('form_id'=>$thisForm['form_id']));
	$oneset->editlink = $this->CreateLink($id,'admin_add_edit_form', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/edit.gif',$this->Lang('edit'),'','','systemicon'), array('form_id'=>$thisForm['form_id']));
	$oneset->deletelink = $this->CreateLink($id, 'admin_delete_form', '', $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif',$this->Lang('delete'),'','','systemicon'), array('form_id'=>$thisForm['form_id']), $this->Lang('are_you_sure_delete_form',$thisForm['name']));
	$oneset->usage = $thisForm['alias'];
	
	$formArray[] = $oneset;
}

#---------------------
# Smarty assigns
#---------------------	

$this->smarty->assign('addlink',$this->CreateLink($id, 'admin_add_edit_form', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('title_add_new_form'),'','','systemicon'), array()));
$this->smarty->assign('addform',$this->CreateLink($id, 'admin_add_edit_form', '', $this->Lang('title_add_new_form'), array()));

$this->smarty->assign_by_ref('forms', $formArray);
$this->smarty->assign('tabheaders', $this->StartTabHeaders() .
				$this->SetTabHeader('forms',$this->Lang('forms')) .
				$this->SetTabHeader('config',$this->Lang('configuration')) .
				$this->EndTabHeaders().
				$this->StartTabContent());
$this->smarty->assign('start_formtab',$this->StartTab("forms", $params));
$this->smarty->assign('start_configtab',$this->StartTab("config", $params));
$this->smarty->assign('end_tab',$this->EndTab());
$this->smarty->assign('end_tabs',$this->EndTabContent());

$this->smarty->assign('start_configform',$this->CreateFormStart($id,'admin_update_config', $returnid));
$this->smarty->assign('input_hide_errors',$this->CreateInputCheckbox($id, 'fbrp_hide_errors', 1, $this->GetPreference('hide_errors','0')). $this->Lang('title_hide_errors_long'));
$this->smarty->assign('input_relaxed_email_regex',$this->CreateInputCheckbox($id, 'fbrp_relaxed_email_regex', 1, $this->GetPreference('relaxed_email_regex','0')). $this->Lang('title_relaxed_regex_long'));
$this->smarty->assign('input_enable_fastadd',$this->CreateInputCheckbox($id, 'fbrp_enable_fastadd', 1, $this->GetPreference('enable_fastadd','1')). $this->Lang('title_enable_fastadd_long'));		
$this->smarty->assign('input_require_fieldnames',$this->CreateInputCheckbox($id, 'fbrp_require_fieldnames', 1, $this->GetPreference('require_fieldnames','1')). $this->Lang('title_require_fieldnames_long'));		
$this->smarty->assign('input_unique_fieldnames',$this->CreateInputCheckbox($id, 'fbrp_unique_fieldnames', 1, $this->GetPreference('unique_fieldnames','1')). $this->Lang('title_unique_fieldnames_long'));		
$this->smarty->assign('input_enable_antispam',$this->CreateInputCheckbox($id, 'fbrp_enable_antispam', 1, $this->GetPreference('enable_antispam','1')). $this->Lang('title_enable_antispam_long'));
$this->smarty->assign('input_show_fieldids',$this->CreateInputcheckbox($id,'fbrp_show_fieldids',1, $this->GetPreference('show_fieldids','0')). $this->Lang('title_show_fieldids_long'));
$this->smarty->assign('input_show_fieldaliases',$this->CreateInputcheckbox($id,'fbrp_show_fieldaliases',1, $this->GetPreference('show_fieldaliases','1')). $this->Lang('title_show_fieldaliases_long'));
$this->smarty->assign('input_show_version',$this->CreateInputCheckbox($id, 'fbrp_show_version', 1, $this->GetPreference('show_version','1')). $this->Lang('title_show_version_long'));
$this->smarty->assign('input_blank_invalid',$this->CreateInputCheckbox($id, 'fbrp_blank_invalid', 1, $this->GetPreference('blank_invalid','0')). $this->Lang('title_blank_invalid_long'));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('save')));
$this->smarty->assign('endform',$this->CreateFormEnd());

$this->smarty->assign('start_xmlform',$this->CreateFormStart($id, 'importxml', $returnid, 'post','multipart/form-data'));
$this->smarty->assign('submitxml', $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('upload')));

$this->smarty->assign('input_xml_to_upload',$this->CreateInputFile($id, 'fbrp_xmlfile'));
$this->smarty->assign('input_xml_upload_formname',$this->CreateInputText($id,'fbrp_import_formname','',25));
$this->smarty->assign('input_xml_upload_formalias',$this->CreateInputText($id,'fbrp_import_formalias','',25));

#---------------------
# Output
#---------------------

echo $this->ProcessTemplate('AdminMain.tpl');
?>
