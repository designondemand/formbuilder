{literal}

<!-- Below, you'll find the "standard CSS template" for displaying FormBuilder Forms
   You can edit it to make your form layout look any way you'd like.
   To make the form work, you'll need to always include the {$hidden} and {$submit}
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
       field->has_label       = 1 if the field type has a label
       field->needs_div       = 1 if the field needs to be wrapped in a DIV (or table row,
                                if that's the way you swing)   
       field->name            = the field's name
       field->input           = the field's input control (e.g., the input field itself)
       field->input_id        = the ID of the field's input (useful for <label for="">)
       field->type            = the field's data type
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
       {$form_id}           - Form database ID
       {$prev}              - "Back" button for multipart forms

       Dunno why you'd want some of those, but there you go...
-->

{/literal}


{$hidden}
<div{if $css_class != ''} class="{$css_class}"{/if}>
{if $total_pages gt 1}<span>{$title_page_x_of_y}</span>{/if}
{foreach from=$fields item=entry}
	  {if $entry->display == 1}
	    	{strip}
	    	{if $entry->needs_div == 1}
            <div
	    	   {if $entry->required == 1 || $entry->css_class != ''} class="
	    		  {if $entry->required == 1}
	    			 required
	    		  {/if}
	    		  {if $entry->required == 1 && $entry->css_class != ''} {/if}
	    		  {if $entry->css_class != ''}
	    			 {$entry->css_class}
	    		  {/if}
	    		  "
	    	   {/if}
	    	   >
         {/if}
	    	{if $entry->hide_name == 0}
	    		<label for="{$entry->input_id}">{$entry->name}</label>
	    		{if $entry->required_symbol != ''}
	    			{$entry->required_symbol}
	    		{/if}
	    	{/if}
	    	{if $entry->multiple_parts == 1}

				{section name=numloop loop=$entry->input}
	    			<div>{$entry->input[numloop]->input}&nbsp;{$entry->input[numloop]->name}</div>
	    		{/section}
	    	{else}
	    		{$entry->input}
	    	{/if}
	    	{if $entry->valid == 0} &lt;--- {/if}
         {if $entry->needs_div == 1}
            </div>
         {/if}
	    	{/strip}
	  {/if}
{/foreach}
<div class="submit">{$prev}{$submit}</div>
</div>