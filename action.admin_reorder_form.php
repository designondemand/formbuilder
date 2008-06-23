<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;


$aeform = new fbForm($this, $params, true);

$fields = array();
foreach ($aeform->GetFields() as $thisField)
   {
   $fld = new StdClass();
   $fld->id = 'fbrp_'.$thisField->GetId();
   $fld->name = $thisField->GetName();
   $fld->type = $thisField->GetFieldType();
   array_push($fields, $fld);
   }

$this->smarty->assign('start_form',$this->CreateFormStart($id,
			'admin_reorder_store', $returnid, 'post'));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'fbrp_submit', $this->Lang('reorder'),'onclick="return send_order_var()"'));
$this->smarty->assign('end_form',$this->CreateFormEnd());
$this->smarty->assign('id',$id);
$this->smarty->assign('fb_hidden',$this->CreateInputHidden($id,'form_id',$params['form_id']));
$this->smarty->assign_by_ref('fields',$fields);
$this->smarty->assign('scriptaculous',
   '<script src="'.dirname(dirname(dirname(__FILE__))).
   '/lib/scriptaculous/scriptaculous.js" type="text/javascript"></script>');

echo $this->ProcessTemplate('ReorderForm.tpl');
?>