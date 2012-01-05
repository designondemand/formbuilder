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
if (! $this->CheckAccess()) exit;

// Check what?
if (isset($params['fbrp_set_field_level'])) {

	$this->SetPreference('show_field_level',$params['fbrp_set_field_level']);
}

// Set active tab			
$tab = $this->GetActiveTab($params);
		
// Load form
$form = new fbForm($params, true);
$contentops = cmsms()->GetContentOperations();

// Start smarty assigns....
if(!empty($message)) $smarty->assign('message',$this->ShowMessage($message));
$smarty->assign('formstart', $this->CreateFormStart($id, 'admin_store_form', $returnid));
$smarty->assign('formid', $this->CreateInputHidden($id, 'form_id', $form->getId()));
$smarty->assign('tab_start',$this->StartTabHeaders().
						$this->SetTabHeader('maintab',$this->Lang('tab_main'),('maintab' == $tab)?true:false).
						$this->SetTabHeader('submittab',$this->Lang('tab_submit'),('submittab' == $tab)?true:false).
						$this->SetTabHeader('symboltab',$this->Lang('tab_symbol'),('symboltab' == $tab)?true:false).
						$this->SetTabHeader('captchatab',$this->Lang('tab_captcha'),('captchatab' == $tab)?true:false).
						$this->SetTabHeader('udttab',$this->Lang('tab_udt'),('udttab' == $tab)?true:false).
						$this->SetTabHeader('templatelayout',$this->Lang('tab_templatelayout'),('templatelayout' == $tab)?true:false).
						$this->SetTabHeader('submittemplate',$this->Lang('tab_submissiontemplate'),('submittemplate' == $tab)?true:false).
						$this->EndTabHeaders() . $this->StartTabContent());
  
$smarty->assign('tabs_end',$this->EndTabContent());
$smarty->assign('maintab_start',$this->StartTab("maintab"));
$smarty->assign('submittab_start',$this->StartTab("submittab"));
$smarty->assign('symboltab_start',$this->StartTab("symboltab"));
$smarty->assign('udttab_start',$this->StartTab("udttab"));
$smarty->assign('templatetab_start',$this->StartTab("templatelayout"));
$smarty->assign('submittemplatetab_start',$this->StartTab("submittemplate"));
$smarty->assign('captchatab_start',$this->StartTab("captchatab"));
$smarty->assign('tab_end',$this->EndTab());
$smarty->assign('form_end',$this->CreateFormEnd());
$smarty->assign('title_form_name',$this->Lang('title_form_name'));
$smarty->assign('input_form_name', $this->CreateInputText($id, 'fbrp_form_name', $form->getName(), 50));

$smarty->assign('title_load_template',$this->Lang('title_load_template'));
$thisLink = $this->CreateLink($id, 'admin_get_template', $returnid, '', array(), '', true);
$smarty->assign('security_key',CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY]);

$templateList = array(
			''=>'',
			$this->Lang('default_template')=>'RenderFormDefault.tpl', 
			$this->Lang('table_left_template')=>'RenderFormTableTitleLeft.tpl', 
			$this->Lang('table_top_template')=>'RenderFormTableTitleTop.tpl');
	
$allForms = $this->GetForms();
foreach ($allForms as $thisForm) {

	if ($thisForm['form_id'] != $form->getId()) {
	
		$templateList[$this->Lang('form_template_name',$thisForm['name'])] = $thisForm['form_id'];
	}
}

$smarty->assign('input_load_template',$this->CreateInputDropdown($id, 'fbrp_fb_template_load', $templateList, -1, '', 'id="fb_template_load" onchange="jQuery(this).fb_get_template(\''.$this->Lang('template_are_you_sure').'\',\''.$thisLink.'\');"'));
$smarty->assign('help_template_variables',$this->Lang('template_variable_help'));
$smarty->assign('title_form_unspecified',$this->Lang('title_form_unspecified'));
$smarty->assign('input_form_unspecified', $this->CreateInputText($id, 'fbrp_forma_unspecified', $form->GetAttr('unspecified',$this->Lang('unspecified')), 50));
$smarty->assign('title_form_status',$this->Lang('title_form_status'));
$smarty->assign('text_ready',$this->Lang('title_ready_for_deployment'));
$smarty->assign('title_form_alias',$this->Lang('title_form_alias'));
$smarty->assign('input_form_alias',$this->CreateInputText($id, 'fbrp_form_alias', $form->getAlias(), 50));
$smarty->assign('title_form_css_class', $this->Lang('title_form_css_class'));
$smarty->assign('input_form_css_class', $this->CreateInputText($id, 'fbrp_forma_css_class', $form->GetAttr('css_class','formbuilderform'), 50,50));
$smarty->assign('title_form_fields', $this->Lang('title_form_fields'));
$smarty->assign('title_form_main', $this->Lang('title_form_main'));

