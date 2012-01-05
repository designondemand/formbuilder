<?php
#-------------------------------------------------------------------------
# Module: FormBuilder
# Version: 1.0, released 2012
#
# Copyright (c) 2007, Samuel Goldstein <sjg@cmsmodules.com>
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
	#---------------------
	# Attributes
	#---------------------	

	var $field_types;
	var $disp_field_types;
	var $std_field_types;
	var $all_validation_types;
	var $module_ptr; // deprecate
	var $module_id; // deprecate
	var $email_regex;
	var $email_regex_relaxed;
	var $dbHandle; // deprecate

	#---------------------
	# Magic methods
	#---------------------
	
	function __construct()
	{
		parent::__construct();

		$this->module_ptr = &$this; // needed?
		$this->dbHandle =  $this->GetDb(); // needed?
		$this->module_id = ''; // needed?
		$this->email_regex = "/^([\w\d\.\-\_])+\@([\w\d\.\-\_]+)\.(\w+)$/i"; // at wrong place?
		$this->email_regex_relaxed="/^([\w\d\.\-\_])+\@([\w\d\.\-\_])+$/i"; // at wrong place?
		
	}

	#---------------------
	# Module api methods
	#---------------------	
	
	function AllowAutoInstall()
	{
		return FALSE;
	}

	function AllowAutoUpgrade()
	{
		return FALSE;
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
		return '1.0';
	}

	function GetAuthor()
	{
		return 'SjG';
	}

	function GetAuthorEmail()
	{
		return 'sjg@cmsmodules.com';
	}

	function GetAdminDescription($lang = 'en_US')
	{
		return $this->Lang('admindesc');
	}

	function GetChangeLog()
	{
		$fn = dirname(__FILE__).'/changelog.inc';
		return @file_get_contents($fn);
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function VisibleToAdminUser()
	{
		return $this->CheckPermission('Modify Forms');
	}

	function LazyLoadAdmin()
	{
	  return false;
	}
	
	function GetDependencies()
	{
		return array('CMSMailer'=>'1.73');
	}

	function MinimumCMSVersion()
	{
		return '1.9.4.3';
	}

	function InstallPostMessage()
	{
		return $this->Lang('post_install');
	}		
	
	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
	}	
	
	function GetEventDescription ( $eventname )
	{
		return $this->Lang('event_info_'.$eventname );
	}

	function GetEventHelp ( $eventname )
	{
		return $this->Lang('event_help_'.$eventname );
	}	
	
	function AdminStyle()
	{
		$output = '';
		
		$fn = dirname(__FILE__).'/includes/admin.css';
		$output .= @file_get_contents($fn);
		
		return $output;
	}

	function GetHeaderHTML()
	{
		$tmpl = '';

		if( version_compare($GLOBALS['CMS_VERSION'],'1.9') < 0 )
		  {
		    $tmpl .= '<script type="text/javascript" src="'.cmsms()->config['root_url'].'/modules/'.$this->GetName().'/includes/jquery-1.4.2.min.js"></script>';
		  }
		$tmpl .= '<script type="text/javascript" src="'.cmsms()->config['root_url'].'/modules/'.$this->GetName().'/includes/jquery.tablednd.js"></script>';		
		$tmpl .= '<script type="text/javascript" src="'.cmsms()->config['root_url'].'/modules/'.$this->GetName().'/includes/fb_jquery_functions.js"></script>';
		$tmpl .= '<script type="text/javascript" src="'.cmsms()->config['root_url'].'/modules/'.$this->GetName().'/includes/fb_jquery.js"></script>';
		
        return $this->ProcessTemplateFromData($tmpl);
		
	}
	
	function SetParameters()
	{
		$this->RegisterModulePlugin();
		$this->RestrictUnknownParams();
		$this->CreateParameter('fbrp_*','null',$this->Lang('formbuilder_params_general'));
		$this->SetParameterType(CLEAN_REGEXP.'/fbrp_.*/',CLEAN_STRING);
		$this->CreateParameter('form_id','null',$this->Lang('formbuilder_params_form_id'));
		$this->SetParameterType('form_id',CLEAN_INT);
		$this->CreateParameter('form','null',$this->Lang('formbuilder_params_form_name'));
		$this->SetParameterType('form',CLEAN_STRING);
		
		$this->CreateParameter('field_id','null',$this->Lang('formbuilder_params_field_id'));
		$this->SetParameterType('field_id',CLEAN_INT);
		
		$this->CreateParameter('value_*','null',$this->Lang('formbuilder_params_passed_from_tag'));
		$this->SetParameterType(CLEAN_REGEXP.'/value_.*/',CLEAN_STRING);
		
		$this->CreateParameter('response_id','null',$this->Lang('formbuilder_params_response_id'));
		$this->SetParameterType('response_id',CLEAN_INT);
	}

	function DoAction($name,$id,$params,$returnid='')
	{
		$smarty = cmsms()->GetSmarty();

		$smarty->assign_by_ref('mod',$this);
		$smarty->assign('actionid',$id);
		$smarty->assign('returnid',$returnid);		
		
		$this->module_id = $id; // deprecated
		
		if(isset($params['fbrp_message'])) {
		
			$this->ShowMessage($params['fbrp_message']);
		}
		
		parent::DoAction($name,$id,$params,$returnid);

	}

	#---------------------
	# Search module methods
	#--------------------- 
