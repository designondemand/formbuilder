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
    $this->hasMultipleFormComponents = true;
    $this->labelSubComponents = false;
	}


    function StatusInfo()
	{
      $mod = &$this->form_ptr->module_ptr;
		return ($this->GetOption('24_hour','0') == '0'?$mod->Lang('12_hour'):$mod->Lang('24_hour'));
	}


	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
		$js = $this->GetOption('javascript','');
		
       $now = localtime(time(),true);
       $Mins = array();
       $Hours = array();
       $ret = array();
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

            $hr = new stdClass();
            $hr->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]',
       			$Hours, -1, $now['tm_hour'],'id="'.$id.'_'.$this->Id.'_1" '.$js);
       		$hr->title = $mod->Lang('hour');
       		$hr->name = '<label for="'.$id.'_'.$this->Id.'_1">'.$mod->Lang('hour').'</label>';
       		array_push($ret, $hr);
       		
            $min = new stdClass();
            $min->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]',
       			$Mins, -1, $now['tm_min'],'id="'.$id.'_'.$this->Id.'_2" '.$js);
       		$min->title = $mod->Lang('min');
       		$min->name = '<label for="'.$id.'_'.$this->Id.'_2">'.$mod->Lang('min').'</label>';
       		array_push($ret, $min);

            $mer = new stdClass();
            $mer->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]',
       			$this->flag12hour, -1, $now['merid'], 'id="'.$id.'_'.$this->Id.'_3" '.$js);
            $mer->name = '<label for="'.$id.'_'.$this->Id.'_3">'.$mod->Lang('merid').'</label>';
            $mer->title = $mod->Lang('merid');
            array_push($ret,$mer);
            return $ret;
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
            $hr = new stdClass();
            $hr->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]',
       			$Hours, -1, $now['tm_hour'],'id="'.$id.'_'.$this->Id.'_1" '.$js);
       		$hr->title = $mod->Lang('hour');
       		$hr->name = '<label for="'.$id.'_'.$this->Id.'_1">'.$mod->Lang('hour').'</label>';
       		array_push($ret, $hr);
       		
            $min = new stdClass();
            $min->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]',
       			$Mins, -1, $now['tm_min'],'id="'.$id.'_'.$this->Id.'_2" '.$js);
       		$min->title = $mod->Lang('min');
       		$min->name = '<label for="'.$id.'_'.$this->Id.'_2">'.$mod->Lang('min').'</label>';
       		array_push($ret, $min);

            return $ret;
       		}


	}

   function CompareTo($val)
   {
      $tt = 0;
      $ot = 0;
      if ($this->HasValue())
			{
			$tt = $this->GetArrayValue(0) * 60 + $this->GetArrayValue(1);
			}
		$o = $val->GetValue();
      if ($o->HasValue())
			{
			$ot = $o->GetArrayValue(0) * 60 + $o->GetArrayValue(1);
			}
      if ($tt == $ot)
         {
         return 0;
         }
      return ($tt < $ot) ? -1 : 1;
   }

	function GetHumanReadableValue($as_string=true)
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->HasValue())
			{
			if ($this->GetOption('24_hour','0') == '0')
				{
				$ret = $this->GetArrayValue(0).':'.
					$this->GetArrayValue(1).' '.
					$this->GetArrayValue(2);
				}
			else
				{
				$ret = $this->GetArrayValue(0).':'.
					$this->GetArrayValue(1);
				}
			}
		else
			{
			$ret = $mod->Lang('unspecified');
			}
		if ($as_string)
			{
			return $ret;
			}
		else
			{
			return array($ret);
			}
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_24_hour'),
            		$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_24_hour',
            		'1',$this->GetOption('24_hour','0'))));
		return array('main'=>$main,array());
	}

}

?>
