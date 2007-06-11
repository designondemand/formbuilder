<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

echo '<script src="'.dirname(dirname(dirname(__FILE__))).'/lib/scriptaculous/scriptaculous.js" type="text/javascript"></script>';

$aeform = new fbForm($this, $params, true);

$listArray = array();
$output = '';
$output .= '<ul id="parent0" class="sortableList">'."\n";

foreach ($aeform->GetFields() as $thisField)
	{
   $output .= '<li id="fld_'.$thisField->GetId().'">'.$thisField->GetName().'</li>';
	}
$output .= '</ul>';

echo $output;
//$sortableLists->printForm($_SERVER['PHP_SELF'], 'post', $this->Lang('reorder'), 'button', 'sortableListForm', $this->Lang('cancel'), $output);
?>
<script type="text/javascript">
Sortable.create('parent0',{tag:'li'});
</script>