/*	
	function DeleteFromSearchIndex(&$params)
	{
		$aeform = new fbForm($params, true);
		
		// find browsers keyed to this
		$browsers = $aeform->GetFormBrowsersForForm();
		if (count($browsers) < 1)
			{
			return;
			}

		$module =& $this->module_ptr->GetModuleInstance('Search');
		if ($module != FALSE)
		  {
			foreach ($browsers as $thisBrowser)
				{
				$module->DeleteWords( 'FormBrowser', $params['response_id'], 'sub_'.$thisBrowser);	
				}
		  }
	}	
*/	
	#---------------------
	# Module methods
	#--------------------- 	
	
	function initialize()
	{
		$dir=opendir(dirname(__FILE__).'/lib/fields');
		$this->field_types = array();
		while($filespec=readdir($dir))
		{
		  if( !endswith($filespec,'.php') ) continue;
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
		$this->std_field_types = array(
			$this->Lang('field_type_TextField')=>'TextField',
			$this->Lang('field_type_TextAreaField')=>'TextAreaField',
			$this->Lang('field_type_CheckboxField')=>'CheckboxField',
			$this->Lang('field_type_CheckboxGroupField')=>'CheckboxGroupField',
			$this->Lang('field_type_PulldownField')=>'PulldownField',
			$this->Lang('field_type_RadioGroupField')=>'RadioGroupField',
			$this->Lang('field_type_DispositionEmail')=>'DispositionEmail',
			$this->Lang('field_type_DispositionFile')=>'DispositionFile',
			$this->Lang('field_type_PageBreakField')=>'PageBreakField',
			$this->Lang('field_type_StaticTextField')=>'StaticTextField');
		ksort($this->std_field_types);
	}

	// Module API method??? Is this really needed?
	function CheckAccess($permission='Modify Forms')
	{
		$access = $this->CheckPermission($permission);
		if (!$access)  {
			echo "<p class=\"error\">".$this->Lang('you_need_permission',$permission)."</p>";
			return false;
		}
		else return true;
	}

	function GetForms($order_by='name')
	{
		$db = cmsms()->GetDb();
		
		$sql = "SELECT * FROM ".cms_db_prefix().'module_fb_form ORDER BY ?';
		$rs = $db->Execute($sql, array($order_by));
		
		$result = array();
		if($rs && $rs->RecordCount() > 0) {
			$result = $rs->GetArray();
		}
		return $result;
	}

	function GetFormIDFromAlias($form_alias)
	{
		$db = $this->GetDb();
		$sql = 'SELECT form_id from '.cms_db_prefix().'module_fb_form WHERE alias = ?';
		$rs = $db->Execute($sql, array($form_alias));
		if($rs && $rs->RecordCount() > 0)
		{
			$result = $rs->FetchRow();
			return $result['form_id'];
		}
		return -1;
	}

	function GetFormNameFromID($form_id)
	{
		$db = $this->GetDb();
		$sql = 'SELECT name from '.cms_db_prefix().'module_fb_form WHERE form_id = ?';
		$rs = $db->Execute($sql, array($form_id));
		if($rs && $rs->RecordCount() > 0)
		{
			$result = $rs->FetchRow();
		}
		return $result['name'];
	}

	function GetFormByID($form_id, $loadDeep=false)
	{
		$params = array('form_id'=>$form_id);
		return new fbForm($this, $params, $loadDeep);
	}

	// WTF????
	function GetFormByParams(&$params, $loadDeep=false)
	{
		return new fbForm($this, $params, $loadDeep);
	}

	// Change this radically!!!!
	function GetResponse($form_id,$response_id,$field_list=array(), $dateFmt='d F y')
	{
		$names = array();
		$values = array();
		$db = $this->GetDb();
		$fbField = $this->GetFormBrowserField($form_id);
		if ($fbField == false)
			{
			// error handling goes here.
			echo($this->Lang('error_has_no_fb_field'));
			}

		$dbresult = $db->Execute('SELECT * FROM '.cms_db_prefix().
					'module_fb_formbrowser WHERE fbr_id=?', array($response_id));

		$oneset = new stdClass();
		if ($dbresult && $row = $dbresult->FetchRow())
		{
			$oneset->id = $row['fbr_id'];
			$oneset->user_approved = (empty($row['user_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['user_approved']))); 
			$oneset->admin_approved = (empty($row['admin_approved'])?'':date($dateFmt,$db->UnixTimeStamp($row['admin_approved'])));
			$oneset->submitted = date($dateFmt,$db->UnixTimeStamp($row['submitted']));
			$oneset->user_approved_date = (empty($row['user_approved'])?'':$db->UnixTimeStamp($row['user_approved'])); 
			$oneset->admin_approved_date = (empty($row['admin_approved'])?'':$db->UnixTimeStamp($row['admin_approved']));
			$oneset->submitted_date = $db->UnixTimeStamp($row['submitted']);
			$oneset->xml = $row['response'];
			$oneset->fields = array();
			$oneset->names = array();
			$oneset->fieldsbyalias = array();
		}

		$populate_names = true;
		$this->HandleResponseFromXML($fbField, $oneset);
		list($fnames, $aliases, $vals) = $this->ParseResponseXML($oneset->xml);

		foreach ($fnames as $id=>$name)
			{
			if (isset($field_list[$id]) && $field_list[$id] > -1)
				{
				$oneset->values[$field_list[$id]]=$vals[$id];
				$oneset->names[$field_list[$id]]=$fnames[$id];
				}
			if (isset($aliases[$id]))
				{
				$oneset->fieldsbyalias[$aliases[$id]] = $vals[$id];
				}
			}
		return $oneset;
	}

	// WTF?
	function field_sorter_asc($a, $b)
	{
		return strcasecmp($a->fields[$a->sf], $b->fields[$b->sf]);
	}

	// WTF?
	function field_sorter_desc($a, $b)
	{
		return strcasecmp($b->fields[$b->sf], $a->fields[$a->sf]);
	}

	// WTF?
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

	// Still needed? don't think so!
	function ClearFileLock()
	{
		$db = $this->GetDb();
		$sql = "DELETE from ".cms_db_prefix().'module_fb_flock';
		$rs = $db->Execute($sql);
	}

	// Still needed? don't think so!	
	function GetFileLock()
	{
		$db = $this->GetDb();
		$sql = "insert into ".cms_db_prefix()."module_fb_flock (flock_id, flock) values (1,".$db->sysTimeStamp.")";
		$rs = $db->Execute($sql);
		if ($rs)
		{
			return true;
		}
		$sql = "SELECT flock_id FROM ".cms_db_prefix().
				"module_fb_flock where flock + interval 15 second < ".$db->sysTimeStamp;
		$rs = $db->Execute($sql);
		if ($rs && $rs->RecordCount() > 0)
		{
			$this->ClearFileLock();
			return false;
		}
		return false;
	}

	// Still needed? don't think so!
	function ReturnFileLock()
	{
		$this->ClearFileLock();
	}

	// I honesty don't have any idea what this does, but let's leave it there for now.
	function CreatePageDropdown($id,$name,$current='', $addtext='',$markdefault =true)
	{
      global $gCms;
		// we get here (hopefully) when the template is changed
		// in the dropdown.
		$db = $this->GetDb();
		$defaultid = '';
		if( $markdefault )
		{
			$contentops = $gCms->GetContentOperations();
			$defaultid = $contentops->GetDefaultPageID();
		}
		
		// get a list of the pages used by this template
		$mypages = array();

		if ($this->GetPreference('mle_version','0') == '1')
			{
			global $mleblock;
			$q = "SELECT content_id,content_name$mleblock as content_name FROM ".
				cms_db_prefix()."content WHERE type = ? AND active = 1";	
			}
		else
			{
			$q = "SELECT content_id,content_name FROM ".cms_db_prefix().
				"content WHERE type = ? AND active = 1";
			}
		$dbresult = $db->Execute( $q, array('content') );
		while( $row = $dbresult->FetchRow() )
		{
			if( $defaultid != '' && $row['content_id'] == $defaultid )
			{
				// use a star instead of a word here so I don't have to
				// worry about translation stuff
				$mypages[$row['content_name'].' (*)'] = $row['content_id'];
			}
			else
			{
				$mypages[$row['content_name']] = $row['content_id'];
			}
		}
		return $this->CreateInputDropdown($id,'fbrp_'.$name,$mypages,-1,$current,$addtext);
	}

	// Ryan did this? May i ask what this does?
	function SuppressAdminOutput(&$request)
	{
		if (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'],'exportxml') !== false)
		{
			return true;
		}
		elseif (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'],'admin_get_template') !== false)
		{
			return true;
		}
		return false;
	}

	// Move this to Form class, nothing to do with actual module stuff.
	function crypt($string, $dispositionField)
	{
	if ($dispositionField->GetOption('crypt_lib') == 'openssl')
	  {
	   $openssl = $this->GetModuleInstance('OpenSSL');
	   if ($openssl === FALSE)
			{
			return array(false,$this->Lang('title_install_openssl'));
			}
	   $openssl->Reset();
	   if (! $openssl->load_certificate($dispositionField->GetOption('crypt_cert')))
		   {
		   return array(false,$openssl->openssl_errors());
		   }
	   $enc = $openssl->encrypt_to_payload($string);
	   }
	else
	  {
	  $kf = $dispositionField->GetOption('keyfile');
	  if (file_exists($kf))
		 {
		 $key = file_get_contents($kf);
		 }
	  else
		 {
		 $key = $kf;
		 }
	  $enc = $this->fbencrypt($string,$key);
	  }
	return array(true,$enc);
	}

	// FBR stuff?
	function getHashedSortFieldVal($val)
	{
		if (strlen($val) > 4)
			{
			$val = substr($val,0,4). md5(substr($val,4));
			}
		return $val;
	}
   
	// Part of moudle API?
	function GetActiveTab(&$params)
	{
		if (FALSE == empty($params['active_tab'])) {
		
		    return $params['active_tab'];
		} else {
		
			return 'maintab';
		}
	}
  
	// Check if this should go to Form class
	function fbencrypt($string,$key)
	{
		$key = substr(md5($key),0,24);
		$td = mcrypt_module_open ('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
		mcrypt_generic_init ($td, $key, $iv);
		$enc = base64_encode(mcrypt_generic ($td, $string));
		mcrypt_generic_deinit ($td);
		mcrypt_module_close ($td);
		return $enc;
	}

	// Check if this should go to Form class	
	function fbdecrypt($crypt,$key)
	{
		$crypt = base64_decode($crypt);
		$td = mcrypt_module_open ('tripledes', '', 'ecb', '');
		$key = substr(md5($key),0,24);
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
		mcrypt_generic_init ($td, $key, $iv);
		$plain = mdecrypt_generic ($td, $crypt);
		mcrypt_generic_deinit ($td);
		mcrypt_module_close ($td);
		return $plain;
	}

	// Make own class for FB form input types.
	function fbCreateInputText($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $type='text')
	{
		$value = cms_htmlentities($value);
		$id = cms_htmlentities($id);
		$name = cms_htmlentities($name);
		$size = cms_htmlentities($size);
		$maxlength = cms_htmlentities($maxlength);

		$value = str_replace('"', '&quot;', $value);

		$text = '<input type="'.$type.'" name="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
		if ($addttext != '')
			{
			$text .= ' ' . $addttext;
			}
		$text .= " />\n";
		return $text;
	}

	// Make own class for FB form input types.	
	function fbCreateInputSubmit($id, $name, $value='', $addttext='', $image='', $confirmtext='')
	{
	  $id = cms_htmlentities($id);
	  $name = cms_htmlentities($name);
	  $image = cms_htmlentities($image);
		global $gCms;
		$config = $gCms->GetConfig();
		$text = '<input name="'.$id.$name.'" value="'.$value.'" type=';
		if ($image != '')
		{
			$text .= '"image"';
			$img = $config['root_url'] . '/' . $image;
			$text .= ' src="'.$img.'"';
		}
		else
		{
			$text .= '"submit"';
		}
		if ($confirmtext != '' )
		  {
			$text .= ' onclick="return confirm(\''.$confirmtext.'\');"';
		  }
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= ' />';
		return $text . "\n";
	}

} // End of Class

?>