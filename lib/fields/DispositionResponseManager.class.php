<?php
/* 
   FormBuilder. Copyright (c) 2005-2008 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2008 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
 
class fbDispositionResponseManager extends fbFieldBase {


	function __construct(fbForm &$FormInstance, &$params)
	{
		parent::__construct($FormInstance, $params);
		$mod = $form_ptr->module_ptr; //??
		$this->Type = 'DispositionResponseManager';
		$this->IsDisposition = true;
		$this->NonRequirableField = true;
		$this->DisplayInForm = false;
		$this->DisplayInSubmission = false;
		$this->HideLabel = 1;
		$this->NeedsDiv = 0;
		$this->sortable = false;
	}

	function StatusInfo()
	{
		return 'Some info';
	}

	function PrePopulateAdminForm($formDescriptor)
	{
	

	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$mod = $this->form_ptr->module_ptr;
		$this->HiddenDispositionFields($mainArray, $advArray);
	}


	function DisposeForm($returnid)
	{
	
		$form = $this->form_ptr;
		
		foreach($form->Fields as $field) {
		
			if($field->DisplayInSubmission) {
			
				
				echo $field->GetHumanReadableValue();
			}
		
		}
		
		
		return array(true,'');
	}


}

?>
