<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffRadioGroupInput extends ffInput {

	function ffRadioGroupInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'RadioGroupInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_radio_group');
		$this->addListParam('radio', 'radioname', 'radiovalue', $params);
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_at_least_one')=>'checked'
            );
	}


    function StatusInfo()
	{
		$ret = count($this->Options);
		$ret .= " ".$this->mod_globals->Lang('choices');
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		return $ret;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		$optVals = $this->GetOptionByKind('radio');
		$opts = array();
        $dispRows = count($optVals);
        for($i=0;$i<$dispRows;$i++)
        	{
        	$opts[$this->NerfHTML($optVals[$i]->Name)]=$optVals[$i]->OptionId;
        	}
        echo CMSModule::CreateInputRadioGroup($id, $this->Alias, $opts, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			$radOpt = $this->GetOptionById($this->Value);
			return $radOpt[0]->Value;
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}



	function RenderAdminForm($formDescriptor)
	{
        $optVals = $this->GetOptionByKind('radio');
        $ret = '<table><tr><th>'.$this->mod_globals->Lang('title_radio_name').
            '</th><th>'.$this->mod_globals->Lang('title_submitted_value').'</th></tr>';
        $dispRows = count($optVals)+5;
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'radioname[]',
				ffUtilityFunctions::def($optVals[$i]->Name)?$this->NerfHTML($optVals[$i]->Name):'',25);
			$ret .= '</td><td>';
			$ret .= CMSModule::CreateInputText($formDescriptor, 'radiovalue[]',
				ffUtilityFunctions::def($optVals[$i]->Value)?$this->NerfHTML($optVals[$i]->Value):'',25);
			$ret .= '</td></tr>';
        	}
        $ret .= '</table>';
		return array($this->mod_globals->Lang('title_radio_group_details').':'=>$ret);
	}


	function Validate()
	{
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'checked':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_check_something').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
