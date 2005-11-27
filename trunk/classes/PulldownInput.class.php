<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffPulldownInput extends ffInput {

	function ffPulldownInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'PulldownInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_pulldown');
		$this->addListParam('select', 'selectname', 'selectvalue', $params);
        if (ffUtilityFunctions::def($params['default']))
            {
            $this->AddOption('default','default',$params['default']);
            }
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_option_selected')=>'selected'
            );
	}


    function StatusInfo()
	{
		$optVals = $this->GetOptionByKind('select');
		$ret = count($optVals);
		$ret .= " ".$this->mod_globals->Lang('choices');
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	$ret .= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		return $ret;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
		$optVals = $this->GetOptionByKind('select');
		$defVals = $this->GetOptionByKind('default');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}

		if (ffUtilityFunctions::def($defVals[0]->Value))
			{
			$opts = array($defVals[0]->Value=>'');
			}
		else
			{
			$opts = array($this->mod_globals->Lang('select_one')=>'');
			}
        $dispRows = count($optVals);
        for($i=0;$i<$dispRows;$i++)
        	{
        	$opts[$this->NerfHTML($optVals[$i]->Name)]=$optVals[$i]->OptionId;
        	}

        echo CMSModule::CreateInputDropdown($id, $this->Alias, $opts, -1, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

	function RenderAdminForm($formDescriptor)
	{
        $optVals = $this->GetOptionByKind('select');
		$defVals = $this->GetOptionByKind('default');
		$ret =  '<table><tr><th>'.$this->mod_globals->Lang('title_selection_name').'</th><th>'.
            $this->mod_globals->Lang('title_submitted_value').'</th></tr>';
        $dispRows = count($optVals)+5;
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'selectname[]',
				ffUtilityFunctions::def($optVals[$i]->Name)?$this->NerfHTML($optVals[$i]->Name):'',25);
			$ret .= '</td><td>';
			$ret .= CMSModule::CreateInputText($formDescriptor, 'selectvalue[]',
				ffUtilityFunctions::def($optVals[$i]->Value)?$this->NerfHTML($optVals[$i]->Value):'',25);
			$ret .= '</td></tr>';
        	}
        $ret .= '</table>';
		return array($this->mod_globals->Lang('title_select_one_message').':'=>CMSModule::CreateInputText($formDescriptor, 'default',
				ffUtilityFunctions::def($defVals[0]->Value)?$this->NerfHTML($defVals[0]->Value):'Select One',25),
            $this->mod_globals->Lang('title_pulldown_contents').':'=>$ret);
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			$pdOpt = $this->GetOptionById($this->Value);
			return $pdOpt[0]->Value;
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}


	function Validate()
	{
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'selected':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_select_something').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
