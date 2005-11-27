<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffDatePicker extends ffInput {

	function ffDatePicker(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'DatePicker';
		$this->DisplayType = $this->mod_globals->Lang('field_type_date');
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none'
            );
        if (ffUtilityFunctions::def($params['dateformat']))
            {
            $this->AddOption('dateformat','dateformat',$params['dateformat']);
            }
        $this->Months = array(
            $this->mod_globals->Lang('date_january')=>1,
            $this->mod_globals->Lang('date_february')=>2,
            $this->mod_globals->Lang('date_march')=>3,
            $this->mod_globals->Lang('date_april')=>4,
            $this->mod_globals->Lang('date_may')=>5,
            $this->mod_globals->Lang('date_june')=>6,
            $this->mod_globals->Lang('date_july')=>7,
            $this->mod_globals->Lang('date_august')=>8,
            $this->mod_globals->Lang('date_september')=>9,
            $this->mod_globals->Lang('date_october')=>10,
            $this->mod_globals->Lang('date_november')=>11,
            $this->mod_globals->Lang('date_december')=>12);
	}


    function StatusInfo()
	{
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
        if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
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
		if (isset($params[$this->Alias]) && is_array($params[$this->Alias]))
			{
			$today['mday'] = $params[$this->Alias][0];
			$today['mon'] = $params[$this->Alias][1];
			$today['year'] = $params[$this->Alias][2];			
			}

       echo CMSModule::CreateInputDropdown($id, $this->Alias.'[]', $Days, -1, $today['mday'],$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_d"':'');
       echo CMSModule::CreateInputDropdown($id, $this->Alias.'[]', $this->Months, -1, $today['mon'],$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_m"':'');
       echo CMSModule::CreateInputDropdown($id, $this->Alias.'[]', $Year, -1, $today['year'],$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'_y"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			$format = $this->GetOptionByKind('dateformat');
			$theDate = mktime ( 1, 1, 1, $this->Value[1],  $this->Value[0], $this->Value[2] );
			return date(ffUtilityFunctions::def($format[0]->Value)?$format[0]->Value:'j F Y', $theDate);
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}

	function RenderAdminForm($formDescriptor)
	{
        $format = $this->GetOptionByKind('dateformat');
		return array($this->mod_globals->Lang('title_date_format').':'=>CMSModule::CreateInputText($formDescriptor, 'dateformat',
				ffUtilityFunctions::def($format[0]->Value)?$this->NerfHTML($format[0]->Value):'j F Y',25));
	}



	function Validate()
	{
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
