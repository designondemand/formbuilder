<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/

require_once('DispositionEmailBase.class.php');

class fbDispositionEmail extends fbDispositionEmailBase {

	var $addressCount;
	var $addressAdd;

	function fbDispositionEmail(&$form_ptr, &$params)
	{
        $this->fbDispositionEmailBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'DispositionEmail';
		$this->DisplayInForm = false;
		$this->NonRequirableField = true;
		$this->IsDisposition = true;
		$this->HasAddOp = true;
		$this->HasDeleteOp = true;
		$this->ValidationTypes = array(
       		);
    }

	function GetOptionAddButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('add_address');
	}

	function GetOptionDeleteButton()
	{
		$mod = $this->form_ptr->module_ptr;
		return $mod->Lang('delete_address');
	}

	function DoOptionAdd(&$params)
	{
		$this->addressAdd = 1;
	}

	function DoOptionDelete(&$params)
	{
		$delcount = 0;
		foreach ($params as $thisKey=>$thisVal)
			{
			if (substr($thisKey,0,4) == 'del_')
				{
				$this->RemoveOptionElement('destination_address', $thisVal - $delcount);
				$delcount++;
				}
			}
	}



    function StatusInfo()
	{
		$mod = $this->form_ptr->module_ptr;
		$opt = $this->GetOption('destination_address','');

		$ret= $mod->Lang('to').": ";
		if (is_array($opt))
		  {
		  if (count($opt)>1)
		      {
		      $ret.= count($opt);
		      $ret.= " ".$mod->Lang('recipients');
		      }
		  else
		      {
		      $ret.= $opt[0];
		      }
		  }
		else
		  {
          $ret.= $mod->Lang('unspecified');
          }
        $ret.= $this->TemplateStatus();
        return $ret;
	}

	function countAddresses()
	{
			$tmp = &$this->GetOptionRef('destination_address');
			if (is_array($tmp))
				{
	        	$this->addressCount = count($tmp);
	        	}
	        elseif ($tmp !== false)
	        	{
	        	$this->addressCount = 1;
	        	}
	        else
	        	{
	        	$this->addressCount = 0;
	        	}
	}


    // Send off those emails
	function DisposeForm()
	{
		return $this->SendForm();
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;

		$this->countAddresses();
		if ($this->addressAdd > 0)
			{
			$this->addressCount += $this->addressAdd;
			$this->addressAdd = 0;
			}
		$dests = '<table><tr><th>'.$mod->Lang('title_destination_address').'</th><th>'.
			$mod->Lang('title_delete').'</th></tr>';


		for ($i=0;$i<($this->addressCount>1?$this->addressCount:1);$i++)
			{
			$dests .= '<tr><td>'.
            		$mod->CreateInputText($formDescriptor, 'opt_destination_address[]',$this->GetOptionElement('destination_address',$i),25,128).
            		'</td><td>'.
            		$mod->CreateInputCheckbox($formDescriptor, 'del_'.$i, $i,-1).
             		'</td></tr>';
			}
		$dests .= '</table>';
		list($main,$adv) = $this->PrePopulateAdminFormBase($formDescriptor);
		array_push($main,array($mod->Lang('title_destination_address'),$dests));
		return array('main'=>$main,'adv'=>$adv);
	}

	function PostPopulateAdminForm(&$mainArray, &$advArray)
	{
		$this->HiddenDispositionFields($mainArray, $advArray);
	}


	function AdminValidate()
    {
		$mod = $this->form_ptr->module_ptr;
    	$opt = $this->GetOptionRef('destination_address');
    	$ret = true;
    	$message = '';
		if ($opt === false || count($opt) == 0)
			{
			$ret = false;
			$message .= $mod->Lang('must_specify_one_destination').'</br>';
			}
        for($i=0;$i<count($opt);$i++)
    	   {
    	   if (! preg_match("/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i", $opt[$i]))
    	       {
    	       	$ret = false;
                $message .= $mod->Lang('not_valid_email',$opt[$i]) . '<br/>';
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
