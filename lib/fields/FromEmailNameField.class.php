<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbFromEmailNameField extends fbFieldBase {

	function __construct(fbForm &$FormInstance, &$params)
	{
		parent::__construct($FormInstance, $params);
		$this->Type = 'FromEmailNameField';
		$this->DisplayInForm = true;
		$this->modifiesOtherFields = true;
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->getModuleInstance();
		$js = $this->GetOption('javascript','');
		
		return $mod->fbCreateInputText($id, 'fbrp__'.$this->Id, htmlspecialchars($this->Value, ENT_QUOTES), 25,128,$js.$this->GetCSSIdTag());
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->getModuleInstance();
		$main = array();
		$adv = array();
		$hopts = array($mod->Lang('option_from')=>'f',$mod->Lang('option_reply')=>'r',$mod->Lang('option_both')=>'b');
		
		array_push($main,array($mod->Lang('title_headers_to_modify'), $mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_headers_to_modify', $hopts, -1, $this->GetOption('headers_to_modify','b'))));

		return array('main'=>$main,'adv'=>$adv);
	}

	function ModifyOtherFields()
	{
		$mod = $this->getModuleInstance();
		$form = $this->getFormInstance();
		$others = $form->GetFields();
		$htm = $this->GetOption('headers_to_modify','b');

		if ($this->Value !== false)
			{		
			for($i=0;$i<count($others);$i++)
				{
				$replVal = '';
				if ($others[$i]->IsDisposition()
               && is_subclass_of($others[$i],'fbDispositionEmailBase'))
					{
					if ($htm == 'f' || $htm == 'b')
                  {
					    $others[$i]->SetOption('email_from_name',$this->Value);
					   }
               if ($htm == 'r' || $htm == 'b')
                  {
					    $others[$i]->SetOption('email_reply_to_name',$this->Value);
                  }
					}
				}
			}
	}

}

?>
