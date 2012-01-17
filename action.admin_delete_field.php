<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;

$form = new fbForm($params, true);
$form->DeleteField($params['field_id']);

$parms['fb_message'] = $this->Lang('field_deleted');
$this->Redirect($id, 'defaultadmin', $returnid, $parms);
?>

