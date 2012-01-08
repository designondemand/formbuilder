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

		$aeform = new fbForm($params,true); // Useless memory eater
		//$aefield = $aeform->NewField($params);
		$aefield = fbForm::NewField($params);
		if (isset($params['fbrp_aef_upd']) ||
			(isset($params['fbrp_aef_add']) && $aefield->GetFieldType() != ''))
			{
			// save the field.
			$this->DoAction('admin_store_field', $id, $params);
			return;
			}
		elseif (isset($params['fbrp_aef_add']))
			{
			// should have got a field type definition, so give rest of the field options
			// reserve this space for special ops :)
			}
		elseif (isset($params['fbrp_aef_optadd']))
			{
			// call the field's option add method, with all available parameters
			$aefield->DoOptionAdd($params);
			}
		elseif (isset($params['fbrp_aef_optdel']))
			{
			// call the field's option delete method, with all available parameters
			$aefield->DoOptionDelete($params);
			}
		else
			{
			// new field, or implicit aef_add.
			// again, reserving the space for future endeavors
			}
		//echo $aeform->AddEditField($id, $aefield, (isset($params['fbrp_dispose_only'])?$params['fbrp_dispose_only']:0), $returnid, isset($params['fbrp_message'])?$this->ShowMessage($params['fbrp_message']):'');


/* UUTTA PASKAA!!! */


 
	
//$smarty->assign('backtoform_nav',$this->CreateLink($id, 'admin_add_edit_form', $returnid, $this->Lang('link_back_to_form'), array('form_id'=>$this->Id)));
$mainList = array();
$advList = array();

$baseList = $aefield->PrePopulateBaseAdminForm($id);
if ($aefield->GetFieldType() == '')
  {
// still need type
$smarty->assign('start_form',$this->CreateFormStart($id, 'admin_add_edit_field', $returnid));			
$fieldList = array('main'=>array(),'adv'=>array());
  }
else
  {
// we have our type
$smarty->assign('start_form',$this->CreateFormStart($id, 'admin_add_edit_field', $returnid));	
$fieldList = $aefield->PrePopulateAdminForm($id);
  }
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

if($aefield->GetId() != -1)
  {
$smarty->assign('op',$this->CreateInputHidden($id, 'fbrp_op',$this->Lang('updated')));
$smarty->assign('submit',$this->CreateInputSubmit($id, 'fbrp_aef_upd', $this->Lang('update')));
  }
else
  {
$smarty->assign('op',$this->CreateInputHidden($id, 'fbrp_op', $this->Lang('added')));
$smarty->assign('submit',$this->CreateInputSubmit($id, 'fbrp_aef_add', $this->Lang('add')));
  }

if ($aefield->HasAddOp())
  {
$smarty->assign('add',$this->CreateInputSubmit($id,'fbrp_aef_optadd',$aefield->GetOptionAddButton()));
  }

  
if ($aefield->HasDeleteOp())
  {
$smarty->assign('del',$this->CreateInputSubmit($id,'fbrp_aef_optdel',$aefield->GetOptionDeleteButton()));
  }



$smarty->assign('fb_hidden', $this->CreateInputHidden($id, 'form_id', $this->Id) . $this->CreateInputHidden($id, 'field_id', $aefield->GetId()) . $this->CreateInputHidden($id, 'fbrp_order_by', $aefield->GetOrder()).
		 $this->CreateInputHidden($id,'fbrp_set_from_form','1'));

if (/*!$aefield->IsDisposition() && */ !$aefield->IsNonRequirableField())
  {
$smarty->assign('requirable',1);
  }
else
  {
$smarty->assign('requirable',0);
  }
		
if (isset($baseList['main']))
  {
foreach ($baseList['main'] as $item)
  {
	$titleStr=$item[0];
	$inputStr=$item[1];
	$oneset = new stdClass();
	$oneset->title = $titleStr;
	if (is_array($inputStr))
	  {
	$oneset->input = $inputStr[0];
	$oneset->help = $inputStr[1];
	  }
	else
	  {
	$oneset->input = $inputStr;
	$oneset->help='';
	  }
	array_push($mainList,$oneset);
  }
  }	
if (isset($baseList['adv']))
  {
foreach ($baseList['adv'] as $item)
  {
	$titleStr = $item[0];
	$inputStr = $item[1];
	$oneset = new stdClass();
	$oneset->title = $titleStr;
	if (is_array($inputStr))
	  {
	$oneset->input = $inputStr[0];
	$oneset->help = $inputStr[1];
	  }
	else
	  {
	$oneset->input = $inputStr;
	$oneset->help='';
	  }
	array_push($advList,$oneset);
  }
  }	
if (isset($fieldList['main']))
  {
foreach ($fieldList['main'] as $item)
  {
	$titleStr=$item[0];
	$inputStr=$item[1];
	$oneset = new stdClass();
	$oneset->title = $titleStr;
	if (is_array($inputStr))
	  {
	$oneset->input = $inputStr[0];
	$oneset->help = $inputStr[1];
	  }
	else
	  {
	$oneset->input = $inputStr;
	$oneset->help='';
	  }
	array_push($mainList,$oneset);
  }
  }
if (isset($fieldList['adv']))
  {
foreach ($fieldList['adv'] as $item)
  {
	$titleStr=$item[0];
	$inputStr=$item[1];
	$oneset = new stdClass();
	$oneset->title = $titleStr;
	if (is_array($inputStr))
	  {
	$oneset->input = $inputStr[0];
	$oneset->help = $inputStr[1];
	  }
	else
	  {
	$oneset->input = $inputStr;
	$oneset->help='';
	  }
	array_push($advList,$oneset);
  }
  }
	
$aefield->PostPopulateAdminForm($mainList, $advList);
$smarty->assign('mainList',$mainList);
$smarty->assign('advList',$advList);
return $this->ProcessTemplate('AddEditField.tpl');


		
?>
