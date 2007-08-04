<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbHiddenField extends fbFieldBase 
{

  function fbHiddenField(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'HiddenField';
    $this->DisplayInForm = true;
    $this->NonRequirableField = true;
    $this->ValidationTypes = array();
    $this->HasLabel = 0;
    $this->NeedsDiv = 0;
    $this->sortable = false;
  }


  function GetFieldInput($id, &$params, $returnid)
  {
    $mod = &$this->form_ptr->module_ptr;
    if ($this->GetOption('smarty_eval','0') == '1')
      {
      $this->SetSmartyEval(true);
      }
   if ($this->Value !== false)
      {
      return $mod->CreateInputHidden($id, '_'.$this->Id,
				   $this->Value);
		}
	else
	   {
      return $mod->CreateInputHidden($id, '_'.$this->Id,
				   $this->GetOption('value',''));
      }
  }

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$main = array(
				array($mod->Lang('title_value'),
            		$mod->CreateInputText($formDescriptor, 'opt_value',$this->GetOption('value',''),25,128))
		);
		$adv = array(
				array($mod->Lang('title_smarty_eval'),
				$mod->CreateInputCheckbox($formDescriptor, 'opt_smarty_eval',
            		'1',$this->GetOption('smarty_eval','0')))
		);
		return array('main'=>$main,'adv'=>$adv);
	}


}

?>
