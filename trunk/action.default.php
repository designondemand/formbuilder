<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
if (!isset($gCms)) exit;


        if (! isset($params['form_id']) && isset($params['form']))
            {
            // get the form by name, not ID
            $params['form_id'] = $this->GetFormIDFromAlias($params['form']);
            }
        $aeform = new fbForm($this,$params,true);

        echo $aeform->RenderFormHeader();
        $finished = false;
        if (($aeform->GetPageCount() > 1 && $aeform->GetPageNumber() > 0) ||
        	(isset($params['done'])&& $params['done']==1))
            {

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
        		$ret = $aeform->GetAttr('redirect_page','-1');
        		if ($ret != -1)
        			{
        			$this->RedirectContent($ret);
        			}
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