if( $this->GetPreference('show_fieldids',0) != 0 ) {
	
	$smarty->assign('title_field_id', $this->Lang('title_field_id'));
}

if( $this->GetPreference('show_fieldaliases',1) != 0 ) {

	$smarty->assign('title_field_alias', $this->Lang('title_field_alias_short'));
}

$smarty->assign('back', $this->CreateLink($id, 'defaultadmin', '', $this->Lang('back_top'), array()));

$smarty->assign('title_field_name', $this->Lang('title_field_name'));
$smarty->assign('title_field_type', $this->Lang('title_field_type'));
$smarty->assign('title_field_type', $this->Lang('title_field_type'));
$smarty->assign('title_form_template', $this->Lang('title_form_template'));
$smarty->assign('title_list_delimiter', $this->Lang('title_list_delimiter'));
$smarty->assign('title_redirect_page', $this->Lang('title_redirect_page'));
$smarty->assign('title_submit_action', $this->Lang('title_submit_action'));
$smarty->assign('title_submit_response', $this->Lang('title_submit_response'));
$smarty->assign('title_must_save_order', $this->Lang('title_must_save_order'));
$smarty->assign('title_inline_form', $this->Lang('title_inline_form'));
$smarty->assign('title_submit_actions', $this->Lang('title_submit_actions'));
$smarty->assign('title_submit_labels', $this->Lang('title_submit_labels'));
$smarty->assign('title_submit_javascript', $this->Lang('title_submit_javascript'));
$smarty->assign('title_submit_help', cmsms()->variables['admintheme']->DisplayImage('icons/system/info.gif','true','','','systemicon').$this->Lang('title_submit_help'));
$smarty->assign('title_submit_response_help', cmsms()->variables['admintheme']->DisplayImage('icons/system/info.gif','true','','','systemicon').$this->Lang('title_submit_response_help'));

$submitActions = array($this->Lang('display_text')=>'text', $this->Lang('redirect_to_page')=>'redir');
$smarty->assign('input_submit_action', $this->CreateInputRadioGroup($id, 'fbrp_forma_submit_action', $submitActions, $form->GetAttr('submit_action','text')));

// Check if Captcha module is installed
$captcha = $this->getModuleInstance('Captcha');
if (!is_object($captcha)) {

	$smarty->assign('title_install_captcha', $this->Lang('title_captcha_not_installed'));
	$smarty->assign('captcha_installed',0);
} else {

	$smarty->assign('title_use_captcha', $this->Lang('title_use_captcha'));
	$smarty->assign('captcha_installed',1);
	$smarty->assign('input_use_captcha',$this->CreateInputHidden($id,'fbrp_forma_use_captcha','0').$this->CreateInputCheckbox($id,'fbrp_forma_use_captcha','1',$form->GetAttr('use_captcha','0')). $this->Lang('title_use_captcha_help'));
}
$smarty->assign('title_information',$this->Lang('information'));
$smarty->assign('title_order',$this->Lang('order'));
$smarty->assign('title_field_required_abbrev',$this->Lang('title_field_required_abbrev'));
$smarty->assign('hasdisposition',$form->HasDisposition()?1:0);

$maxOrder = 1;

