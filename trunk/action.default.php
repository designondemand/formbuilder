<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;

		$this->mod_globals->ModuleInputPrefix = $id;
//debug_display($params);

        if (! isset($params['form_id']) && isset($params['form']))
            {
            // get the form by name, not ID
            $params['form_id'] = $this->GetFormIDFromAlias($params['form']);
            }
//echo "pre-instantiate form";
//debug_display($params);
        $aeform = new fbForm($this,$params,true);
//echo 'pre-render';
//debug_display($params);
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
            	$results = $aeform->Dispose($returnid);
            	}
            }
//error_log('Finished is '.$finished);

		if (! $finished)
			{
        	echo $this->CreateFormStart($id, 'default', $returnid, 'post', 'multipart/form-data');
        	echo $aeform->RenderForm($id, $params, $returnid);
        	echo $this->CreateFormEnd();
        	}
        else
        	{
        	if ($results[0] == true)
        		{
        		$this->RedirectContent($aeform->GetAttr('redirect_page','0'));
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
?>
