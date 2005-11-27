<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffStateInput extends ffInput {

	function ffStateInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'StateInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_state');
		$this->addListParam('select', 'selectname', 'selectvalue', $params);
        if (ffUtilityFunctions::def($params['default']))
            {
            $this->AddOption('default','default',$params['default']);
            }
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_option_selected')=>'selected'
            );
        $this->States = array(
        "Alabama"=>"AL","Alaska"=>"AK","Arizona"=>"AZ","Arkansas"=>"AR",
        "California"=>"CA","Colorado"=>"CO","Connecticut"=>"CT","Delaware"=>"DE","Florida"=>"FL",
        "Georgia"=>"GA","Hawaii"=>"HI","Idaho"=>"ID","Illinois"=>"IL","Indiana"=>"IN","Iowa"=>"IA",
        "Kansas"=>"KS","Kentucky"=>"KY","Louisiana"=>"LA","Maine"=>"ME","Maryland"=>"MD","Massachusetts"=>"MA",
        "Michigan"=>"MI","Minnesota"=>"MN","Mississippi"=>"MS","Missouri"=>"MO","Montana"=>"MT","Nebraska"=>"NE",
        "Nevada"=>"NV","New Hampshire"=>"NH","New Jersey"=>"NJ","New Mexico"=>"NM","New York"=>"NY",
        "North Carolina"=>"NC","North Dakota"=>"ND","Ohio"=>"OH","Oklahoma"=>"OK","Oregon"=>"OR",
        "Pennsylvania"=>"PA","Rhode Island"=>"RI","South Carolina"=>"SC","South Dakota"=>"SD",
        "Tennessee"=>"TN","Texas"=>"TX","Utah"=>"UT","Vermont"=>"VT","Virginia"=>"VA",
        "Washington"=>"WA","District of Columbia"=>"DC","West Virginia"=>"WV","Wisconsin"=>"WI","Wyoming"=>"WY");

	}


    function StatusInfo()
	{
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
		return '';
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
		$defVals = $this->GetOptionByKind('default');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}

		if (ffUtilityFunctions::def($defVals[0]->Value))
			{
			$this->States[$defVals[0]->Value]='';
			}
		else
			{
			$this->States[$this->mod_globals->Lang('select_one')]='';
			}
		asort($this->States);
        echo CMSModule::CreateInputDropdown($id, $this->Alias, $this->States, -1, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

	function RenderAdminForm($formDescriptor)
	{
		$defVals = $this->GetOptionByKind('default');
		return array($this->mod_globals->Lang('title_select_one_message').':'=>CMSModule::CreateInputText($formDescriptor, 'default',
				ffUtilityFunctions::def($defVals[0]->Value)?$this->NerfHTML($defVals[0]->Value):'Select One',25));
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			return $this->Value;
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