// Old form, load fields
if($form->getId() > 0) {
  
	$smarty->assign('fb_hidden', $this->CreateInputHidden($id, 'fbrp_form_op',$this->Lang('updated')). $this->CreateInputHidden($id, 'fbrp_sort','','class="fbrp_sort"'));
	$smarty->assign('adding',0);
	$smarty->assign('save_button', $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('save')));
	$smarty->assign('submit_button', $this->CreateInputHidden($id, 'active_tab', '', 'id="fbr_atab"'). $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('save_and_continue'),'onclick="jQuery(this).fb_set_tab()"'));

	$fieldList = array();
	$currow = "row1";
	$count = 1;
	$last = $form->getFieldCount();
	
	foreach ($form->Fields as $thisField) {

		$oneset = new stdClass();
		$oneset->rowclass = $currow;
		$oneset->name = $this->CreateLink($id, 'admin_add_edit_field', '', $thisField->GetName(), array('field_id'=>$thisField->GetId(),'form_id'=>$form->GetId()));
		if( $this->GetPreference('show_fieldids',0) != 0 ) {
			
			$oneset->id = $this->CreateLink($id, 'admin_add_edit_field', '', $thisField->GetId(), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
		}
		$oneset->type = $thisField->GetDisplayFriendlyType();
		$oneset->alias = $thisField->GetAlias();
		$oneset->id = $thisField->GetID();

		if (!$thisField->DisplayInForm() || $thisField->IsNonRequirableField()) {
		
			$no_avail = $this->Lang('not_available');
			if($this->cms->variables['admintheme']->themeName == 'NCleanGrey') {

				$oneset->disposition = '<img src="'.$config['root_url'].'/modules/'.$this->GetName().'/images/stop.gif" width="20" height="20" alt="'.$no_avail.'" title="'.$no_avail.'" />';
			} else {
		  
				$oneset->disposition = '<img src="'.$config['root_url'].'/modules/'.$this->GetName().'/images/stop.gif" width="16" height="16" alt="'.$no_avail.'" title="'.$no_avail.'" />';
			}
		} else if ($thisField->IsRequired()) {
		
			$oneset->disposition = $this->CreateLink($id, 'admin_update_field_required', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/true.gif','true','','','systemicon'), array('form_id'=>$this->Id,'fbrp_active'=>'off','field_id'=>$thisField->GetId()),'', '', '', 'class="true" onclick="jQuery(this).fb_admin_update_field_required(); return false;"');
		} else {
		
			$oneset->disposition = $this->CreateLink($id, 'admin_update_field_required', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/false.gif','false','','','systemicon'), array('form_id'=>$this->Id,'fbrp_active'=>'on','field_id'=>$thisField->GetId()),'', '', '', 'class="false" onclick="jQuery(this).fb_admin_update_field_required(); return false;"');
		}
		  
		$oneset->field_status = $thisField->StatusInfo();
		$oneset->editlink = $this->CreateLink($id, 'admin_add_edit_field', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/edit.gif',$this->Lang('edit'),'','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id));
		$oneset->deletelink = $this->CreateLink($id, 'admin_delete_field', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/delete.gif',$this->Lang('delete'),'','','systemicon'), array('field_id'=>$thisField->GetId(),'form_id'=>$this->Id),'', '', '', 'onclick="jQuery(this).fb_delete_field(\''.$this->Lang('are_you_sure_delete_field',htmlspecialchars($thisField->GetName())).'\'); return false;"');

		/* Removed By Stikki, reinstated by SjG with Javascript to hide it if Javascript's enabled. */
		if ($count > 1) {
		
			$oneset->up = $this->CreateLink($id, 'admin_update_field_order', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/arrow-u.gif','up','','','systemicon'), array('form_id'=>$this->Id,'fbrp_dir'=>'up','field_id'=>$thisField->GetId()));
		} else {
		
			$oneset->up = '&nbsp;';
		}
		
		if ($count < $last) {
		
			$oneset->down=$this->CreateLink($id, 'admin_update_field_order', '', cmsms()->variables['admintheme']->DisplayImage('icons/system/arrow-d.gif','down','','','systemicon'), array('form_id'=>$this->Id,'fbrp_dir'=>'down','field_id'=>$thisField->GetId()));
		} else {
		
			$oneset->down = '&nbsp;';
		}

		($currow == "row1"?$currow="row2":$currow="row1");
		$count++;
		if ($thisField->GetOrder() >= $maxOrder) {
		
			$maxOrder = $thisField->GetOrder() + 1;
		}
		
		array_push($fieldList, $oneset);
	} // end of form fields
	  
	$smarty->assign('fields',$fieldList);
	$smarty->assign('add_field_link', $this->CreateLink($id, 'admin_add_edit_field', $returnid, cmsms()->variables['admintheme']->DisplayImage('icons/system/newobject.gif',$this->Lang('title_add_new_field'),'','','systemicon'),array('form_id'=>$this->Id, 'fbrp_order_by'=>$maxOrder), '', false) . $this->CreateLink($id, 'admin_add_edit_field', $returnid,$this->Lang('title_add_new_field'),array('form_id'=>$this->Id, 'fbrp_order_by'=>$maxOrder), '', false));

	if ($this->GetPreference('enable_fastadd',1) == 1) {

		$smarty->assign('fastadd',1);
		$smarty->assign('title_fastadd',$this->Lang('title_fastadd'));
		$typeInput = "<script type=\"text/javascript\">
	/* <![CDATA[ */
	function fast_add(field_type)
	{
	var type=field_type.options[field_type.selectedIndex].value;
	var link = '".$this->CreateLink($id, 'admin_add_edit_field', $returnid,'',array('form_id'=>$this->Id, 'fbrp_order_by'=>$maxOrder), '', true,true)."&".$id."fbrp_field_type='+type;
	this.location=link;
	return true;
	}
	/* ]]> */
	</script>";

		$typeInput = str_replace('&amp;','&',$typeInput); 
		$this->initialize();
		if ($this->GetPreference('show_field_level','basic') == 'basic') {
		
			$smarty->assign('input_fastadd',$typeInput.$this->CreateInputDropdown($id, 'fbrp_field_type',array_merge(array($this->Lang('select_type')=>''),$this->std_field_types), -1,'', 'onchange="fast_add(this)"').
			$this->Lang('title_switch_advanced').
			$this->CreateLink($id, 'admin_add_edit_form', $returnid,$this->Lang('title_switch_advanced_link'),array('form_id'=>$this->Id, 'fbrp_set_field_level'=>'advanced')));
		} else {
		
			$smarty->assign('input_fastadd',$typeInput.$this->CreateInputDropdown($id, 'fbrp_field_type',array_merge(array($this->Lang('select_type')=>''),$this->field_types), -1,'', 'onchange="fast_add(this)"').
			$this->Lang('title_switch_basic').
			$this->CreateLink($id, 'admin_add_edit_form', $returnid,$this->Lang('title_switch_basic_link'),array('form_id'=>$this->Id, 'fbrp_set_field_level'=>'basic')));
		}
	}		

// New form 
} else {

	$smarty->assign('save_button','');
	$smarty->assign('submit_button', $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('add')));
	$smarty->assign('fb_hidden', $this->CreateInputHidden($id, 'fbrp_form_op',$this->Lang('added')).$this->CreateInputHidden($id, 'fbrp_sort','','id="fbrp_sort"'));
	$smarty->assign('adding',1);
}

$smarty->assign('link_notready',"<strong>".$this->Lang('title_not_ready1')."</strong> ".$this->Lang('title_not_ready2')." ".$this->CreateLink($id, 'admin_add_edit_field', $returnid,$this->Lang('title_not_ready_link'),array('form_id'=>$this->Id, 'fbrp_order_by'=>$maxOrder,'fbrp_dispose_only'=>1), '', false, false,'class="module_fb_link"')." ".$this->Lang('title_not_ready3'));
$smarty->assign('input_inline_form',$this->CreateInputHidden($id,'fbrp_forma_inline','0'). $this->CreateInputCheckbox($id,'fbrp_forma_inline','1',$form->GetAttr('inline','0')). $this->Lang('title_inline_form_help'));
$smarty->assign('title_form_submit_button', $this->Lang('title_form_submit_button'));
$smarty->assign('input_form_submit_button', $this->CreateInputText($id, 'fbrp_forma_submit_button_text', $form->GetAttr('submit_button_text',$this->Lang('button_submit')), 35, 35));
$smarty->assign('title_submit_button_safety', $this->Lang('title_submit_button_safety_help'));
$smarty->assign('input_submit_button_safety', $this->CreateInputHidden($id,'fbrp_forma_input_button_safety','0'). $this->CreateInputCheckbox($id,'fbrp_forma_input_button_safety','1',$form->GetAttr('input_button_safety','0')). $this->Lang('title_submit_button_safety'));
$smarty->assign('title_form_prev_button', $this->Lang('title_form_prev_button'));
$smarty->assign('input_form_prev_button', $this->CreateInputText($id, 'fbrp_forma_prev_button_text', $form->GetAttr('prev_button_text',$this->Lang('button_previous')), 35, 35));

$smarty->assign('input_title_user_captcha', $this->CreateInputText($id, 'fbrp_forma_title_user_captcha', $form->GetAttr('title_user_captcha',$this->Lang('title_user_captcha')),35,80));
$smarty->assign('title_title_user_captcha',$this->Lang('title_title_user_captcha'));

$smarty->assign('input_title_user_captcha_error', $this->CreateInputText($id, 'fbrp_forma_captcha_wrong', $form->GetAttr('captcha_wrong', $this->Lang('wrong_captcha')),35,80));
$smarty->assign('title_user_captcha_error',$this->Lang('title_user_captcha_error'));

$smarty->assign('title_form_next_button', $this->Lang('title_form_next_button'));
$smarty->assign('input_form_next_button', $this->CreateInputText($id, 'fbrp_forma_next_button_text', $form->GetAttr('next_button_text',$this->Lang('button_continue')), 35, 35));
$smarty->assign('title_form_predisplay_udt', $this->Lang('title_form_predisplay_udt'));
$smarty->assign('title_form_predisplay_each_udt', $this->Lang('title_form_predisplay_each_udt'));

$usertagops = cmsms()->GetUserTagOperations();
$usertags = $usertagops->ListUserTags();
$usertaglist = array();
$usertaglist[$this->lang('none')] = -1;
foreach( $usertags as $key => $value ) {

	$usertaglist[$value] = $key;
}

$smarty->assign('input_form_predisplay_udt', $this->CreateInputDropdown($id,'fbrp_forma_predisplay_udt',$usertaglist,-1, $form->GetAttr('predisplay_udt',-1)));
$smarty->assign('input_form_predisplay_each_udt', $this->CreateInputDropdown($id,'fbrp_forma_predisplay_each_udt',$usertaglist,-1, $form->GetAttr('predisplay_each_udt',-1)));
$smarty->assign('title_form_validate_udt', $this->Lang('title_form_validate_udt'));
$smarty->assign('input_form_validate_udt', $this->CreateInputDropdown($id,'fbrp_forma_validate_udt',$usertaglist,-1, $form->GetAttr('validate_udt',-1)));


$smarty->assign('title_form_required_symbol', $this->Lang('title_form_required_symbol'));
$smarty->assign('input_form_required_symbol', $this->CreateInputText($id, 'fbrp_forma_required_field_symbol', $form->GetAttr('required_field_symbol','*'), 50));
$smarty->assign('input_list_delimiter', $this->CreateInputText($id, 'fbrp_forma_list_delimiter', $form->GetAttr('list_delimiter',','), 50));

$smarty->assign('input_redirect_page',$contentops->CreateHierarchyDropdown('',$form->GetAttr('redirect_page','0'), $id.'fbrp_forma_redirect_page'));

$smarty->assign('input_form_template',$this->CreateTextArea(false, $id, $form->GetAttr('form_template',$form->DefaultTemplate()), 'fbrp_forma_form_template','','fb_form_template', '', '', '80', '15','','html'));
$smarty->assign('input_submit_javascript', $this->CreateTextArea(false, $id, $form->GetAttr('submit_javascript',''), 'fbrp_forma_submit_javascript','module_fb_area_short','fb_submit_javascript', '', '', '80', '15','','js'). '<br />'.$this->Lang('title_submit_javascript_long'));
$smarty->assign('input_submit_response', $this->CreateTextArea(false, $id, $form->GetAttr('submit_response',$form->createSampleTemplate(true,false)), 'fbrp_forma_submit_response','module_fb_area_wide','', '', '', '80', '15','','html'));

// Admin help
$parms = array();
$parms['forma_submit_response']['html_button'] = true;
$parms['forma_submit_response']['txt_button'] = false;
$parms['forma_submit_response']['is_one_line'] = false;
$parms['forma_submit_response']['is_email'] = false;
$smarty->assign('help_submit_response', $form->AdminTemplateHelp($id,$parms));

echo $this->ProcessTemplate('AddEditForm.tpl');

//echo $aeform->AddEditForm($id, $returnid, $tab, isset($params['fbrp_message'])?$params['fbrp_message']:'');

?>