<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffFunctionCallInput extends ffInput {

	function ffFunctionCallInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'FunctionCallInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_callback');
		$this->Required = false;
		$this->DisplayInForm = false;
		$this->ValidationTypes = array($this->mod_globals->Lang('validation_none')=>'none');
		$this->specialInput = true;
		if (ffUtilityFunctions::def($params['method']) && ffUtilityFunctions::def($params['method']))
		  {
		  $this->AddOption('method', $params['method'], $params['method']);
          }
		if (ffUtilityFunctions::def($params['cmodule']) && ffUtilityFunctions::def($params['cmodule']))
		  {
		  $this->AddOption('cmodule', $params['cmodule'], $params['cmodule']);
          }
		if (ffUtilityFunctions::def($params['fields']) && ffUtilityFunctions::def($params['fields']))
		  {
		  $this->AddOption('fields', $params['fields'], $params['fields']);
          }
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
        $fnName = $this->GetOptionByKind('method');
        $mName = $this->GetOptionByKind('cmodule');
        if (ffUtilityFunctions::def($fnName[0]->Value) && ffUtilityFunctions::def($mName[0]->Value))
		  {
		  	echo eval('echo '.$mName[0]->Value.'::'.$fnName[0]->Value.'($id, $params, $return_id, $this->cms);');
		  }
        else
          {
          echo '<!-- '.ffUtilityFunctions::def($fnName[0]->Value) .' '. ffUtilityFunctions::def($mName[0]->Value) .' -->';
          }
	}

	function GetValue()
	{
        $evName = $this->GetOptionByKind('fields');
        $retStr = '';
        if (ffUtilityFunctions::def($evName[0]->Value))
            {
            $fieldList = explode(',',($evName[0]->Value.','));
            foreach ($fieldList as $thisField)
                {
                $retStr .= $_POST[trim($thisField)] . ' ';
                }
		  }
		else
		  {
		  	return "no evaluation!";
		  }
		return $retStr;
    }


	function StatusInfo()
	{
        $fnName = $this->GetOptionByKind('method');
        $mName = $this->GetOptionByKind('cmodule');
        $evName = $this->GetOptionByKind('fields');
        $ret = '';
        if (ffUtilityFunctions::def($fnName[0]->Value) && ffUtilityFunctions::def($mName[0]->Value))
		  {
		  	$ret.= $mName[0]->Value.'::'.$fnName[0]->Value.' ';
		  }
		else
		  {
		  	$ret.= $this->mod_globals->Lang('nomethod');
		  }
		if (ffUtilityFunctions::def($evName[0]->Value))
		  {
		  	$ret.= $evName[0]->Value;
		  }
		else
		  {
		  	$ret.= $this->mod_globals->Lang('nofields');
		  }
		  return $ret;
	}


	function RenderAdminForm($formDescriptor)
	{
        $fnName = $this->GetOptionByKind('method');
        $mName = $this->GetOptionByKind('cmodule');
        $evName = $this->GetOptionByKind('fields');
		return array($this->mod_globals->Lang('title_callback_module').':'=>
		  CMSModule::CreateInputText($formDescriptor, 'cmodule',
                ffUtilityFunctions::def($mName[0]->Name)?$this->NerfHTML($mName[0]->Name):'',60,255),
            $this->mod_globals->Lang('title_callback_method').':'=>
		  CMSModule::CreateInputText($formDescriptor, 'method',
                ffUtilityFunctions::def($fnName[0]->Name)?$this->NerfHTML($fnName[0]->Name):'',60,255),
            $this->mod_globals->Lang('title_callback_fields').':'=>
		  CMSModule::CreateInputText($formDescriptor, 'fields',
                ffUtilityFunctions::def($evName[0]->Name)?$this->NerfHTML($evName[0]->Name):'',60,255),
            $this->mod_globals->Lang('title_callback_help').':'=>$this->mod_globals->Lang('title_callback_helplong'));
	}


	function Validate()
	{
		return array(true, '');
	}

}

?>
