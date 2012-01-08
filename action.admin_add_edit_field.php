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
# Check params
#---------------------	

$formid = -1;
if (isset($params['form_id'])) {

	$formid = $params['form_id'];
}

$fieldid = -1;
if (isset($params['field_id'])) {

	$fieldid = $params['field_id'];
}

$fieldtype = '';
if (isset($params['field_type'])) {

	$fieldtype = $params['field_type'];
}

$tab = '';
if (isset($params['active_tab'])) {

	$tab = $params['active_tab'];
}

$opt_num = 1;
if (isset($params['fbrp_opt_num'])) {

	$opt_num = $params['fbrp_opt_num'];
}

/*
if (isset($params['fbrp_set_field_level'])) {

	$this->SetPreference('show_field_level',$params['fbrp_set_field_level']);
}
*/
#---------------------
# Load objects
#---------------------	

$form = new fbForm($params, true);
$field = $form->LoadField($params);

$parms = array();
$parms['form_id'] = $formid;

#---------------------
# Submit
#---------------------	

if (isset($params['cancel'])) {

	$this->Redirect($id, 'admin_add_edit_form', $returnid, $parms);
}

if(is_object($field)) {

	if (isset($params['fbrp_aef_submit'])) {

		//$val = $aefield->AdminValidate();	// Not in use atm, make this work when you have time.
		$field->PostAdminSubmitCleanup();
		$field->Store(true);
		$field->PostFieldSaveProcess($params);	
		$parms['fb_message'] = ($fieldid != -1 ? $this->Lang('update') : $this->Lang('added'));
		$this->Redirect($id, 'admin_add_edit_form', $returnid, $parms);
		
	} 
	
	if (isset($params['fbrp_aef_optadd'])) {

		$field->DoOptionAdd($params);
	}
	
	if (isset($params['fbrp_aef_optdel'])) {

		$field->DoOptionDelete($params);
	} 
}

#---------------------
# Smarty assigns
#---------------------	

// Initiate arrays
$mainList = array();
$advList = array();
$parms['field_id'] = $fieldid;

// Check if we need fields dropdown
if(!is_object($field) || $field->GetId() == -1) {

	$smarty->assign('field_type', $this->CreateInputDropdown($id, 'field_type',array_merge(array($this->Lang('select_type')=>''), $this->field_types), -1, $fieldtype, 'onchange="this.form.submit()"'));	
	
} else {	

	$smarty->assign('field_type', $field->GetDisplayFriendlyType() . $this->CreateInputHidden($id, 'field_type', $field->getFieldType()));
}

// Populate form
if(is_object($field)) {

	// Set parms for form
	$parms['fbrp_order_by'] = $field->GetOrder();

	// Change to use list when you have time.
	$baselist = $field->PrePopulateBaseAdminForm($id);
	$fieldlist = $field->PrePopulateAdminForm($id);
	
	if($field->GetId() != -1) {
	
		$smarty->assign('submit',$this->CreateInputSubmit($id, 'fbrp_aef_submit', $this->Lang('update')));
	} else {
	
		$smarty->assign('submit',$this->CreateInputSubmit($id, 'fbrp_aef_submit', $this->Lang('add')));
	}

	if ($field->HasAddOp()) {
	
		$smarty->assign('add',$this->CreateInputSubmit($id,'fbrp_aef_optadd',$field->GetOptionAddButton()));
	}

	if ($field->HasDeleteOp()) {
	
		$smarty->assign('del',$this->CreateInputSubmit($id,'fbrp_aef_optdel',$field->GetOptionDeleteButton()));
	}


/*
	$smarty->assign('fb_hidden', $this->CreateInputHidden($id, 'form_id', $formid) . 
								$this->CreateInputHidden($id, 'field_id', $fieldid) . 
								$this->CreateInputHidden($id, 'fbrp_order_by', $field->GetOrder()).
								$this->CreateInputHidden($id,'fbrp_set_from_form',1)); //?????

*/

	// Base list main tab
	if (isset($baselist['main'])) {
	
		foreach ($baselist['main'] as $item) {
		
			$titleStr=$item[0];
			$inputStr=$item[1];
			$oneset = new stdClass();
			$oneset->title = $titleStr;
			if (is_array($inputStr)) {
			
				$oneset->input = $inputStr[0];
				$oneset->help = $inputStr[1];
			} else {
			
				$oneset->input = $inputStr;
				$oneset->help='';
			}
			
			$mainList[] = $oneset;
		}
	}	
	
	// Base list advanced tab
	if (isset($baselist['adv'])) {
	
		foreach ($baselist['adv'] as $item)
		{
			$titleStr = $item[0];
			$inputStr = $item[1];
			$oneset = new stdClass();
			$oneset->title = $titleStr;
			if (is_array($inputStr)) {
			
				$oneset->input = $inputStr[0];
				$oneset->help = $inputStr[1];
			} else {
			
				$oneset->input = $inputStr;
				$oneset->help='';
			}
			
			$advList[] = $oneset;
		}
	}	
	
	// Field list main tab
	if (isset($fieldlist['main'])) {
	
		foreach ($fieldlist['main'] as $item) {
		
			$titleStr=$item[0];
			$inputStr=$item[1];
			$oneset = new stdClass();
			$oneset->title = $titleStr;
			if (is_array($inputStr)) {
			
				$oneset->input = $inputStr[0];
				$oneset->help = $inputStr[1];
			} else {
			
				$oneset->input = $inputStr;
				$oneset->help='';
			}
			
			$mainList[] = $oneset;
		}
	}
	
	// Field list advanced tab
	if (isset($fieldlist['adv'])) {
	
		foreach ($fieldlist['adv'] as $item) {
		
			$titleStr=$item[0];
			$inputStr=$item[1];
			$oneset = new stdClass();
			$oneset->title = $titleStr;
			if (is_array($inputStr)) {
			
				$oneset->input = $inputStr[0];
				$oneset->help = $inputStr[1];
			} else {
			
				$oneset->input = $inputStr;
				$oneset->help='';
			}
			
			$advList[] = $oneset;
		}
	}

	// Clean up some fields if neccery
	$field->PostPopulateAdminForm($mainList, $advList);

}

$smarty->assign('start_form',$this->CreateFormStart($id, 'admin_add_edit_field', $returnid, 'post', '', false, '', $parms));		
$smarty->assign('end_form', $this->CreateFormEnd());
$smarty->assign('tab_start',$this->StartTabHeaders().
							$this->SetTabHeader('maintab',$this->Lang('tab_main')).
							$this->SetTabHeader('advancedtab',$this->Lang('tab_advanced')).
							$this->EndTabHeaders() . $this->StartTabContent());
$smarty->assign('tabs_end',$this->EndTabContent());
$smarty->assign('maintab_start',$this->StartTab("maintab"));
$smarty->assign('advancedtab_start',$this->StartTab("advancedtab"));
$smarty->assign('tab_end',$this->EndTab());
$smarty->assign('notice_select_type',$this->Lang('notice_select_type'));
$smarty->assign('cancel',$this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$smarty->assign('opt_num',$this->CreateInputText($id, 'fbrp_opt_num', $opt_num, 1, 2));

$smarty->assign('mainList',$mainList);
$smarty->assign('advList',$advList);

#---------------------
# Output
#---------------------

echo $this->ProcessTemplate('AddEditField.tpl');
		
?>
