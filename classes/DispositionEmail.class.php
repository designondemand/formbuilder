<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

require_once('DispositionEmailBase.class.php');

class ffDispositionEmail extends ffDispositionEmailBase {

	function ffDispositionEmail(&$mod_globals, $formRef, $params=array())
	{
        $this->ffDispositionEmailBase($mod_globals, $formRef, $params);
		$this->Type = 'DispositionEmail';
		$this->DisplayType = $this->mod_globals->Lang('field_type_disposition_email');

 		$this->addListParam('address', 'address', 'address', $params);
	}

    function StatusInfo()
	{
		$opt = $this->GetOptionByKind('address');
		$ret= $this->mod_globals->Lang('to').": ";
		if (ffUtilityFunctions::def($opt[0]->Value))
		  {
		  if (count($opt)>1)
		      {
		      $ret.= count($opt);
		      $ret.= " ".$this->mod_globals->Lang('recipients');
		      }
		  else
		      {
		      $ret.= $opt[0]->Value;
		      }
		  }
		else
		  {
          $ret.= $this->mod_globals->Lang('unspecified');
          }
        $ret.= $this->TemplateStatus();
        return $ret;
	}


    // Send off those emails
	function DisposeForm($formName, &$config, $results)
	{
		return $this->SendForm($formName, $config, $results);
	}


	function RenderAdminForm($formDescriptor)
	{
        $opt = $this->GetOptionByKind('address');
        $dispRows = count($opt)+2;
        $ret = '<table>';
        for($i=0;$i<$dispRows;$i++)
        	{
        	$ret .= '<tr><td>';
        	$ret .= CMSModule::CreateInputText($formDescriptor, 'address[]',
				ffUtilityFunctions::def($opt[$i]->Value)?$this->NerfHTML($opt[$i]->Value):'',25);
			}
	   $ret .= '</table>';
	   $tmp = array($this->mod_globals->Lang('title_email_addresses').':'=>$ret);
	   $tmp2 = $this->RenderAdminFormBase($formDescriptor);
	   foreach ($tmp2 as $key=>$val)
	   		{
	   		$tmp[$key]=$val;
	   		}
	   return $tmp;
	}


	function AdminValidate()
    {
    	$opt = $this->GetOptionByKind('address');
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
		return array(true, '');
	}

}

?>
