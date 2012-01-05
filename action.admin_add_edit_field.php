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

		$this->initialize();
		
		$aeform = new fbForm($params,true);
		$aefield = $aeform->NewField($params);
		if (isset($params['fbrp_aef_upd']) ||
			(isset($params['fbrp_aef_add']) && $aefield->GetFieldType() != ''))
			{
			// save the field.
			$this->DoAction('admin_store_field', $id, $params);
			return;
			}
		elseif (isset($params['fbrp_aef_add']))
			{
			// should have got a field type definition, so give rest of the field options
			// reserve this space for special ops :)
			}
		elseif (isset($params['fbrp_aef_optadd']))
			{
			// call the field's option add method, with all available parameters
			$aefield->DoOptionAdd($params);
			}
		elseif (isset($params['fbrp_aef_optdel']))
			{
			// call the field's option delete method, with all available parameters
			$aefield->DoOptionDelete($params);
			}
		else
			{
			// new field, or implicit aef_add.
			// again, reserving the space for future endeavors
			}
		echo $aeform->AddEditField($id, $aefield, (isset($params['fbrp_dispose_only'])?$params['fbrp_dispose_only']:0), $returnid, isset($params['fbrp_message'])?$this->ShowMessage($params['fbrp_message']):'');
		
?>
