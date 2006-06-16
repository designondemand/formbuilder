<?php
#-------------------------------------------------------------------------
# Module: FormBuilder
# Version: 0.1, released  2006
#
# Copyright (c) 2006, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple Forge:
# http://dev.cmsmadesimple.org/projects/formbuilder/
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
class FormBuilder extends CMSModule
{

	var $field_types;
	var $disp_field_types;
	var $all_validation_types;
	var $module_ptr;
	var $email_regex;
	var $email_regex_relaxed;
	var $dbHandle;
	
	function FormBuilder()
	{
		global $gCms;
		$this->CMSModule();
        $this->module_ptr = &$this;
        $this->dbHandle = &$gCms->GetDb();
		$this->email_regex = "/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i";
		$this->email_regex_relaxed="/^([\w\d\.\-\_])+\@([\w\d\.\-\_])+$/i";
		require_once 'classes/Form.class.php';
		require_once 'classes/FieldBase.class.php';
	}


	function initialize()
	{
		$dir=opendir(dirname(__FILE__).'/classes');
   		$this->field_types = array();
   		while($filespec=readdir($dir))
   			{
       		if(strpos($filespec,'Field') === false && strpos($filespec,'Disposition') === false)
       			{
       			continue;
       			}
       		$shortname = substr($filespec,0,strpos($filespec,'.'));
       		if (substr($shortname,-4) == 'Base')
       			{
       			continue;
       			}
       		$this->field_types[$this->Lang('field_type_'.$shortname)] = $shortname;
			}        
		
        foreach ($this->field_types as $tName=>$tType)
        	{
        	if (substr($tType,0,11) == 'Disposition')
        		{
        		$this->disp_field_types[$tName]=$tType;
        		}
        	}
		$this->all_validation_types = array();
		ksort($this->field_types);
	}

