<?php
#-------------------------------------------------------------------------
# Module: FormBuilder
# Version: 0.1, released  2006
#
# Copyright (c) 2006, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple tracker at http://dev.cmsmadesimple.org
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
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
	var $dbHandle;
	
	function FormBuilder()
	{
		$this->CMSModule();
        $this->module_ptr = &$this;
        $this->dbHandle = &$this->cms->db;

		require_once 'classes/Form.class.php';
		require_once 'classes/FieldBase.class.php';
		require_once 'classes/Option.class.php';
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
		$this->initialize();
		$db = &$this->cms->db;
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
			value C(255)
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

		$this->CreatePermission('Modify Forms', 'Modify Forms');
//        include 'includes/SampleData.inc';
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
	}

	function Uninstall()
	{
		$this->initialize();
		$db = &$this->cms->db;
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

	function InstallPostMessage()
	{
		return $this->Lang('post_install');
	}

	function Upgrade($oldversion, $newversion)
	{
		$this->initialize();
		$current_version = $oldversion;
		$db = &$this->cms->db;
		$dict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		switch($current_version)
		{
			case "0.1":
		}
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));

	}


	function DoAction($name, $id, $params, $returnid='')
	{
		$this->initialize();
		$this->mod_globals->ModuleInputPrefix = $id;
		switch ($name)
		{
			case 'defaultadmin':
			case 'admin_main_formbuilder':
 				 $this->AdminMain($id, $params, $returnid);
			     break;
			case 'admin_edit_formbuilder_form':
			case 'admin_add_formbuilder_form':
 				 if ($this->CheckAccess())
 				 	{
 				 	$this->AdminAddEditForm($id, $params, $returnid);
			     	}
			     break;
			case 'admin_form_update':
			     if ($this->CheckAccess())
			         {
				    $this->AdminStoreForm($id, $params, $returnid);
				     }
				break;
			case 'admin_edit_formbuilder_field':
			case 'admin_add_formbuilder_field':
			     if ($this->CheckAccess())
			         {
 				     $this->AdminAddEditField($id, $params, $returnid);
 				     }
			     break;
            case 'admin_field_order_update':
                if ($this->CheckAccess())
                    {
                    $this->AdminUpdateFieldOrder($id, $params, $returnid);
                    }
                break;
            case 'admin_field_required_update':
                if ($this->CheckAccess())
                    {
                    $this->AdminUpdateFieldRequired($id, $params, $returnid);
                    }
                break;
            case 'admin_field_update':
                if ($this->CheckAccess())
                    {
                    $this->AdminStoreField($id, $params, $returnid);
                    }
                break;
			case 'admin_delete_formbuilder_field':
			     if ($this->CheckAccess())
			         {
 				     $this->AdminDeleteField($id, $params, $returnid);
 				     }
			     break;
			case 'admin_delete_formbuilder_form':
			     if ($this->CheckAccess())
			         {
 				     $this->AdminDeleteForm($id, $params, $returnid);
 				     }
			     break;
			case 'admin_config_formbuilder_update':
			     if ($this->CheckAccess())
			         {
 				     $this->AdminStoreConfig($id, $params, $returnid);
 				     }
			     break;
		    case 'default':
			case 'submit_form':
			    $this->HandlePublicForm($id, $params, $returnid);
				break;
		}
	}


	function HandlePublicForm($id, &$params, $returnid)
	{
debug_display($params);
        if (! isset($params['form_id']) && isset($params['form']))
            {
            // get the form by name, not ID
            $params['form_id'] = $this->GetFormIDFromAlias($params['form']);
            }
//echo "pre-instantiate form";
//debug_display($params);
        $aeform = new fbForm($this,$params,true);
echo 'pre-render';
debug_display($params);
        echo $aeform->RenderFormHeader();
        $finished = false;
        if (($aeform->GetPageCount() > 1 && $aeform->GetPageNumber() > 0) ||
        	(isset($params['done'])&& $params['done']==1))
            {
//error_log( "validating page :".$aeform->GetPageNumber());            
//        	debug_display($params);
//        	$aeform->DebugDisplay();
        	$res = $aeform->Validate();

            if ($res[0] === false)
                {
                echo $res[1]."\n";
                $aeform->PageBack();
                }
            else if (isset($params['done']) && $params['done']==1)
            	{
            	$finished = true;
            	$results = $aeform->Dispose();
            	}
            }
//error_log('Finished is '.$finished);

		if (! $finished)
			{
        	echo $this->CreateFormStart($id, 'submit_form', $returnid, 'post', 'multipart/form-data');
        	echo $aeform->RenderForm($id, $params, $returnid);
        	echo $this->CreateFormEnd();
        	}
        else
        	{
        	if ($results[0] == true)
        		{
        		echo $this->Lang('successful_formsubmit');
        		}
        	else
        		{
        		echo "Error!: ";
        		foreach ($results[1] as $thisRes)
        			{
        			echo $thisRes . '<br />';
        			}
        		}
        	}
        echo $aeform->RenderFormFooter();
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

	function AdminMain($id, &$params, $returnid, $message='')
	{
		// and a list of all the extant forms.
        $forms = $this->GetForms();
		$num_forms = count($forms);
        
        $this->smarty->assign('tabheaders', $this->StartTabHeaders() .
			$this->SetTabHeader('forms',$this->Lang('forms')) .
			$this->SetTabHeader('config',$this->Lang('configuration')) .
			$this->EndTabHeaders().
			$this->StartTabContent());
		$this->smarty->assign('message',$message);
		$this->smarty->assign('start_formtab',$this->StartTab("forms"));
		$this->smarty->assign('start_configtab',$this->StartTab("config"));
		$this->smarty->assign('end_tab',$this->EndTab());
		$this->smarty->assign('end_tabs',$this->EndTabContent());
		$this->smarty->assign('title_form_name',$this->Lang('title_form_name'));
        $this->smarty->assign('title_form_alias',$this->Lang('title_form_alias'));
        $this->smarty->assign('start_configform',$this->CreateFormStart($id,
			'admin_config_formbuilder_update', $returnid));
        $this->smarty->assign_by_ref('message', $message);

		$formArray = array();
		$currow = "row1";
		foreach ($forms as $thisForm)
    		{
			$oneset = new stdClass();
			$oneset->rowclass = $currow;
			if ($this->CheckPermission('Modify Forms'))
				{
				$oneset->name = $this->CreateLink($id,
				 	'admin_edit_formbuilder_form', '',
            		$thisForm['name'], array('form_id'=>$thisForm['form_id']));
				$oneset->editlink = $this->CreateLink($id,
					'admin_edit_formbuilder_form', '',
					$this->cms->variables['admintheme']->DisplayImage('icons/system/edit.gif','edit','','','systemicon'),
						array('form_id'=>$thisForm['form_id']));
				$oneset->deletelink = $this->CreateLink($id,
					'admin_delete_formbuilder_form', '',
					$this->cms->variables['admintheme']->DisplayImage('icons/system/delete.gif','delete','','','systemicon'),
					array('form_id'=>$thisForm['form_id']),
					$this->Lang('are_you_sure_delete_form',$thisForm['name']));

				}
			else
				{
				$oneset->name=$thisForm['name'];
				$oneset->editlink = '';
				$oneset->deletelink = '';
				}
			$oneset->usage = $thisForm['alias'];
			array_push($formArray,$oneset);
			($currow == "row1"?$currow="row2":$currow="row1");
			}
		if ($this->CheckPermission('Modify Forms'))
			{
			$this->smarty->assign('addlink',$this->CreateLink($id,
				'admin_add_formbuilder_form', '',
				$this->cms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('title_add_new_form'),'',
					'','systemicon'), array()));
			$this->smarty->assign('addform',$this->CreateLink($id,
				'admin_add_formbuilder_form', '', $this->Lang('title_add_new_form'),
				array()));
			$this->smarty->assign('may_config',1);
			}
		else
			{
			$this->smarty->assign('no_permission',
				$this->Lang('lackpermission'));
			}
	
		$this->smarty->assign('title_hide_errors',$this->Lang('title_hide_errors'));		
		$this->smarty->assign('input_hide_errors',$this->CreateInputCheckbox($id, 'hide_errors', 1, $this->GetPreference('hide_errors',1)). $this->Lang('title_hide_errors_long'));		
		$this->smarty->assign('title_show_version',$this->Lang('title_show_version'));		
		$this->smarty->assign('input_show_version',$this->CreateInputCheckbox($id, 'show_version', 1, $this->GetPreference('show_version','0')). $this->Lang('title_show_version_long'));				
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('save')));
		$this->smarty->assign('config_formend',$this->CreateFormEnd());
		
        $this->smarty->assign_by_ref('forms', $formArray);			
        echo $this->ProcessTemplate('AdminMain.tpl');
	}

	function AdminAddEditForm($id, &$params, $returnid, $message='')
	{
		$aeform = new fbForm($this, $params, true);
		echo $aeform->AddEditForm($id, $returnid, $message);
	}

	function AdminAddEditField($id, &$params, $returnid, $message='')
	{
		$aeform = new fbForm($this, $params);
		$aefield = $aeform->NewField($params);
		echo $aeform->AddEditField($id, $aefield, $returnid, $message);
	}

    function AdminStoreField($id, &$params, $returnid)
    {
        $aeform = new fbForm($this, $params);
        $aefield = $aeform->NewField($params);
		$val = $aefield->AdminValidate();
        if ($val[0])
            {
            $aefield->Store(true);
            return $this->AdminAddEditForm($id, $params, $returnid, $params['op']);
            }
        else
        	{
            $aefield->LoadField($params);
			echo $aeform->AddEditField($id, $aefield, $returnid, $val[1]);
        	}

	}

