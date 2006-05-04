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
	var $dbHandle;
	
	function FormBuilder()
	{
		global $gCms;
		$this->CMSModule();
        $this->module_ptr = &$this;
        $this->dbHandle = &$gCms->GetDb();
		$this->email_regex = "/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i";
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

	function Install()
	{
		global $gCms;
		$db =& $gCms->GetDb();
		$dict = NewDataDictionary($db);
		$flds = "
			form_id I KEY,
			name C(255),
			alias C(255)
		";
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_form', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_form_seq');
//		$db->CreateIndexSQL(cms_db_prefix().'module_fb_form_idx', cms_db_prefix().'module_fb_form', 'alias');
		
		$flds = "
			form_attr_id I KEY,
			form_id I,
			name C(35),
			value X
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_form_attr', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_form_attr_seq');
//		$db->CreateIndexSQL(cms_db_prefix().'module_fb_form_attr_idx', cms_db_prefix().'module_fb_form_attr', 'form_id');

		$flds = "
			field_id I KEY,
			form_id I,
			name C(255),
			type C(50),
			validation_type C(50),
			required I1,
			hide_label I1,
			order_by I
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_field', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_field_seq');

		$flds = "
			option_id I KEY,
			field_id I,
			form_id I,
			name C(255),
			value X
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_field_opt', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_field_opt_seq');

		$flds = "
			flock_id I KEY,
			flock T
		";

		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_flock', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$flds = "
			resp_id I KEY,
			form_id I,
			user_approved T,
			secret_code C(35),
			admin_approved T,
			submitted T
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_resp', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$flds = "
			resp_attr_id I KEY,
			resp_id I,
			name C(35),
			value X
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_resp_attr', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_resp_attr_seq');


		$db->CreateSequence(cms_db_prefix().'module_fb_resp_seq');

		$flds = "
			resp_val_id I KEY,
			resp_id I,
			field_id I,
			value X
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_resp_val', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix().'module_fb_resp_val_seq');


		$this->CreatePermission('Modify Forms', 'Modify Forms');
//        include 'includes/SampleData.inc';
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
	}

	function Uninstall()
	{
        global $gCms;
		$db =& $gCms->GetDb();
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_form_seq');
//DropIndexSQL ($idxname, $tabname = NULL);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form_attr');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_form_attr_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_field_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field_opt');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_field_opt_seq');

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_flock');
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_val');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_attr');
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix().'module_fb_resp_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_val_seq');
		$db->DropSequence(cms_db_prefix().'module_fb_resp_attr_seq');

		$this->RemovePermission('Modify Forms', 'Modify Forms');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

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
      return "\n.module_fb_table {font-size: 10px;}\n.module_fb_area_wide {width: 500px;}\n.module_fb_legend{font-size: 9px; margin: 6px; border: 1px solid black;}.module_fb_area_short {width: 500px; height: 100px;}\n";
    }


	function InstallPostMessage()
	{
		return $this->Lang('post_install');
	}

	function Upgrade($oldversion, $newversion)
	{
        global $gCms;
		$this->initialize();
		$db =& $gCms->GetDb();
		$current_version = $oldversion;
		$dict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		switch($current_version)
		{
			case "0.1":
		}
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));

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

	
	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
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
