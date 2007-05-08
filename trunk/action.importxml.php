<?php
if (!isset($gCms)) exit;

if (! $this->CheckAccess()) exit;

$params['xml_file'] = $_FILES[$id.'xmlfile']['tmp_name'];

$aeform = new fbForm($this, $params, true);
$res = $aeform->ImportXML($params);

if ($res)
	{
	$params['message'] = $this->Lang('form_imported');
	}
else
	{
	$params['message'] = $this->Lang('form_import_failed');
	}
$this->DoAction('defaultadmin', $id, $params);
?>