	function GetName()
	{
		return 'FormBuilder';
	}
	
	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '0.1';
	}

	function GetAuthor()
	{
		return 'SjG';
	}

	function GetAuthorEmail()
	{
		return 'sjg@cmsmodules.com';
	}

    function GetAdminDescription()
    {
		return $this->Lang('admindesc');
    }

	function GetChangeLog()
	{
		return $this->Lang('changelog');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

    function AdminStyle()
    {
      return "\n.module_fb_table {font-size: 10px;}\n.module_fb_area_wide {width: 500px;}\n.module_fb_legend{font-size: 9px; margin: 6px; border: 1px solid black;}.module_fb_area_short {width: 500px; height: 100px;}\n.module_fb_link {text-decoration: underline;}\n";
    }


	function GetDependencies()
	{
		return array('CMSMailer'=>'1.73');
	}

	// may be too stringent, but better safe than sorry.
	function MinimumCMSVersion()
	{
		return '0.13';
	}

	function InstallPostMessage()
	{
		return $this->Lang('post_install');
	}


	function CheckAccess($permission='Modify Forms')
	{

		$access = $this->CheckPermission($permission);
		if (!$access)  {
			echo "<p class=\"error\">".$this->Lang('you_need_permission',$permission)."</p>";
			return false;
		}
		else return true;
	}

	/*
	DO NOT allow parameters to be used for passing the order_by! It is not escaped before
	database access. If we let ADODB quote it, the SQL is not valid (not that MySQL cares,
	but Postgres does).
	*/
	function GetForms($order_by='name')
	{
        global $gCms;
		$db =& $gCms->GetDb();
		$sql = "SELECT * FROM ".cms_db_prefix().'module_fb_form ORDER BY '.$order_by;
	    $result = array();
	    $rs = $db->Execute($sql);
	    if($rs && $rs->RecordCount() > 0)
	    	{
	        $result = $rs->GetArray();
	    	}
	    return $result;
	}	

    function GetFormIDFromAlias($form_alias)
	{
        global $gCms;
		$db =& $gCms->GetDb();
		$sql = 'SELECT form_id from '.cms_db_prefix().'module_fb_form WHERE alias = ?';
		$rs = $db->Execute($sql, array($form_alias));
		if($rs && $rs->RowCount() > 0)
		{
			$result = $rs->FetchRow();
		}
		return $result['form_id'];
	}

	function GetFormNameFromID($form_id)
	{
        global $gCms;
		$db =& $gCms->GetDb();
		$sql = 'SELECT name from '.cms_db_prefix().'module_fb_form WHERE form_id = ?';
		$rs = $db->Execute($sql, array($form_id));
		if($rs && $rs->RowCount() > 0)
		{
			$result = $rs->FetchRow();
		}
		return $result['name'];
	}

	function GetFormByID($form_id, $loadDeep=false)
	{
			$params = array('form_id'=>$form_id);
		    $form = new fbForm($this, $params, $loadDeep);
		    return $form;
	}

	function GetFormByParams(&$params, $loadDeep=false)
	{
		    $form = new fbForm($this, $params, $loadDeep);
		    return $form;
	}

	
	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
	}


	function GetResponse($form_id,$response_id,$field_list=array(), $dateFmt='d F y')
	{
		$names = array();
		$values = array();
		global $gCms;
		$db =& $gCms->GetDb();

       	$dbresult = $db->Execute('SELECT * FROM '.cms_db_prefix().
        			'module_fb_resp WHERE resp_id=?', array($response_id));

		$oneset = new stdClass();
		if ($dbresult && $row = $dbresult->FetchRow())
			{			
			$oneset->id = $row['resp_id'];
			$oneset->user_approved = (empty($row['user_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['user_approved']))); 
 			$oneset->admin_approved = (empty($row['admin_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['admin_approved']))); 
			$oneset->submitted = date($dateFmt,$db->UnixTimeStamp($row['submitted']));
			$oneset->fields = array();
			$oneset->names = array();
		    }

		$paramSet = array('form_id'=>$form_id, 'response_id'=>$response_id);
		$fm = $this->GetFormByParams($paramSet, true);
		$fields = $fm->GetFields();
		for($j=0;$j<count($fields);$j++)
			{
			if ($fields[$j]->DisplayInSubmission())
				{
				if (isset($field_list[$fields[$j]->GetId()])
					&& $field_list[$fields[$j]->GetId()] > -1)
					{
						$oneset->names[$field_list[$fields[$j]->GetId()]] = $fields[$j]->GetName();
                		$oneset->values[$field_list[$fields[$j]->GetId()]] = $fields[$j]->GetHumanReadableValue();
                	}
                }
        	}
		return $oneset;	
	}

	function GetResponses($form_id, $start_point, $number, $admin_approved=false, $user_approved=false, $field_list=array(), $dateFmt='d F y')
	{
		global $gCms;
		$db =& $gCms->GetDb();
		$names = array();
		$values = array();
		$sql = 'SELECT * FROM '.cms_db_prefix().
        			'module_fb_resp WHERE form_id=?';
        if ($user_approved)
        	{
        	 $sql .= 'and user_approved is not null';
        	 }
        if ($admin_approved)
        	{
        	$sql .= ' and admin_approved is not null';
        	}

       	$dbresult = $db->SelectLimit($sql, $number, $start_point, array($form_id));

		while ($dbresult && $row = $dbresult->FetchRow())
			{
			$oneset = new stdClass();
			$oneset->id = $row['resp_id'];
			$oneset->user_approved = (empty($row['user_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['user_approved']))); 
 			$oneset->admin_approved = (empty($row['admin_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['admin_approved']))); 
			$oneset->submitted = date($dateFmt,$db->UnixTimeStamp($row['submitted']));
			$oneset->fields = array();
		    array_push($values,$oneset);
		    }
		$populate_names = true;
		for($i=0;$i<count($values);$i++)
			{
			$paramSet = array('form_id'=>$form_id, 'response_id'=>$values[$i]->id);
			$fm = $this->GetFormByParams($paramSet, true);
			$fields = $fm->GetFields();
			for($j=0;$j<count($fields);$j++)
				{
				if ($fields[$j]->DisplayInSubmission())
					{
					if (isset($field_list[$fields[$j]->GetId()])
						&& $field_list[$fields[$j]->GetId()] > -1)
						{
						if ($populate_names)
							{
							$names[$field_list[$fields[$j]->GetId()]] = $fields[$j]->GetName();
							}
                		$values[$i]->fields[$field_list[$fields[$j]->GetId()]] = $fields[$j]->GetHumanReadableValue();
                		}
                	}
        		}
        	$populate_names = false;
			}
		return array($names, $values);
	}

	// For a given form, returns an array of response objects
	function ListResponses($form_id, $sort_order='submitted')
	{
		global $gCms;
		$db =& $gCms->GetDb();
		$ret = array();
		$sql = 'SELECT * FROM '.cms_db_prefix().
        			'module_fb_resp WHERE form_id=? ORDER BY ?';
       	$dbresult = $db->Execute($query, array($form_id,$sort_order));
		while ($dbresult && $row = $dbresult->FetchRow())
			{
			$oneset = new stdClass();
			$oneset->id = $result['resp_id'];
			$oneset->user_approved = $db->UnixTimeStamp($result['user_approved']); 
 			$oneset->admin_approved = $db->UnixTimeStamp($result['admin_approved']); 
			$oneset->submitted = $db->UnixTimeStamp($result['submitted']); 
		    array_push($ret,$oneset);
		    }
		return $ret;
	}

    function def(&$var)
    {
    	if (!isset($var))
    	   {
    	   	return false;
    	   }
    	else if (is_null($var))
    	   {
    	   	return false;
    	   }
    	else if (!is_array($var) && empty($var))
    	   {
    	   	return false;
    	   }
    	else if (is_array($var) && count($var) == 0)
    	   {
    	   	return false;
    	   }
    	return true;
    }

    function ClearFileLock()
    {
		global $gCms;
		$db =& $gCms->GetDb();
		$sql = "DELETE from ".cms_db_prefix().'module_fb_flock';
		$rs = $db->Execute($sql);
    }


    function GetFileLock()
    {
		global $gCms;
		$db =& $gCms->GetDb();
		$sql = "insert into ".cms_db_prefix()."module_fb_flock (flock_id, flock) values (1,".$db->sysTimeStamp.")";
		$rs = $db->Execute($sql);
        if ($rs)
        	{
        	return true;
        	}
        $sql = "select flock_id from ".cms_db_prefix().
        	"module_fb_flock where flock + interval 15 second < ".$db->sysTimeStamp;
		$rs = $db->Execute($sql);
        if ($rs && $rs->RowCount() > 0)
        	{
        	$this->ClearFileLock();
        	return false;
        	}        	 
		return false;
    }

    function ReturnFileLock()
    {
		$this->ClearFileLock();
    }




}

# vim:ts=4 sw=4 noet
?>
