<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyrigth (c)2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbTextAreaField extends fbFieldBase {

	function fbTextAreaField(&$form_ptr, &$params)
	{
      $this->fbFieldBase($form_ptr, $params);
		$mod = &$form_ptr->module_ptr;
		$this->Type = 'TextAreaField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            );

	}

	function GetFieldInput($id, &$params, $returnid)
	{            
	   $mod = &$this->form_ptr->module_ptr;
       return $mod->CreateTextArea($this->GetOption('wysiwyg','0') == '1'?true:false,
				   $id, $this->Value,
				   'fbrp__'.$this->Id,'',$id.'fbrp__'.$this->Id,
               '','',
               $this->GetOption('cols','80'),
               $this->GetOption('rows','15')
               );
	//CreateTextArea($enablewysiwyg, $id, $text, $name, $classname='', $htmlid='', $encoding='', $stylesheet='', $width='80', $cols='15',$forcewysiwyg="",$wantedsyntax="")

   }


	function StatusInfo()
	{
	   $mod = &$this->form_ptr->module_ptr;
		$ret = '';
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret = array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 if ($this->GetOption('wysiwyg','0') == '1')
		 	{
		 	$ret .= ' wysiwyg';
		 	}
		 else
		 	{
		 	$ret .= ' non-wysiwyg';
		 	}
		 $ret .=  ', '.$mod->Lang('rows',$this->GetOption('rows','15'));
		 $ret .=  ', '.$mod->Lang('cols',$this->GetOption('cols','80'));
		 return $ret;
	}


	function PrePopulateAdminForm($formDescriptor)
	{
	   $mod = &$this->form_ptr->module_ptr;
		return array(
			'main'=>array(
         array($mod->Lang('title_use_wysiwyg'),
            		$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_wysiwyg',
            		'1',$this->GetOption('wysiwyg','0'))),
			array($mod->Lang('title_textarea_rows'),
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_rows',
            		$this->GetOption('rows','15'),5,5)),
			array($mod->Lang('title_textarea_cols'),
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_cols',
            		$this->GetOption('cols','80'),5,5))
               )
         );
	}

}

?>
