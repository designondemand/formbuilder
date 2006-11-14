<?php

$form_id = $db->GenID(cms_db_prefix().'module_fb_form_seq');

$templateInsert = '{literal}

<!-- below, you\'ll find the "standard CSS template" for displaying FormBuilder Forms
   You can edit it to make your form layout look any way you\'d like.
   To make the form work, you\'ll need to always include the {$hidden} and {$submit}
   tags.

   You can access your form fields either using the $fields array, as demonstrated below,
   or by directly accessing fields by their names (e.g., {$myfield->input} )


   Each field has the following attributes:
       field->display         = 1 if the field should be displayed, 0 otherwise
       field->required        = 1 if the field is required, 0 otherwise
       field->required_symbol = the symbol for required fields
       field->css_class       = the CSS class specified for this field
       field->valid           = 1 if this field has passed validation, 0 otherwise
       field->hide_name       = 1 if the field name should be hidden, 0 otherwise
       field->name            = the field\'s name
       field->input           = the field\'s input control (e.g., the input field itself)
       field->input_id        = the of the field\'s input (useful for <label for="">)
       field->type            = the field\'s data type
       field->multiple_parts  = 1 if the field->input is actually a collection of controls

   In certain cases, field->input is actually an array of objects rather than an input. This
   happens, for example, in CheckBoxGroups or RadioButtonGroups. For them, you
   can iterate through field->input->name and field->input->inputs.
    

       Additional smarty variables that you can use include:
       {$total_pages}       - number of pages for multi-page forms
       {$this_page}         - number fo the current page for multi-page forms
       {$title_page_x_of_y} - displays "page x of y" for multi-page forms
       {$css_class}         - CSS Class for the form
       {$form_name}         - Form name
       {$form_id}           - Form Database ID
       {$prev}              - "Back" button for multipart forms

       Dunno why you\'d want some of those, but there you go...
-->

{/literal}


{$hidden}{assign var="cols" value="3"}
<div{if $css_class != \'\'} class="{$css_class}"{/if}>
{if $total_pages gt 1}<span>{$title_page_x_of_y}</span>{/if}
{foreach from=$fields item=entry}
	  {if $entry->display == 1}
	    	{strip}
	    	<div
	    	{if $entry->required == 1 || $entry->css_class != \'\'} class=" 
	    		{if $entry->required == 1}
	    			required
	    		{/if}
	    		{if $entry->required == 1 && $entry->css_class != \'\'} {/if}
	    		{if $entry->css_class != \'\'}
	    			{$entry->css_class}
	    		{/if}
	    		"
	    	{/if}
	    	>
	    	{if $entry->hide_name == 0}
	    		<label for="{$entry->input_id}">{$entry->name}</label>
	    		{if $entry->required_symbol != \'\'}
	    			{$entry->required_symbol}
	    		{/if}
	    	{/if}
	    	{if $entry->multiple_parts == 1}
    		<table>
					<tr>
				{section name=numloop loop=$entry->input}
	    			<td>{$entry->input[numloop]->input}&nbsp;{$entry->input[numloop]->name}</td>
	    			       {if not ($smarty.section.numloop.rownum mod $cols)}
                				{if not $smarty.section.numloop.last}
                        		</tr><tr>
                				{/if}
        					{/if}
       				{if $smarty.section.numloop.last}
                		{math equation = "n - a % n" n=$cols a=$entry->input|@count assign="cells"}
                		{if $cells ne $cols}
                			{section name=pad loop=$cells}
                        		<td>&nbsp;</td>
                			{/section}
               		 	{/if}
                		</tr>
        			{/if}
	    		{/section}
	    		</table>
	    	{else}
	    		{$entry->input}
	    	{/if}
	    	{if $entry->valid == 0} &lt;--- {/if}
	    	</div>
	    	{/strip}
	  {/if}
{/foreach}
<div class="submit">{$prev}{$submit}</div>
</div>';

$query = "INSERT INTO ".cms_db_prefix()."module_templates VALUES ('FormBuilder', ?,?,?,?)";

$ts = $db->DBTimeStamp(time());
$ts = substr($ts,1,strlen($ts)-2);

$dbresult = $db->Execute($query,array('fb_'.$form_id,$templateInsert,$ts,$ts));

$query = "INSERT INTO ".cms_db_prefix()."module_fb_form VALUES (?,?,?)";
$dbresult = $db->Execute($query,array($form_id,'Sample Form', 'sample_form'));

$query = "INSERT INTO ".cms_db_prefix()."module_fb_form_attr VALUES (?, ?, ?, ?)";


$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'css_class', 'formbuilderform'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'form_displaytype', 'tab'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'redirect_page', '-1'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'submit_button_text', 'Send Form'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'next_button_text', 'Continue...'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'prev_button_text', 'Back...'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'required_field_symbol', '*'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'unspecified', '[unspecified]'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'list_delimiter', ','));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'title_position', 'left'));

$attr_id = $db->GenID(cms_db_prefix().'module_fb_form_attr_seq');
$dbresult = $db->Execute($query,array($attr_id, $form_id, 'form_template', $templateInsert));


$fieldQuery = "INSERT INTO ".cms_db_prefix()."module_fb_field VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$optQuery = "INSERT INTO ".cms_db_prefix()."module_fb_field_opt VALUES (?, ?, ?, ?, ?)";


$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'Welcome!', 'StaticTextField', '', 0, 0, 2));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'text', 'Thanks for trying out FormBuilder. You can use this form to provide me with feedback about how you will be using Form Builder, so I can best allocate resources for future development. Thanks, ___Samuel___'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'css_class', ''));


