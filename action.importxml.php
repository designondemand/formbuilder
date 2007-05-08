<?php
if (!isset($gCms)) exit;

if (! $this->CheckAccess()) exit;

$params['xml_file'] = $_FILES[$id.'xmlfile']['tmp_name'];

$aeform = new fbForm($this, $params, true);
$aeform->ImportXML($params);

$params['message'] = $this->Lang('form_imported');
$this->DoAction('defaultadmin', $id, $params);
?>