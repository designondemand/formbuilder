{* TABLE FORM LAYOUT / Field titles on Top *}
{* next line sets number of columns for things like checkbox groups *}
{assign var="cols" value="3"}
{$fb_form_header}
{if $fb_form_done == 1}
	{* This first section is for displaying submission errors *}
	{if $fb_submission_error}
		<div class="error_message">{$fb_submission_error}</div>
		{if $fb_show_submission_errors}
			<table class="error">
			{foreach from=$fb_submission_error_list item=thisErr}
				<tr><td>{$thisErr}</td></tr>
			{/foreach}
			</table>
		{/if}
	{/if}
{else}
	{* this section is for displaying the form *}
	{* we start with validation errors *}
	{if $fb_form_has_validation_errors}
		<div class="error_message">
		<ul>
		{foreach from=$fb_form_validation_errors item=thisErr}
			<li>{$thisErr}</li>
		{/foreach}
		</ul>
		</div>
	{/if}
	{if $captcha_error}
		<div class="error_message">{$captcha_error}</div>
	{/if}

	{* and now the form itself *}
	{$fb_form_start}
	{$fb_hidden}

    <table{if $css_class != ''} class="{$css_class}"{/if}>
    {if $total_pages gt 1}<tr><td colspan="2">{$title_page_x_of_y}</td></tr>{/if}
    {foreach from=$fields item=entry}
	  {if $entry->display == 1 &&
	    $entry->type != '-Fieldset Start' &&
	    $entry->type != '-Fieldset End' }
	    <tr>
	    	{strip}
	    	<td valign="top"
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
	    	{if $entry->hide_name == 0}
	    		{$entry->name}
	    		{if $entry->required_symbol != ''}
	    			{$entry->required_symbol}
	    		{/if}
	    	{/if}
	    	</td></tr><tr><td align="left" valign="top"{if $entry->css_class != ''} class="{$entry->css_class}"{/if}>
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
	    	{if $entry->valid == 0} &lt;--- {$entry->error}{/if}
	    	</td></tr>
	    	{/strip}
	  {/if}
{/foreach}
<tr><td>{$prev}</td><td>{$submit}</td></tr>
</table>
{$fb_form_end}
{/if}
{$fb_form_footer}