$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'Modules you\'ll be using together', 'CheckboxGroupField', '', 0, 0, 3));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Form Builder'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Form Browser'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Front-end Users'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Newsletter Made Simple (NMS)'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Protected Pages'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_name', 'Others'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'formbuilder'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'formbrowser'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'feusers'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'nms'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'protected pages'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_checked', 'others'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not formbuilder'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not formbrowser'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not feusers'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not nms'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not protected pages'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'box_unchecked', 'not others'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id, 'css_class', ''));



$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'Where are you from?', 'CountryPickerField', '', 0, 0, 5));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'default', ''));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'select_one', 'Select One'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'css_class', ''));


$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'Do you have any comments / feedback for the module author?', 'TextAreaField', '', 0, 0, 6));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'css_class', ''));


$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'What will you personally be doing on your CMS MS site?', 'CheckboxGroupField', '', 0, 0, 4));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Design HTML/CSS'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Populate/Maintain Content'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Graphical Design'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Install/Configure modules'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Edit Smarty Templates'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Create Modules or Code User Tags'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Create Event Workflows'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Hack the Core, Extend CMS'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_name', 'Port CMS MS to Ruby on Rails'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'html'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'content'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'graphics'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'modules'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'smarty'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'php'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'events'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'hack core'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_checked', 'rails'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not html'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not content'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not graphics'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not modules'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not smarty'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not php'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not events'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not hack core'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'box_unchecked', 'not rails'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'css_class', ''));

$field_id = $db->GenID(cms_db_prefix().'module_fb_field_seq');
$dbresult = $db->Execute($fieldQuery,array($field_id,$form_id,'Send It', 'DispositionEmail', '', 0, 0, 1));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'email_subject', 'FormBuilder Sample Form Submission'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'email_from_name', 'Form Builder Installation'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'email_from_address', 'you@yourhost.com'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'destination_address', 'sjg@cmsmodules.com'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'email_template', 'Hey! Someone\'s filled out a FormBuilder Sample Submission!
Form Name: {$sub_form_name}
Date of Submission: {$sub_date}
Submission server: {$sub_host}
IP address of person using form: {$sub_source_ip}
URL of page containing form: {$sub_url}

-------------------------------------------------
And They Said:
-------------------------------------------------
Modules they\'ll be using: {$modules_you_ll_be_using}

Their CMS MS Tasks: {$what_will_you}

Where: {$where_are_you_from_}

Comments or Feedback:
{$do_you_have_any}

(end)'));
$opt_id = $db->GenID(cms_db_prefix().'module_fb_field_opt_seq');
$dbresult = $db->Execute($optQuery,array($opt_id, $field_id, $form_id,'email_encoding', 'utf-8'));
?>