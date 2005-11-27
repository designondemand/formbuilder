<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

require_once('DispositionEmailBase.class.php');

class ffDispositionDirector extends ffDispositionEmailBase {

	function ffDispositionDirector(&$mod_globals, $formRef, $params=array())
	{
        $this->ffDispositionEmailBase($mod_globals, $formRef, $params);
		$this->Type = 'DispositionDirector';
		$this->DisplayInForm = true;
		$this->DisplayType = $this->mod_globals->Lang('field_type_disposition_director');
		$this->addListParam('director', 'subject', 'address', $params);
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_option_selected')=>'selected'
            );
        $this->ValidationType = 'selected';
        if (ffUtilityFunctions::def($params['default']))
            {
            $this->AddOption('default','default',$params['default']);
            }
	}


    function StatusInfo()
	{
		$optVals = $this->GetOptionByKind('director');
		$ret = count($optVals);
		$ret.= " ".$this->mod_globals->Lang('choices');
        $ret .= $this->TemplateStatus();
        return $ret;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
		$optVals = $this->GetOptionByKind('director');
		$defVals = $this->GetOptionByKind('default');
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



    // Send off those emails
   	function DisposeForm($formName, &$config, $results)
	{
		// a touch ugly, since we're rewiring everything to use a base class.
		$opt = $this->GetOptionById($this->Value);
		$this->AddOption('address','address',$opt[0]->Value);
		return $this->SendForm($formName, $config, $results);
	}


	function RenderAdminForm($formDescriptor)
	{
        $optVals = $this->GetOptionByKind('director');
		$defVals = $this->GetOptionByKind('default');
        $ret = '<table><tr><th>'.$this->mod_globals->Lang('title_selection_subject').
            '</th><th>'.$this->mod_globals->Lang('title_destination_email').'</th></tr>';
        $dispRows = count($optVals)+4;
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'subject[]',
				ffUtilityFunctions::def($optVals[$i]->Name)?$this->NerfHTML($optVals[$i]->Name):'',25);
			$ret .= '</td><td>';
			$ret .= CMSModule::CreateInputText($formDescriptor, 'address[]',
				ffUtilityFunctions::def($optVals[$i]->Value)?$this->NerfHTML($optVals[$i]->Value):'',25);
			$ret .= '</td></tr>';
        	}
        $ret .= '</table>';
	   $tmp = array($this->mod_globals->Lang('title_select_one_message').
	   		': '=>CMSModule::CreateInputText($formDescriptor, 'default',
			ffUtilityFunctions::def($defVals[0]->Value)?$this->NerfHTML($defVals[0]->Value):$this->mod_globals->Lang('select_one'),25),
	   		$this->mod_globals->Lang('title_director_details').':'=>$ret);
	   $tmp2 = $this->RenderAdminFormBase($formDescriptor);
	   foreach ($tmp2 as $key=>$val)
	   		{
	   		$tmp[$key]=$val;
	   		}
	   return $tmp;
	}

	function AdminValidate()
    {
    	$opt = $this->GetOptionByKind('director');
    	$ret = true;
    	$message = '';
		if ($this->NameExists())
		  {
		  $ret = false;
          $message = $this->mod_globals->Lang('field_name_in_use1').' "'.$this->Name.
            '" '.$this->mod_globals->Lang('field_name_in_use2').'<br/>';
		  }
		if (count($opt) == 0)
			{
			$ret = false;
			$message .= $this->mod_globals->Lang('must_specify_one_destination').'</br>';
			}
        for($i=0;$i<count($opt);$i++)
    	   {
			if (! preg_match("/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i", $opt[$i]->Value))
    	       {
    	       	$ret = false;
                $message .= '"'.$opt[$i]->Value . '" '.$this->mod_globals->Lang('not_valid_email').'<br/>';
    	       }
        }
        return array($ret,$message);
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
