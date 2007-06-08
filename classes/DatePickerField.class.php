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
       $Days = array();
       for ($i=1;$i<32;$i++)
         {
         	$Days[$i]=$i;
         }
       $Year = array();
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
		else if ($this->GetOption('default_year','-1') != '-1')
		   {
         $today['year'] = $this->GetOption('default_year','-1');
         }

      $ret = array();
      $day = new stdClass();
      $day->input = $mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $Days, -1, $today['mday'], 'id="'.$id. '_'.$this->Id.'_1"');
 		$day->title = $mod->Lang('day');
 		$day->name = '<label for="'.$id.'_'.$this->Id.'_1">'.$mod->Lang('day').'</label>';
 		array_push($ret, $day);

      $mon = new stdClass();
      $mon->input = $mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $this->Months, -1, $today['mon'], 'id="'.$id. '_'.$this->Id.'_2"');
 		$mon->title = $mod->Lang('mon');
 		$mon->name = '<label for="'.$id.'_'.$this->Id.'_2">'.$mod->Lang('mon').'</label>';
 		array_push($ret, $mon);

      $yr = new stdClass();
      $yr->input = $mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $Year, -1, $today['year'],'id="'.$id. '_'.$this->Id.'_3"');
      $yr->name = '<label for="'.$id.'_'.$this->Id.'_3">'.$mod->Lang('year').'</label>';
      $yr->title = $mod->Lang('year');
      array_push($ret,$yr);
      return $ret;
	}

	function GetHumanReadableValue()
	{
		$mod = &$this->form_ptr->module_ptr;
		if ($this->HasValue())
			{
			$theDate = mktime ( 1, 1, 1, $this->GetArrayValue(1),  $this->GetArrayValue(0), $this->GetArrayValue(2) );
			return date($this->GetOption('dateformat','j F Y'), $theDate);
			}
		else
			{
			return $mod->Lang('unspecified');
			}	
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
      $today = getdate();
		$main = array(
			array($mod->Lang('title_date_format'),
            		array($mod->CreateInputText($formDescriptor, 'opt_date_format',
            		$this->GetOption('date_format','j F Y'),25,25),$mod->Lang('help_date_format'))
		    ),
		   array($mod->Lang('title_start_year'),
            		$mod->CreateInputText($formDescriptor, 'opt_start_year',
            		    $this->GetOption('start_year',($today['year']-10)),10,10)),
		   array($mod->Lang('title_end_year'),
            		$mod->CreateInputText($formDescriptor, 'opt_end_year',
            		    $this->GetOption('end_year',($today['year']+10)),10,10)),
		   array($mod->Lang('title_default_year'),
            		array($mod->CreateInputText($formDescriptor, 'opt_default_year',
            		    $this->GetOption('default_year','-1'),10,10),$mod->Lang('title_default_year_help'))
         )
      );
		return array('main'=>$main,array());
	}

}

?>
