<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDispositionUserTag extends  fbFieldBase 
{

  var $sampleTemplateCode;
  var $sampleHeader;

  function fbDispositionUserTag(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'DispositionUserTag';
    $this->IsDisposition = true;
    $this->NonRequirableField = true;
    $this->DisplayInForm = false;
    $this->sortable = false;
  }

  function StatusInfo()
  {
    $mod=&$this->form_ptr->module_ptr;
    return $this->GetOption('udtname',$mod->Lang('unspecified'));
  }

  function DisposeForm($returnid)
  {
    $mod=&$this->form_ptr->module_ptr;
    $others = &$this->form_ptr->GetFields();
    $unspec = $this->form_ptr->GetAttr('unspecified',$mod->Lang('unspecified'));
    $params = array();
    for($i=0;$i<count($others);$i++)
      {
	$replVal = '';
	if ($others[$i]->DisplayInSubmission())
	  {
	    $replVal = $others[$i]->GetHumanReadableValue();
	    if ($replVal == '')
	      {
		$replVal = $unspec;
	      }
	  }
	$params[$others[$i]->GetName()] = $replVal;
      }

    global $gCms;
    $usertagops =& $gCms->GetUserTagOperations();
    $usertagops->CallUserTag( $this->GetOption('udtname'), $params);

    return array(true,'');        
  }

  function MakeVar($string)
  {
    $maxvarlen = 24;
    $string = strtolower(preg_replace('/\s+/','_',$string));
    $string = strtolower(preg_replace('/\W/','_',$string));
    if (strlen($string) > $maxvarlen)
      {
	$string = substr($string,0,$maxvarlen);
	$pos = strrpos($string,'_');
	if ($pos !== false)
	  {
	    $string = substr($string,0,$pos);
	  }
      }
    return $string;
  }


  function createSampleHeader()
  {
    $mod = &$this->form_ptr->module_ptr;
    $others = &$this->form_ptr->GetFields();
    $fields = array();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {
	    array_push($fields,$others[$i]->GetName());
	  }
      }
    return implode('{$TAB}',$fields);
  }


  function PrePopulateAdminForm($formDescriptor)
  {
    $mod = &$this->form_ptr->module_ptr;
    $main = array();
    $adv = array();

    global $gCms;
    $usertagops =& $gCms->GetUserTagOperations();
    $usertags = $usertagops->ListUserTags();
    $usertaglist = array();
    foreach( $usertags as $key => $value )
      {
	$usertaglist[$value] = $key;
      }
    $main[] = array($mod->Lang('title_udt_name'),
		    $mod->CreateInputDropdown($formDescriptor,
					      'opt_udtname',$usertaglist,-1,
					      $this->GetOption('udtname')));

    return array('main'=>$main,'adv'=>$adv);
  }

  function PostPopulateAdminForm(&$mainArray, &$advArray)
  {
    $this->HiddenDispositionFields($mainArray, $advArray);
  }
}

?>
