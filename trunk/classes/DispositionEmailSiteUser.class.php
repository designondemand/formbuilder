<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

require_once('DispositionEmailBase.class.php');

class ffDispositionSiteUser extends ffDispositionEmailBase {

    var $nameTypes;

	function ffDispositionSiteUser(&$mod_globals, $formRef, $params=array())
	{
        $this->ffDispositionEmailBase($mod_globals, $formRef, $params);
		$this->Type = 'DispositionSiteUser';
		$this->DisplayType = $this->mod_globals->Lang('field_type_disposition_email_site_user');
		$this->DisplayInForm = true;
		$this->addListParam('group', 'group', 'group', $params);
		$this->addListParam('subject', 'subject', 'subject', $params);
		if (ffUtilityFunctions::def($params['name_type']))
		  {
		  $this->AddOption('name_type', 'name_type', $params['name_type']);
          }
        if (ffUtilityFunctions::def($params['default']))
            {
            $this->AddOption('default','default',$params['default']);
            }
        if (ffUtilityFunctions::def($params['default_user']))
            {
            $this->AddOption('default_user','default_user',$params['default_user']);
            }
        $this->nameTypes = array($this->mod_globals->Lang('title_username')=>'username',
            $this->mod_globals->Lang('title_real_name')=>'fullname',
            $this->mod_globals->Lang('title_both_names')=>'both');

	}

    function StatusInfo()
	{
	 $grp = $this->GetOptionByKind('group');
     $nt = $this->GetOptionByKind('name_type');
	 $ret ='';
	 if (ffUtilityFunctions::def($grp[0]->Value))
	   {
	   	if ($grp[0]->Value == -1)
	   	   {
	   	   	$ret.= $this->mod_globals->Lang('any_user');
	   	   }
	   	else
	   	   {
	   	   	$group = GroupOperations::LoadGroupByID($grp[0]->Value);
	   	   	$ret.= $group->name;
	   	   }
	   }
     else
        {
        $ret.= $this->mod_globals->Lang('not_configured');
        }
     $ret.= ", ".$this->mod_globals->Lang('list').": ";
     $ret.= array_search($nt[0]->Value,$this->nameTypes);
     $ret.= $this->TemplateStatus();
     return $ret;
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
        $grp = $this->GetOptionByKind('group');
        $nt = $this->GetOptionByKind('name_type');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}
        if ($grp[0]->Value != -1)
            {
		    $userlist = UserOperations::LoadUsersInGroup($grp[0]->Value);
            }
        else
            {
            $userlist = UserOperations::LoadUsers();
            }
		$defVals = $this->GetOptionByKind('default');
        $defUserVals = $this->GetOptionByKind('default_user');
        if (ffUtilityFunctions::def($defUserVals[0]->Value))
            {
            $defUser = $defUserVals[0]->Value;
            $opts = array();
            }
        else
            {
            $defUser = -1;
            if (ffUtilityFunctions::def($defVals[0]->Value))
                {
                $opts = array($defVals[0]->Value=>'');
                }
            else
                {
                $opts = array($this->mod_globals->Lang('select_a_user')=>'');
                }
            }
        for($i=0;$i<count($userlist);$i++)
        	{
        	if (! ffUtilityFunctions::def($nt[0]->Value) || $nt[0]->Value == 'username')
        	   {
        	   $opts[$this->NerfHTML($userlist[$i]->username)]=$userlist[$i]->id;
        	   }
        	else if ($nt[0]->Value == 'fullname')
        	   {
        	   $opts[$this->NerfHTML($userlist[$i]->firstname.' '.$userlist[$i]->lastname)]=$userlist[$i]->id;
        	   }
        	else
        	   {
               $opts[$this->NerfHTML($userlist[$i]->username. ' ('.
                    $userlist[$i]->firstname.' '.$userlist[$i]->lastname.')')]=$userlist[$i]->id;
        	   }
        	}
        echo CMSModule::CreateInputDropdown($id, $this->Alias, $opts, -1, ffUtilityFunctions::def($this->Value)?$this->Value:$defUser,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
        
	}


    // Send off those emails
	function DisposeForm($formName, &$config, $results)
	{
		// a touch ugly, since we're rewiring everything to use a base class.
        $user = UserOperations::LoadUserByID($this->Value);
		$this->AddOption('address','address',$user->email);
		return $this->SendForm($formName, $config, $results);
	}

	function RenderAdminForm($formDescriptor)
	{
        $grp = $this->GetOptionByKind('group');
        $opt = $this->GetOptionByKind('subject');
        $nt = $this->GetOptionByKind('name_type');
        $usr = $this->GetOptionByKind('default_user');
        $grouplist = GroupOperations::LoadGroups();
        $userlist = UserOperations::LoadUsers();

		$opts = array($this->mod_globals->Lang('title_no_group_restriction')=>'-1');
        for($i=0;$i<count($grouplist);$i++)
        	{
        	$opts[$this->NerfHTML($grouplist[$i]->name)]=$grouplist[$i]->id;
        	}
		$userOpts = array($this->mod_globals->Lang('title_no_default_user')=>'-1');
        for($i=0;$i<count($userlist);$i++)
        	{
        	$userOpts[$this->NerfHTML($userlist[$i]->username)]=$userlist[$i]->id;
        	}

		$defVals = $this->GetOptionByKind('default');

	   $tmp = array($this->mod_globals->Lang('title_email_subject').':'=>CMSModule::CreateInputText($formDescriptor, 'subject',
				ffUtilityFunctions::def($opt[0]->Value)?$this->NerfHTML($opt[0]->Value):'',25),
        $this->mod_globals->Lang('title_restrict_to_group').':'=>CMSModule::CreateInputDropdown($formDescriptor, 'group', $opts, -1, ffUtilityFunctions::def($grp[0]->Value)?$grp[0]->Value:''),
        $this->mod_globals->Lang('title_names_in_pulldown').':'=>CMSModule::CreateInputRadioGroup($formDescriptor, 'name_type', $this->nameTypes, ffUtilityFunctions::def($nt[0]->Value)?$nt[0]->Value:'username'),
        $this->mod_globals->Lang('title_select_a_user_message').':'=>CMSModule::CreateInputText($formDescriptor, 'default',
				ffUtilityFunctions::def($defVals[0]->Value)?$this->NerfHTML($defVals[0]->Value):$this->mod_globals->Lang('select_a_user'),25),
		$this->mod_globals->Lang('title_default_user').':'=>CMSModule::CreateInputDropdown($formDescriptor, 'default_user',
			$userOpts, -1, ffUtilityFunctions::def($usr[0]->Value)?$usr[0]->Value:''));
	   $tmp2 = $this->RenderAdminFormBase($formDescriptor);
	   foreach ($tmp2 as $key=>$val)
	   		{
	   		$tmp[$key]=$val;
	   		}
	   return $tmp;
	}

	function Validate()
	{
		return array(true, '');
	}
	function AdminValidate()
    {
		return array(true, '');
	}

}

?>
