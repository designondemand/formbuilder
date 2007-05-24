{literal}

<!-- below, you'll find the "standard CSS template" for displaying FormBuilder Forms
   You can edit it to make your form layout look any way you'd like.
   To make the form work, you'll need to always include the {$hidden} and {$submit}
   tags.

   You can access your form fields either using the $fields array, as demonstrated below,
   or by directly accessing fields by their names (e.g., {$myfield->input} )


   Each field has the following attributes:
       entry->display         = 1 if the field should be displayed, 0 otherwise
       entry->required        = 1 if the field is required, 0 otherwise
       entry->required_symbol = the symbol for required fields
       entry->css_class       = the CSS class specified for this field
       entry->valid           = 1 if this field has passed validation, 0 otherwise
       entry->hide_name       = 1 if the field name should be hidden, 0 otherwise
       entry->name            = the field's name
       entry->input           = the field's input control (e.g., the input field itself)
       entry->input_id        = the of the field's input (useful for <label for="">)
       entry->type            = the field's data type
       entry->multiple_parts  = 1 if the entry->input is actually a collection of controls

   In certain cases, entry->input is actually an array of objects rather than an input. This
   happens, for example, in CheckBoxGroups or RadioButtonGroups. For them, you
   can iterate through entry->input->name and entry->input->inputs.
    

       Additional smarty variables that you can use include:
       {$total_pages}       - number of pages for multi-page forms
       {$this_page}         - number fo the current page for multi-page forms
       {$title_page_x_of_y} - displays "page x of y" for multi-page forms
       {$css_class}         - CSS Class for the form
       {$form_name}         - Form name
       {$form_id}           - Form Database ID
       {$prev}              - "Back" button for multipart forms

       Dunno why you'd want some of those, but there you go...
-->

{/literal}


{$hidden}{assign var="cols" value="3"}
<div{if $css_class != ''} class="{$css_class}"{/if}>
{if $total_pages gt 1}<span>{$title_page_x_of_y}</span>{/if}
{foreach from=$fields item=entry}
          <!-- {$entry|print_r} -->
	  {if $entry->display == 1}
	    	{strip}
                {* leading div before the tag *}
                {if $entry->type != "Fieldset Start" && $entry->type != "Fieldset End"}
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
                {* begin field output *}
	    	{if $entry->hide_name == 0}
	    		<label for="{$entry->input_id}">{$entry->name}</label>
	    		{if $entry->required_symbol != ''}
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
                {* trailing div *}
                {if $entry->type != "Fieldset Start" && $entry->type != "Fieldset End"}
	    	</div>
                {/if}
	    	{/strip}
	  {/if}
{/foreach}
<div class="submit">{$prev}{$submit}</div>
</div>