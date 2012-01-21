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

if (!isset($gCms)) exit;

#---------------------
# Database tables
#---------------------

$db = cmsms()->GetDb();
$dict = NewDataDictionary($db);

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_form_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_form_attr');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_form_attr_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_field_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_field_opt');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_field_opt_seq');

/*
$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_flock');
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_val');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_resp_val_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_resp_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_resp_attr');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_resp_attr_seq');

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_ip_log');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_ip_log_seq');
*/

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_fb_formbrowser');
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix().'module_fb_formbrowser_seq');

$db->DropSequence(cms_db_prefix().'module_fb_uniquefield_seq'); // deprecating this => just one field using this

#---------------------
# Permissions
#---------------------

$this->RemovePermission('Modify Forms');

#---------------------
# Events
#---------------------

$this->RemoveEvent('OnFormBuilderFormSubmit');
$this->RemoveEvent('OnFormBuilderFormDisplay');
$this->RemoveEvent('OnFormBuilderFormSubmitError');

#---------------------
# Preferences
#---------------------

$this->RemovePreference();

#---------------------
# Templates
#---------------------

$this->DeleteTemplate();

#---------------------
# Drop default samples 
#---------------------

// Change this to use core methods, if we get DeleteByName into stylesheet ops
$sql = 'DELETE FROM '.cms_db_prefix().'css WHERE css_name = ?';
$db->Execute($sql, array('FormBuilder Default Style'));

#---------------------
# Note to admin log
#---------------------

$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>