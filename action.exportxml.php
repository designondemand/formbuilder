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

if (! isset($params['form_id']) && isset($params['form'])) {

	// get the form by name, not ID
	$params['form_id'] = $this->GetFormIDFromAlias($params['form']);
}

$form = new fbForm($params,true);

$spec = $form->GetName().".xml";
$spec = preg_replace('/[^\w\d\.\-\_]/','_',$spec);
$xmlstr = $form->ExportXML(isset($params['fbrp_export_values'])?true:false);

@ob_clean();
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header('Content-Description: File Transfer');
header('Content-Type: application/force-download; charset=utf-8');
header('Content-Length: ' . strlen($xmlstr));
header('Content-Disposition: attachment; filename=' . $spec);
echo $xmlstr;
exit; // Prevent default
?>