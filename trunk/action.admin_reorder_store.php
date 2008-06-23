<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

$order_list = explode(',',$params['fbrp_order']);
$count = 1;
//debug_display($params['fbrp_order']);
if (! is_array($order_list))
	{
	$order_list = array($order_list);
	}

$db =& $gCms->GetDb();
$sql = 'update '.cms_db_prefix().
	'module_fb_field set order_by=? where field_id=?';
	
foreach ($order_list as $thisField)
	{
	$fldid = substr($thisField,strpos($thisField,'_')+1);
	$rs = $db->Execute($sql, array($count, $fldid));
	$count++;
	}

$aeform = new fbForm($this, $params, true);

echo $aeform->AddEditForm($id, $returnid, $this->Lang('field_order_updated'));
?>