/*
        $emptyArr = array();
        $aeform = new ffForm($this->mod_globals,$emptyArr);
        $aefield = new ffInput($this->mod_globals,$aeform,$params);
        if (ffUtilityFunctions::def($aefield->Type))
            {
            // OK, instantiating a specific Input class.
            $className = $aeform->MakeClassName($aefield->Type, '');
            $aefield = new $className($this->mod_globals, $aeform, $params);
            }
        $val = $aefield->AdminValidate();
        if ($val[0])
            {
            $aefield->Store(true);
		    $url = $this->CreateLink($id, 'admin_edit_formbuilder_form', $returnid, '',
		    	array('form_id'=>$aefield->FormId,'message'=>$this->Lang('field')." ".$params['op']), '', true);
		    $url = str_replace('&amp;', '&', $url);
		    redirect($url);
            }
		else
		    {
            $aefield->LoadField($params);
            $params['message']=$val[1];
		    include "includes/AddEditField.inc";
            }
    }

*/
    function AdminUpdateFieldOrder($id, &$params, $returnid)
    {

        $aeform = new fbForm($this, $params, true);
        $srcIndex = $aeform->GetFieldIndexFromId($params['field_id']);
        if ($params['dir'] == 'up')
            {
            $destIndex = $srcIndex - 1;
            }
        else
            {
            $destIndex = $srcIndex + 1;
            }
        $aeform->SwapFieldsByIndex($srcIndex,$destIndex);
		echo $aeform->AddEditForm($id, $returnid, $this->Lang('field_order_updated'));
    }

    function AdminUpdateFieldRequired($id, &$params, $returnid)
    {
        $aeform = new fbForm($this, $params, true);
        
        $aefield = $aeform->GetFieldById($params['field_id']);
		if ($aefield !== false)
			{
			$aefield->SetRequired($params['active']=='on'?true:false);
			$aefield->Store();
			}
		echo $aeform->AddEditForm($id, $returnid, $this->Lang('field_requirement_updated'));
    }

    function AdminDeleteForm($id, &$params, $returnid)
	{
        $aeform = new fbForm($this, $params, true);
        $aeform->Delete();
		return $this->AdminMain($id, $params, $returnid, $this->Lang('form_deleted'));
    }

    function AdminDeleteField($id, &$params, $returnid)
	{
        $aeform = new fbForm($this, $params, true);
        $aeform->DeleteField($params['field_id']);
		echo $aeform->AddEditForm($id, $returnid, $this->Lang('field_deleted'));
    }


	/*
	DO NOT allow parameters to be used for passing the order_by! It is not escaped before
	database access. 
	*/
	function GetForms($order_by='name')
	{
		$db = &$this->cms->db;
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
		$db = &$this->cms->db;
		$sql = 'SELECT form_id from '.cms_db_prefix().'module_fb_form WHERE alias = ?';
		$rs = $db->Execute($sql, array($form_alias));
		if($rs && $rs->RowCount() > 0)
		{
			$result = $rs->FetchRow();
		}
		return $result['form_id'];
	}


	function AdminStoreForm($id, &$params, $returnid)
	{
        $aeform = new fbForm($this, $params, false);
        $aeform->Store();
        if ($params['submit'] == $this->Lang('save'))
            {
            return $this->AdminMain($id,$params,$returnid,$this->Lang('form')." ".$params['form_op']);
            }
		echo $aeform->AddEditForm($id, $returnid,$this->Lang('form')." ".$params['form_op']);
	}

	function AdminStoreConfig($id, &$params, $returnid)
	{
		$this->SetPreference('hide_errors',isset($params['hide_errors'])?$params['hide_errors']:0);
		$this->SetPreference('show_version',isset($params['show_version'])?$params['show_version']:0);

		$this->AdminMain($id, $params, $returnid, $this->Lang('configuration_updated'));
	}
	
	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
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


}

# vim:ts=4 sw=4 noet
?>
