<?php
/* 
   FormBuilder. Copyright (c) 2005-2007 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2007 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initialize();
		$db =& $gCms->GetDb();
		$current_version = $oldversion;
		$dict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		debug_display('Current-version: '.$current_version);
		switch($current_version)
		{
			case "0.1":
			case "0.2":
			case "0.2.2":
			case "0.2.3":
			case "0.2.4":
				{
				$flds = "
					sent_id I KEY,
					src_ip C(16),
					sent_time ".CMS_ADODB_DT;
				debug_display($flds);
				$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_fb_ip_log', $flds, $taboptarray);
				$dict->ExecuteSQLArray($sqlarray);

				$db->CreateSequence(cms_db_prefix().'module_fb_ip_log_seq');
				}
			case "0.3":
			   {
            // read the old templates
            $temp_tab_left = file_get_contents ( dirname(__FILE__).'/templates/RenderFormTableTitleLeft.tpl');
            $temp_tab_top = file_get_contents ( dirname(__FILE__).'/templates/RenderFormTableTitleTop.tpl');
            $temp_tab_css =  file_get_contents ( dirname(__FILE__).'/templates/RenderFormCSS.tpl');
            
            // this upgrade should have a lot more error checking, but I'm too lazy.
            // That's the downside to Free software :(

            // update all forms to use a Custom Template
	         $sql = 'SELECT form_id, value FROM ' . cms_db_prefix().
	              "module_fb_form_attr where name='form_displaytype' and value <> 'template'";
            $subsql = 'UPDATE '.cms_db_prefix().
	              "module_fb_form_attr SET value=? where name='form_template' and form_id=?";
	         $dbresult = $db->Execute($sql);
            while ($dbresult && $row = $dbresult->FetchRow())
               {
               if ($row['value'] == 'tab')
                  {
                  // top or left title? Another damn query.
                  $topleft = $db->GetOne('SELECT value from ' . cms_db_prefix().
	                    "module_fb_form_attr where name='title_position' and form_id=?",
                       array($row['form_id']));
                  if ($topleft == 'left')
                     {
                     $res = $db->Execute($subsql,array($temp_tab_left,$row['form_id']));
                     $this->SetTemplate('fb_'.$row['form_id'],$temp_tab_left);
                     }
                  else
                     {
                     $res = $db->Execute($subsql,array($temp_tab_top,$row['form_id']));
                     $this->SetTemplate('fb_'.$row['form_id'],$temp_tab_top);
                     }
                  }
               else if ($row['value'] == 'cssonly')
                  {
                  $res = $db->Execute($subsql,array($temp_tab_css,$row['form_id']));
                  $this->SetTemplate('fb_'.$row['form_id'],$temp_tab_css);
                  }
               }
            $cleanupsql = 'DELETE FROM ' . cms_db_prefix().
	                    "module_fb_form_attr where name='title_position' or name='form_displaytype'";
            $res = $db->Execute($cleanupsql);
            }
         case "0.4":
            {
            // upgrade the templates so they at least work.
	         $sql = 'SELECT form_id, value FROM ' . cms_db_prefix().
	              "module_fb_form_attr where name='form_template'";
            $subsql = 'UPDATE '.cms_db_prefix().
	              "module_fb_form_attr SET value=? where name='form_template' and form_id=?";
	         $dbresult = $db->Execute($sql);
            $temp_nfh = file_get_contents ( dirname(__FILE__).'/includes/new_form_header.tpl');
            $temp_nff = file_get_contents ( dirname(__FILE__).'/includes/new_form_footer.tpl');

            while ($dbresult && $row = $dbresult->FetchRow())
               {
               $fixtempl = $temp_nfh."{*".$this->Lang('upgrade03to04'}."*}\n".$row['value'].$temp_nff;

               $res = $db->Execute($subsql,array($fixtempl,$row['form_id']));
               $this->SetTemplate('fb_'.$row['form_id'],$fixtempl);
               }
            // fix rows/cols problem for TextAreas
	         $sql = 'SELECT form_id, field_id, name, value FROM ' . cms_db_prefix().
	              "module_fb_field_opt where name='rows' or name='cols'";
            $rows = array();
            $cols = array();
            while ($dbresult && $row = $dbresult->FetchRow())
               {
               if ($row['name'] == 'rows')
                  {
                  $cols[$row['form_id'].'_'.$row['field_id']] = $row['value'];
                  }
               else
                  {
                  $rows[$row['form_id'].'_'.$row['field_id']] = $row['value'];
                  }
               }
            $sql = 'UPDATE '.cms_db_prefix(). 'module_fb_field_opt set value=? where form_id=? and field_id=? and name=?';
            foreach ($rows as $key=>$val)
               {
               $thisRow = $val;
               $thisCol = $cols[$key];
               list($form_id,$field_id) = explode('_',$key);
               $res = $db->Execute($sql,array($thisRow,$form_id,$field_id,'cols'));
               $res = $db->Execute($sql,array($thisCol,$form_id,$field_id,'rows'));
               }

            }
		}
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));

?>