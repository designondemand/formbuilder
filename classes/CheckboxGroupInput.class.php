<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffCheckboxGroupInput extends ffInput {

	function ffCheckboxGroupInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'CheckboxGroupInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_checkbox_group');
		$this->addListParam('checkbox', 'checkboxname', 'checkboxvalue', $params);
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_at_least_one')=>'checked'
            );
	}


    function StatusInfo()
	{
		$ret= count($this->Options);
		$ret.= " ".$this->mod_globals->Lang('boxes');
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	$ret.= ", ".array_search($this->ValidationType,$this->ValidationTypes);
		  }
		 return $ret;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
		$optVals = $this->GetOptionByKind('checkbox');
        $dispRows = count($optVals);
        if ($this->mod_globals->UseCSS)
        	{
        	echo "<div";
        	}
        else
        	{
        	echo "<table";
        	}
        if (strlen($this->CSSClass)>0)
        	{
        	echo " class=\"".$this->CSSClass."\"";
        	}
        echo ">";

        for($i=0;$i<$dispRows;$i++)
        	{
        	if ($i%2 == 0)
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "<div class=\"left\">";
        			}
        		else
        			{
        			echo "<tr><td class=\"left\">";
        			}
        		}
        	if (is_array($this->Value))
        	   {
        	  
                $index = array_search($optVals[$i]->OptionId, $this->Value);
        	   	if ($this->Value[$index] == $optVals[$i]->OptionId)
        	   	   {
                    echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, $optVals[$i]->OptionId, $this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   	   }
        	   	else
        	   	   {
        	   	   echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, '',$this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   	   }
        	   }
        	else
        	   {
        	   echo CMSModule::CreateInputCheckbox($id, $this->Alias.'[]', $optVals[$i]->OptionId, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.$i.'"':'');
        	   }
          echo $optVals[$i]->Name;
        	if ($i%2 == 0)
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "</div><div class=\"right\">";
        			}
        		else
        			{
        			echo "</td><td class=\"right\">";
        			}
        		}
        	else
        		{
        		if ($this->mod_globals->UseCSS)
        			{
        			echo "</div>\n";
        			}
        		else
        			{
        			echo "</td></tr>\n";
        			}
        		}
          }
          if ($i%2 != 0)
          {
          	if ($this->mod_globals->UseCSS)
          		{
          		echo "</div>\n";
          		}
          	else
          		{
          		echo "</td></tr>\n";
          		}
          }
          if ($this->mod_globals->UseCSS)
          	{
          	echo "</div>";
          	}
          else
          	{
          	echo "</table>";
          	}
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			if (is_array($this->Value))
				{
				$val = '';
				foreach($this->Value as $tv)
					{
					$boxOpt = $this->GetOptionById($tv);
					$val .= $boxOpt[0]->Value.", ";
					}
				return rtrim($val,', ');
				}
			else
				{
				$boxOpt = $this->GetOptionById($this->Value);
				return $boxOpt[0]->Value;
				}
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}



	function RenderAdminForm($formDescriptor)
	{
        $optVals = $this->GetOptionByKind('checkbox');
        $ret = '<table><tr><th>'.$this->mod_globals->Lang('title_checkbox_name').
            '</th><th>'.$this->mod_globals->Lang('title_submitted_value').'</th></tr>';
        $dispRows = count($optVals)+5;
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'checkboxname[]',
				ffUtilityFunctions::def($optVals[$i]->Name)?$this->NerfHTML($optVals[$i]->Name):'',25);
			$ret .= '</td><td>';
			$ret .= CMSModule::CreateInputText($formDescriptor, 'checkboxvalue[]',
				ffUtilityFunctions::def($optVals[$i]->Value)?$this->NerfHTML($optVals[$i]->Value):'',25);
			$ret .= '</td></tr>';
        	}
        $ret .= '</table>';
		return array($this->mod_globals->Lang('title_checkbox_details').':'=>$ret);
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
