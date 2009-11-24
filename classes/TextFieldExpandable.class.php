<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbTextFieldExpandable extends fbFieldBase {

	function fbTextFieldExpandable(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'TextFieldExpandable';
		$this->DisplayInForm = true;
		$this->HasUserAddOp = true;
		$this->HasUserDeleteOp = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            $mod->Lang('validation_numeric')=>'numeric',
            $mod->Lang('validation_integer')=>'integer',
            $mod->Lang('validation_email_address')=>'email',
            $mod->Lang('validation_regex_match')=>'regex_match',
            $mod->Lang('validation_regex_nomatch')=>'regex_nomatch'
            );
      $this->hasMultipleFormComponents = true;

	}


	function GetFieldInput($id, &$params, $returnid)
	{
	  $mod = $this->form_ptr->module_ptr;
	 //debug_display($this->Value);
	  $js = $this->GetOption('javascript','');
	

     if (! is_array($this->Value))
	      {
	      $vals = 1;
	      }
	  else
	      {
	      $vals = count($this->Value);
	      }
      foreach ($params as $pKey=>$pVal)
         {
         if (substr($pKey,0,9) == 'fbrp_FeX_')
            {
            $pts = explode('_',$pKey);
            if ($pts[2] == $this->Id)
               {
               // expand
               $this->Value[$vals]='';
               $vals++;
               }
            }
         else if (substr($pKey,0,9) == 'fbrp_FeD_')
            {
            $pts = explode('_',$pKey);
            if ($pts[2] == $this->Id)
               {
               // delete row
               if (isset($this->Value[$pts[2]]))
                  {
                  array_splice($this->Value, $pts[2], 1);
                  }
               $vals--;
               }
            }
         }

	  $ret = array();
	  for ($i=0;$i<$vals;$i++)
	    {
	    $thisRow = new stdClass();
        $thisRow->name = '';
        $thisRow->title = '';
	    $thisRow->input = $mod->fbCreateInputText($id, 'fbrp__'.$this->Id.'[]',
				       $this->Value[$i],
            $this->GetOption('length')<25?$this->GetOption('length'):25,
            $this->GetOption('length'),$js.$this->GetCSSIdTag('_'.$i));
        $thisRow->op = $mod->fbCreateInputSubmit($id, 'fbrp_FeD_'.$this->Id.'_'.$i, $this->GetOption('del_button','X'),
			$this->GetCSSIdTag('_del_'.$i));
        array_push($ret, $thisRow);
        }
      $thisRow = new stdClass();
      $thisRow->name = '';
      $thisRow->title = '';
      $thisRow->input = '';
      $thisRow->op = $mod->fbCreateInputSubmit($id, 'fbrp_FeX_'.$this->Id.'_'.$i, $this->GetOption('add_button','+'),
			$this->GetCSSIdTag('_add_'.$i));
      array_push($ret, $thisRow);
      return $ret;
	}

	function StatusInfo()
	{
	  $mod = $this->form_ptr->module_ptr;
	  $ret = $mod->Lang('abbreviation_length',$this->GetOption('length','80'));
		if (strlen($this->ValidationType)>0)
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}

	function GetHumanReadableValue($as_string = true)
	{
		$form = $this->form_ptr;
      if (! is_array($this->Value))
	      {
	      $this->Value = array($this->Value);
	      }
	if ($as_string)
		{
		return join($form->GetAttr('list_delimiter',','),$this->Value);
		}
	else
		{
		return array($ret);
		}
	}



	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_maximum_length'),
			      $mod->CreateInputText($formDescriptor, 
						    'fbrp_opt_length',
			         $this->GetOption('length','80'),25,25)),
			array($mod->Lang('title_add_button_text'),
			      $mod->CreateInputText($formDescriptor,
						    'fbrp_opt_add_button',
			         $this->GetOption('add_button','+'),15,25)),
			array($mod->Lang('title_del_button_text'),
			      $mod->CreateInputText($formDescriptor,
						    'fbrp_opt_del_button',
			         $this->GetOption('del_button','X'),15,25))
		);
		$adv = array(
			array($mod->Lang('title_field_regex'),
			      array($mod->CreateInputText($formDescriptor, 
							  'fbrp_opt_regex',
							  $this->GetOption('regex'),25,255),$mod->Lang('title_regex_help')))	
		);
		return array('main'=>$main,'adv'=>$adv);
	}


	function Validate()
	{
		$this->validated = true;
		$this->validationErrorText = '';
		$mod = $this->form_ptr->module_ptr;
		if (! is_array($this->Value))
		    {
		    $this->Value = array($this->Value);
		    }
		foreach ($this->Value as $thisVal)
		    {
		    switch ($this->ValidationType)
		    {
		  	   case 'none':
		  	       break;
		  	   case 'numeric':
                  if ($thisVal !== false &&
                      ! preg_match("/^([\d\.\,])+$/i", $thisVal))
                      {
                      $this->validated = false;
                      $this->validationErrorText = $mod->Lang('please_enter_a_number',$this->Name);
                      }
		  	       break;
		  	   case 'integer':
                  if ($thisVal !== false &&
                  	! preg_match("/^([\d])+$/i", $thisVal) ||
                      intval($thisVal) != $thisVal)
                    {
                    $this->validated = false;
                    $this->validationErrorText = $mod->Lang('please_enter_an_integer',$this->Name);
                    }
		  	       break;
		  	   case 'email':
                  if ($thisVal !== false &&
                      ! preg_match(($mod->GetPreference('relaxed_email_regex','0')==0?$mod->email_regex:$mod->email_regex_relaxed), $thisVal))
                    {
                    $this->validated = false;
                    $this->validationErrorText = $mod->Lang('please_enter_an_email',$this->Name);
                    }
		  	       break;
		  	   case 'regex_match':
                  if ($thisVal !== false &&
                      ! preg_match($this->GetOption('regex','/.*/'), $thisVal))
                    {
                    $this->validated = false;
                    $this->validationErrorText = $mod->Lang('please_enter_valid',$this->Name);
                    }
		  	   	   break;
		  	   case 'regex_nomatch':
                  if ($thisVal !== false &&
                       preg_match($this->GetOption('regex','/.*/'), $thisVal))
                    {
                    $this->validated = false;
                    $this->validationErrorText = $mod->Lang('please_enter_valid',$this->Name);
                    }
		  	   	   break;
		  }
		if ($this->GetOption('length',0) > 0 && strlen($thisVal) > $this->GetOption('length',0))
			{
			$this->validated = false;
			$this->validationErrorText = $mod->Lang('please_enter_no_longer',$this->GetOption('length',0));
			}
		}
		return array($this->validated, $this->validationErrorText);
	}
}

?>
