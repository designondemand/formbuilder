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
if (! $this->CheckAccess()) exit;

$this->SetPreference('hide_errors',isset($params['fbrp_hide_errors'])?$params['fbrp_hide_errors']:0);
$this->SetPreference('show_version',isset($params['fbrp_show_version'])?$params['fbrp_show_version']:0);
$this->SetPreference('relaxed_email_regex',isset($params['fbrp_relaxed_email_regex'])?$params['fbrp_relaxed_email_regex']:0);
$this->SetPreference('mle_version',isset($params['fbrp_mle_version'])?$params['fbrp_mle_version']:0);
$this->SetPreference('require_fieldnames',isset($params['fbrp_require_fieldnames'])?$params['fbrp_require_fieldnames']:0);
$this->SetPreference('unique_fieldnames',isset($params['fbrp_unique_fieldnames'])?$params['fbrp_unique_fieldnames']:0);
$this->SetPreference('enable_fastadd',isset($params['fbrp_enable_fastadd'])?$params['fbrp_enable_fastadd']:0);
$this->SetPreference('enable_antispam',isset($params['fbrp_enable_antispam'])?$params['fbrp_enable_antispam']:0);
$this->SetPreference('show_fieldids',isset($params['fbrp_show_fieldids'])?$params['fbrp_show_fieldids']:0);
$this->SetPreference('show_fieldaliases',isset($params['fbrp_show_fieldaliases'])?$params['fbrp_show_fieldaliases']:0);
$this->SetPreference('blank_invalid',isset($params['fbrp_blank_invalid'])?$params['fbrp_blank_invalid']:0);

// Change to use Redirect
$params['fbrp_message'] = $this->Lang('configuration_updated');
$this->DoAction('defaultadmin', $id, $params);

?>