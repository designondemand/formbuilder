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

class fbLoader {

	#---------------------
	# Magic methods
	#---------------------

	protected function __construct() {}

	#---------------------
	# Field methods
	#---------------------

    public final function &NewField(&$params)
    {

		$db = cmsms()->GetDb();
		$field = null;
		$form = new fbForm($params, true); // deprecate when you can, totally unneccery.
		$className = 'fb'; // add fb secure prefix, to avoid namespace collapse.
		
		// Try to get type by id first.
		if (isset($params['field_id']) && $params['field_id'] != -1 ) {
		
			$sql = 'SELECT type FROM '.cms_db_prefix().'module_fb_field WHERE field_id=?';
			$type = $db->GetOne($sql, array($params['field_id']));
			
			if($type) {				

				$className .= $type;
				$field = new $className($form, $params);
				$field->LoadField($params);  
			}
		}
		
		// No luck, check if we have type.
		if (!is_object($field) && isset($params['field_type'])) {
		
			$className .= $params['field_type'];
			$field = new $className($form, $params);
		}
		
		return $field;
    }	
	



} // end of class

?>