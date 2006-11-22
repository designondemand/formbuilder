<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbLinkField extends fbFieldBase {

	function fbLinkField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type =  'LinkField';
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
		$mod = &$this->form_ptr->module_ptr;
		if ($this->Value !== false && is_array($this->Value))
			{
			$val = &$this->Value;
			}
		else
			{
			$val = array("","");
			} 
		$fieldDisp = array();
		$thisBox = new stdClass();
		$thisBox->name = '<label for="'.$id.'_'.$this->Id.'[]">'.$mod->Lang('link_destination').'</label>';
		$thisBox->input = $mod->CreateInputText($id, '_'.$this->Id.'[]', $val[0]);
		array_push($fieldDisp, $thisBox);
		$thisBox = new stdClass();
		$thisBox->name = '<label for="'.$id.'_'.$this->Id.'[]">'.$mod->Lang('link_label').'</label>';
		$thisBox->input = $mod->CreateInputText($id, '_'.$this->Id.'[]', $val[1]);
		array_push($fieldDisp, $thisBox);			
		return $fieldDisp;
		
	}

	function GetHumanReadableValue()
	{
		$mod = &$this->form_ptr->module_ptr;

		if ($this->Value === false || ! is_array($this->Value))
			{
			return '';
			}
		else
			{
			return '<a href="'.$this->Value[0].'">'.$this->Value[1].'</a>';
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
		$main = array(
			);
		$adv = array(
		);
		return array('main'=>$main,'adv'=>$adv);
	}

	function Validate()
	{
		$mod = &$this->form_ptr->module_ptr;
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
