<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbDatePickerField extends fbFieldBase {

	var $Months;
	
	function fbDatePickerField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = &$form_ptr->module_ptr;
		$this->Type = 'DatePickerField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            $mod->Lang('validation_none')=>'none',
            );
        $this->Months = array(
			''=>'',
            $mod->Lang('date_january')=>1,
            $mod->Lang('date_february')=>2,
            $mod->Lang('date_march')=>3,
            $mod->Lang('date_april')=>4,
            $mod->Lang('date_may')=>5,
            $mod->Lang('date_june')=>6,
            $mod->Lang('date_july')=>7,
            $mod->Lang('date_august')=>8,
            $mod->Lang('date_september')=>9,
            $mod->Lang('date_october')=>10,
            $mod->Lang('date_november')=>11,
            $mod->Lang('date_december')=>12);
    $this->hasMultipleFormComponents = true;
    $this->labelSubComponents = false;
	}


    function StatusInfo()
	{
      $mod = &$this->form_ptr->module_ptr;
      $today = getdate();
		return $mod->Lang("date_range",array($this->GetOption('start_year',($today['year']-10)) ,
         $this->GetOption('end_year',($today['year']+10)))).
         ($this->GetOption('default_year','-1')!=='-1'?' ('.$this->GetOption('default_year','-1').')':'');
	}


	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = &$this->form_ptr->module_ptr;
       $today = getdate();
       $Days = array(''=>'');
       for ($i=1;$i<32;$i++)
         {
         	$Days[$i]=$i;
         }
       $Year = array(''=>'');
       for ($i=$this->GetOption('start_year',($today['year']-10));$i<$this->GetOption('end_year',($today['year']+10))+1;$i++)
         {
         	$Year[$i]=$i;
         }
		if ($this->HasValue())
			{
			$today['mday'] = $this->GetArrayValue(0);
			$today['mon'] = $this->GetArrayValue(1);
			$today['year'] = $this->GetArrayValue(2);			
			}
		else if ($this->GetOption('default_blank','0') == '1')
			{
			$today['mday']='';
			$today['mon']='';
			$today['year']='';
			}
		else if ($this->GetOption('default_year','-1') != '-1')
		   {
         $today['year'] = $this->GetOption('default_year','-1');
         }

      $ret = array();
      $day = new stdClass();
	  $js = $this->GetOption('javascript','');

      $day->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]', $Days, -1, $today['mday'], 'id="'.$id. '_'.$this->Id.'_1" '.$js);
 		$day->title = $mod->Lang('day');
 		$day->name = '<label for="'.$id.'_'.$this->Id.'_1">'.$mod->Lang('day').'</label>';
 		array_push($ret, $day);

      $mon = new stdClass();
      $mon->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]', $this->Months, -1, $today['mon'], 'id="'.$id. '_'.$this->Id.'_2" '.$js);
 		$mon->title = $mod->Lang('mon');
 		$mon->name = '<label for="'.$id.'_'.$this->Id.'_2">'.$mod->Lang('mon').'</label>';
 		array_push($ret, $mon);

      $yr = new stdClass();
      $yr->input = $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id.'[]', $Year, -1, $today['year'],'id="'.$id. '_'.$this->Id.'_3" '.$js);
      $yr->name = '<label for="'.$id.'_'.$this->Id.'_3">'.$mod->Lang('year').'</label>';
      $yr->title = $mod->Lang('year');
      array_push($ret,$yr);
      return $ret;
	}

   function CompareTo($val)
   {
      $td = 0;
      $od = 0;
      if ($this->HasValue())
			{
			$td = mktime ( 1, 1, 1, $this->GetArrayValue(1),  $this->GetArrayValue(0), $this->GetArrayValue(2) );
			}
		$o = $val->GetValue();
      if ($o->HasValue())
			{
			$od = mktime ( 1, 1, 1, $o->GetArrayValue(1),  $o->GetArrayValue(0), $o->GetArrayValue(2) );
			}
      if ($td == $od)
         {
         return 0;
         }
      return ($td < $od) ? -1 : 1;
   }



	function GetHumanReadableValue($as_string=true)
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->HasValue())
			{
			$theDate = mktime ( 1, 1, 1, $this->GetArrayValue(1),  $this->GetArrayValue(0), $this->GetArrayValue(2) );
			$ret = date($this->GetOption('date_format','j F Y'), $theDate);
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
      $today = getdate();
		$main = array(
			array($mod->Lang('title_date_format'),
            		array($mod->CreateInputText($formDescriptor, 'fbrp_opt_date_format',
            		$this->GetOption('date_format','j F Y'),25,25),$mod->Lang('help_date_format'))
		    ),
		   array($mod->Lang('title_default_blank'),
            		$mod->CreateInputHidden($formDescriptor,'fbrp_opt_default_blank','0').
					$mod->CreateInputCheckbox($formDescriptor, 'fbrp_opt_default_blank',
            		'1',$this->GetOption('default_blank','0')).$mod->Lang('title_default_blank_help')),
		   array($mod->Lang('title_start_year'),
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_start_year',
            		    $this->GetOption('start_year',($today['year']-10)),10,10)),
		   array($mod->Lang('title_end_year'),
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_end_year',
            		    $this->GetOption('end_year',($today['year']+10)),10,10)),
		   array($mod->Lang('title_default_year'),
            		array($mod->CreateInputText($formDescriptor, 'fbrp_opt_default_year',
            		    $this->GetOption('default_year','-1'),10,10),$mod->Lang('title_default_year_help'))
         )
      );
		return array('main'=>$main,array());
	}

  function HasValue()
  {
    if ($this->Value === false)
		{
		return false;
		}
	if (!is_array($this->Value))
		{
		return false;
		}
	if ($this->GetArrayValue(1) == '' ||
	  	$this->GetArrayValue(0) == '' ||
	    $this->GetArrayValue(2) == '')
		{
		return false;
		}
	return true;
  }


}

?>
