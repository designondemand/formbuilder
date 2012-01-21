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

/************************************************************************
 NOTE: fbForm instance not usable in this method, due FB not installed!
************************************************************************/

if (!isset($gCms)) exit;

#---------------------
# Database tables
#---------------------

$db = cmsms()->GetDb();
$config = cmsms()->GetConfig();

$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

# Form table
$flds = "
	form_id I KEY,
	name C(255),
	alias C(255)
";

$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_form', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// deprecate this => use auto_increament
$db->CreateSequence(cms_db_prefix().'module_fb_form_seq');
$db->Execute('create index '.cms_db_prefix().'module_fb_form_idx on '.cms_db_prefix().'module_fb_form (alias)');

# Form attributes table
$flds = "
	form_attr_id I KEY,
	form_id I,
	name C(35),
	value X
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_form_attr', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// deprecate this => use auto_increament
$db->CreateSequence(cms_db_prefix().'module_fb_form_attr_seq');
$db->Execute('create index '.cms_db_prefix().'module_fb_form_attr_idx on '.cms_db_prefix().'module_fb_form_attr (form_id)');

# Field table
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

// deprecate this => use auto_increament
$db->CreateSequence(cms_db_prefix().'module_fb_field_seq');
$db->Execute('create index '.cms_db_prefix().'module_fb_field_idx on '.cms_db_prefix().'module_fb_field (form_id)');

# Field options table
$flds = "
	option_id I KEY,
	field_id I,
	form_id I,
	name C(255),
	value X
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_field_opt', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// deprecate this => use auto_increament
$db->CreateSequence(cms_db_prefix().'module_fb_field_opt_seq');
$db->Execute('create index '.cms_db_prefix().'module_fb_field_opt_idx on '.cms_db_prefix().'module_fb_field_opt (field_id,form_id)');

//Let's test if we can deprecate this already
/*
$flds = "
	flock_id I KEY,
	flock T
";

$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_flock', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	resp_id I KEY,
	form_id I,
	feuser_id I,
	user_approved ".CMS_ADODB_DT.",
	secret_code C(35),
	admin_approved ".CMS_ADODB_DT.",
	submitted ".CMS_ADODB_DT;
	
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

$flds = "
	sent_id I KEY,
	src_ip C(16),
	sent_time ".CMS_ADODB_DT;
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_ip_log', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix().'module_fb_ip_log_seq');

*/

$flds = "
		fbr_id I KEY,
		form_id I,
		index_key_1 C(80),
		index_key_2 C(80),
		index_key_3 C(80),
		index_key_4 C(80),
		index_key_5 C(80),
		feuid I,
		response XL,
		user_approved ".CMS_ADODB_DT.",
		secret_code C(35),
		admin_approved ".CMS_ADODB_DT.",
		submitted ".CMS_ADODB_DT;
		
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_formbrowser', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix().'module_fb_formbrowser_seq');
$db->CreateSequence(cms_db_prefix().'module_fb_uniquefield_seq'); // deprecating this => just one field using this

#---------------------
# Permissions
#---------------------

$this->CreatePermission('Modify Forms', 'Modify Forms');

#---------------------
# Events
#---------------------

$this->CreateEvent( 'OnFormBuilderFormSubmit' );
$this->CreateEvent( 'OnFormBuilderFormDisplay' );
$this->CreateEvent( 'OnFormBuilderFormSubmitError' );

#---------------------
# Set default samples
#---------------------

# Insert default CSS 
$fn = cms_join_path($config['root_path'], 'lib', 'classes', 'class.stylesheet.inc.php'); // this type isn't on autoloader yet :(
$css = @file_get_contents(cms_join_path(dirname(__FILE__), 'samples','default.css'));
if(file_exists($fn)) {

	require_once($fn);
	
	$obj = new Stylesheet();
	$obj->name = 'FormBuilder Default Style';
	$obj->value = $css;
	$obj->media_type = 'screen';

	$obj->Save();
}

# Insert sample forms
// Figure out how to handle this stuff.
/*
$path = cms_join_path(dirname(__FILE__),'samples');
$dir=opendir($path);
while ($filespec=readdir($dir))
	{
	$params = array();
	$aeform = '';
	if (preg_match('/.xml$/',$filespec) > 0)
		{
		$params['fbrp_xml_file'] = cms_join_path($path,$filespec);
		$aeform = new fbForm($params, true);
		$res = $aeform->ImportXML($params);
		}
	}
*/
	
#---------------------
# Note to admin log
#---------------------	
	
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
?>
