<?php
/*
   FormBuilder. Copyright (c) 2005-2007 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2007 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

class fbComputedField extends fbFieldBase 
{

  function fbComputedField(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'ComputedField';
    $this->DisplayInForm = false;
    $this->DisplayInSubmission = true;
    $this->NonRequirableField = true;
    $this->ValidationTypes = array();
    $this->HasLabel = 1;
    $this->NeedsDiv = 0;
    $this->sortable = false;
    $this->IsComputedOnSubmission = true;
  }

    function ComputeOrder()
    {
        return $this->GetOption('order','1');    
    }

    function Compute()
    {
		$mod = &$this->form_ptr->module_ptr;
        $others = &$this->form_ptr->GetFields();

        $mapId = array();

        for($i=0;$i<count($others);$i++)
            {
	        $mapId[$others[$i]->GetId()] = $i;
            }

        $flds = array();
        $procstr = $this->GetOption('value','');
        preg_match_all('/\$fld_(\d+)/', $procstr, $flds);
        
        if ($this->GetOption('string_or_number_eval','numeric') == 'numeric')
            {
            foreach ($flds[1] as $tF)
                {
                if (isset($mapId[$tF]))
                    {
                    $ref = $mapId[$tF];
                    if (is_numeric($others[$ref]->GetValue()))
                        {
                        $procstr = str_replace('$fld_'.$tF,
                            $others[$ref]->GetValue(),$procstr);
                        }
                    else
                        {
                        $procstr = str_replace('$fld_'.$tF,
                            '0',$procstr);
                        }
                    }
                }
            
            $strToEval = "\$this->Value=$procstr;";
            // see if we can trap an error
            // this is all vulnerable to an evil form designer, but
            // not an evil form user. 
            ob_start();
			if (eval('function testcfield'.rand().
			    '() {'.$strToEval.'}') === FALSE)
			    {
			    $this->Value = $mod->Lang('title_bad_function',$procstr);
			    }
			else
			    {
			    eval($strToEval);
			    } 
			ob_end_clean();
            }
        else
            {
            $thisValue = '';
            foreach ($flds[1] as $tF)
                {
                if (isset($mapId[$tF]))
                    {
                    $ref = $mapId[$tF];
                    $this->Value .= $others[$ref]->GetValue().' ';
                    }
                }
            }
                
    }


	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = &$this->form_ptr->module_ptr;
		$processType = array($mod->Lang('title_numeric')=>'numeric',
		    $mod->Lang('title_string')=>'string');
		
		$ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_computation').'</th></tr>';
        $ret .= '<tr><th>'.$mod->Lang('help_php_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
        $odd = false;

        $others = &$this->form_ptr->GetFields();
        for($i=0;$i<count($others);$i++)
            {
            if (! $others[$i]->HasMultipleFormComponents())
                {
	            $ret .= '<tr><td class="'.($odd?'odd':'even').
	                '">$fld_'.$others[$i]->GetId().
	                '</td><td class="'.($odd?'odd':'even').
	                '">' .$others[$i]->GetName() . '</td></tr>';
	            }
	  	    $odd = ! $odd;
	        }
	    $ret .= '<tr><td colspan="2">'.$mod->Lang('operators_help') .
	        '</td></tr></table>';
		
		$main = array(
				array($mod->Lang('title_compute_value'),
            		array($mod->CreateInputText($formDescriptor, 'fbrp_opt_value',$this->GetOption('value',''),35,1024),$ret)),
				array($mod->Lang('title_string_or_number_eval'),
				$mod->CreateInputRadioGroup($formDescriptor, 'fbrp_opt_string_or_number_eval',
				    $processType,
				    $this->GetOption('string_or_number_eval','numeric'))),
				array($mod->Lang('title_order'),
				$mod->CreateInputText($formDescriptor,
				    'fbrp_opt_order',$this->GetOption('order','1'),5,10).
				    $mod->Lang('title_order_help'))
		);
		$adv = array(
		);
		return array('main'=>$main,'adv'=>$adv);
	}


}

?>
