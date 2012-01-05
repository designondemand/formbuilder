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
if (!$this->CheckAccess()) exit; // deprecate

// Store data
$form = new fbForm($params, true);

//$tab = $this->GetActiveTab($params);

if (isset($params['fbrp_submit'])) {

	$parms['form_id'] = $form->Store($params);
	$parms['tab_message'] = 'updated';
	$parms['active_tab'] = $params['active_tab'];
	$this->Redirect($id, 'admin_add_edit_form', $returnid, $parms);

} else if(isset($params['fbrp_save'])) {

	$form->Store($params);
    $parms['fbrp_message'] = $this->Lang('form',$params['fbrp_form_op']);
	$this->Redirect($id, 'defaultadmin', $returnid, $parms);
		
} else {

	$this->Redirect($id, 'defaultadmin', $returnid);
}


?>
