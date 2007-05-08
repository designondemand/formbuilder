<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbTimePickerField extends fbFieldBase {

	var $flag12hour;
	
	function fbTimePickerField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type = 'TimePickerField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            );
      $this->flag12hour = array(
		  	$mod->Lang('title_before_noon')=>$mod->Lang('title_before_noon'),
        	$mod->Lang('title_after_noon')=>$mod->Lang('title_after_noon'));
	}


    function StatusInfo()
	{
		return '';
	}


	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
       $now = localtime(time(),true);
       $Mins = array();
       $Hours = array();
       for ($i=0;$i<60;$i++)
       	{
			$mo = sprintf("%02d",$i);
			$Mins[$mo]=$mo;
			}
       if ($this->GetOption('24_hour','0') == '0')
       		{
       		for ($i=1;$i<13;$i++)
       		{
					$mo = sprintf("%02d",$i);
					$Hours[$mo]=$mo;
				}
			if ($this->HasValue())
				{
				$now['tm_hour'] = $this->GetArrayValue(0);
				$now['merid'] = $this->GetArrayValue(2);
				$now['tm_min'] = $this->GetArrayValue(1);
				}
			else
				{
				$now['merid'] = $mod->Lang('title_before_noon');
				if ($now['tm_hour'] > 12)
					{
					$now['tm_hour'] -= 12;
					$now['merid'] = $mod->Lang('title_after_noon');
					}
				elseif ($now['tm_hour'] == 0)
					{
					$now['tm_hour'] = 12;
					}
				}

       		return $mod->CreateInputDropdown($id, '_'.$this->Id.'[]',
       			$Hours, -1, $now['tm_hour']) .
       				$mod->CreateInputDropdown($id, '_'.$this->Id.'[]',
       			$Mins, -1, $now['tm_min']) .
       				$mod->CreateInputDropdown($id, '_'.$this->Id.'[]',
       			$this->flag12hour, -1, $now['merid']);
       		}
       else
       		{
       		for ($i=0;$i<24;$i++)
       		{
					$mo = sprintf("%02d",$i);
					$Hours[$mo]=$mo;
				}

			if ($this->HasValue())
				{
				$now['tm_hour'] = $this->GetArrayValue(0);
				$now['tm_min'] = $this->GetArrayValue(1);
				}
       		return $mod->CreateInputDropdown($id, '_'.$this->Id.'[]',
       			$Hours, -1, $now['tm_hour']) .
       				$mod->CreateInputDropdown($id, '_'.$this->Id.'[]',
       			$Mins, -1, $now['tm_min']);
       		}


	}

	function GetHumanReadableValue()
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->HasValue())
			{
			if ($this->GetOption('24_hour','0') == '0')
				{
				return $this->GetArrayValue(0).':'.
					$this->GetArrayValue(1).' '.
					$this->GetArrayValue(2);
				}
			else
				{
				return $this->GetArrayValue(0).':'.
					$this->GetArrayValue(1);
				}
			}
		else
			{
			return $mod->Lang('unspecified');
			}	
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_24_hour'),
            		$mod->CreateInputCheckbox($formDescriptor, 'opt_24_hour',
            		'1',$this->GetOption('24_hour','0'))));
		return array('main'=>$main,array());
	}

}

?>
