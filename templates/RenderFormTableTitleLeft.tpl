{$hidden}
<table{if $css_class != ''} class="{$css_class}"{/if}>
{if $total_pages gt 1}<tr><td colspan="2">{$title_page_x_of_y}</td></tr>{/if}
{foreach from=$fields item=entry}
	  {if $entry->display == 1}
	    <tr>
	    	{strip}
	    	<td align="right" valign="top"
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
	    	</td><td align="left" valign="top">
	    	{$entry->input}
	    	{if $entry->valid == 0} &lt;--- {/if}
	    	</td></tr>
	    	{/strip}
	  {/if}
{/foreach}
<tr><td>{$prev}</td><td>{$submit}</td></tr>
</table>