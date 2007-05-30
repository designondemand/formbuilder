<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbFieldsetEnd extends fbFieldBase {

  function fbFieldsetEnd(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'FieldsetEnd';
    $this->DisplayInForm = true;
    $this->DisplayInSubmission = false;
    $this->NonRequirableField = true;
    $this->ValidationTypes = array();    
    $this->HasLabel = 0;
    $this->NeedsDiv = 0;
  }

  function GetFieldInput($id, &$params, $returnid)
  {
    return '</fieldset>';
  }

  function StatusInfo()
  {
    return '';
  }

  function GetHumanReadableValue()
  {
    // there's nothing human readable about a fieldset.
    return '[End Fieldset: '.$this->Value.']';
  }
	
  function PrePopulateAdminForm($formDescriptor)
  {
    $mod = &$this->form_ptr->module_ptr;
    $main = array();
    $adv = array();
    return array('main'=>$main,'adv'=>$adv);
  }

}

?>
