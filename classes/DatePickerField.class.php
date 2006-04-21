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
        $mod = $form_ptr->module_ptr;
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
	}


    function StatusInfo()
	{
		return '';
	}


	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;
       $today = getdate();
       $Days = array();
       for ($i=1;$i<32;$i++)
         {
         	$Days[$i]=$i;
         }
       $Year = array();
       for ($i=$today['year']-10;$i<$today['year']+10;$i++)
         {
         	$Year[$i]=$i;
         }
		if ($this->HasValue())
			{
			$today['mday'] = $this->GetArrayValue(0);
			$today['mon'] = $this->GetArrayValue(1);
			$today['year'] = $this->GetArrayValue(2);			
			}


       return $mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $Days, -1, $today['mday']) .
       			$mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $this->Months, -1, $today['mon']).
       			$mod->CreateInputDropdown($id, '_'.$this->Id.'[]', $Year, -1, $today['year']);
	}

	function GetHumanReadableValue()
	{
		$mod = $this->form_ptr->module_ptr;
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
		$mod = $this->form_ptr->module_ptr;
		$main = array(
			array($mod->Lang('title_date_format'),
            		array($mod->CreateInputText($formDescriptor, 'opt_date_format',
            		$this->GetOption('date_format','j F Y'),25,25),$mod->Lang('help_date_format'))
		));
		return array('main'=>$main,array());
	}

}

?>
