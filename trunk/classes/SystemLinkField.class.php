<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbSystemLinkField extends fbFieldBase {

	function fbSystemLinkField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type =  'SystemLinkField';
		$this->DisplayInForm = true;
		$this->NonRequirableField = true;
		$this->Required = false;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none'
            );
        $this->hasMultipleFormComponents = true;
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$thisLink = new stdClass();
		global $gCms;
		if ($this->GetOption('auto_link','0') == '1')
			{
			$pageinfo = $gCms->variables['pageinfo'];
			$thisLink->input = $this->form_ptr->module_ptr->CreateContentLink($pageinfo->content_id, $pageinfo->content_title);
			$thisLink->name = $pageinfo->content_title;
			$thisLink->title = $pageinfo->content_title;
			}
		else
			{
			$contentops =& $gCms->GetContentOperations();
    		$cobj = $contentops->LoadContentFromId($this->GetOption('target_page','0'));
			$thisLink->input = $this->form_ptr->module_ptr->CreateContentLink($cobj->Id(), $cobj->Name());
			$thisLink->name = $cobj->Name();
			$thisLink->title = $cobj->Name();
			}
		return array($thisLink);
	}

	function GetHumanReadableValue()
	{
		global $gCms;
		if ($this->GetOption('auto_link','0') == '1')
			{
			$pageinfo = $gCms->variables['pageinfo'];
			return $this->form_ptr->module_ptr->CreateContentLink($pageinfo->content_id, $pageinfo->content_title);
			}
		else
			{
			$contentops =& $gCms->GetContentOperations();
    		$cobj = $contentops->LoadContentFromId($this->GetOption('target_page','0'));
			return $this->form_ptr->module_ptr->CreateContentLink($cobj->Id(), $cobj->Name());
			}
	}


	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = $this->form_ptr->module_ptr;
		// remove the "required" field, since this can only be done via validation
		$reqIndex = -1;
		for ($i=0;$i<count($mainArray);$i++)
			{
			if ($mainArray[$i]->title == $mod->Lang('title_field_required'))
				{
				$reqIndex = $i;
				}
			}
		if ($reqIndex != -1)
			{
			array_splice($mainArray, $reqIndex,1);
			}
	}


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
    	global $gCms;
    	$contentops =& $gCms->GetContentOperations();

		$main = array(
		 		 array($mod->Lang('title_link_autopopulate'),$mod->CreateInputCheckbox($formDescriptor, 'opt_auto_link',
            		'1',$this->GetOption('auto_link','0')).$mod->Lang('title_link_autopopulate_help')),
             array($mod->Lang('title_link_to_sitepage'),
				 	$contentops->CreateHierarchyDropdown('',$this->GetOption('target_page',''), $formDescriptor.'opt_target_page'))
		);
		$adv = array(
		);
		return array('main'=>$main,'adv'=>$adv);
	}

}

?>
