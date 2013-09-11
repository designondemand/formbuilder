<?php
/*
 * FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
 * More info at http://dev.cmsmadesimple.org/projects/formbuilder
 *
 * A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
 * This project's homepage is: http://www.cmsmadesimple.org
 */

class fbButtonField extends fbFieldBase
{
	public function __construct(&$form_ptr, &$params)
	{
		parent::__construct($form_ptr, $params);
		$mod = $form_ptr->module_ptr;
		$this->Type = 'ButtonField';
		$this->DisplayInForm = true;
		$this->DisplayInSubmission = false;
		$this->NonRequirableField = true;
		$this->ValidationTypes = array();
		$this->sortable = false;
	}

	public function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
		$js = $this->GetOption('javascript','');

		return '<input type="button" name="'.$id.$this->GetCSSId().'" '.$this->GetCSSIdTag().' value="' .$this->GetOption('text','').'" '.$js.'/>';
	}

	public function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_button_text'),$mod->CreateInputText($formDescriptor,'fbrp_opt_text',$this->GetOption('text',''), 40))
		);
		$adv = array();
		return array('main'=>$main,'adv'=>$adv);
	}
}
?>