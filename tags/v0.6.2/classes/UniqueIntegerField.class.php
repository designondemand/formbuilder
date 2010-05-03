<?php
/* 
   FormBuilder. Copyright (c) 2005-2008 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2008 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbUniqueIntegerField extends fbFieldBase 
{

  function fbUniqueIntegerField(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = $form_ptr->module_ptr;
    $this->Type = 'UniqueIntegerField';
    $this->DisplayInForm = true;
    $this->NonRequirableField = true;
    $this->ValidationTypes = array();
    $this->sortable = false;
  }


  function GetFieldInput($id, &$params, $returnid)
  {
    $mod = $this->form_ptr->module_ptr;
   if ($this->Value !== false)
      {
      $ret = $mod->CreateInputHidden($id, 'fbrp__'.$this->Id,
				   $this->Value);
	  if ($this->GetOption('show_to_user','0') == '1')
		 {
		 $ret .= $this->Value;
		 }
	  }
	else
	   {
	   $db = $mod->dbHandle;
	   $seq = $db->GenID(cms_db_prefix(). 'module_fb_uniquefield_seq');
       $ret = $mod->CreateInputHidden($id, 'fbrp__'.$this->Id,$seq);
	   if ($this->GetOption('show_to_user','0') == '1')
		 {
		 $ret .= $seq;
		 }
      }
	return $ret;
  }

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$main = array(
				array($mod->Lang('title_show_to_user'),
				$mod->CreateInputHidden($formDescriptor,'fbrp_opt_show_to_user','0').
				$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_show_to_user', '1',
				$this->GetOption('show_to_user','0')))
				);
		$adv = array();
		return array('main'=>$main,'adv'=>$adv);
	}


}

?>
