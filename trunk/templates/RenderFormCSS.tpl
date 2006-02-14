{$hidden}
<div{if $css_class != ''} class="{$css_class}"{/if}>
{if $total_pages gt 1}<div class="status">{$title_page_x_of_y}</div>{/if}
{foreach from=$fields item=entry}
	  {if $entry->display == 1}
	    	{strip}
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
	    	{if $entry->hide_name == 0}
	    		<label for="{$entry->input_id}">{$entry->name}</label>
	    		{if $entry->required_symbol != ''}
	    			{$entry->required_symbol}
	    		{/if}
	    	{/if}
	    	{$entry->input}
	    	{if $entry->valid == 0} &lt;--- {/if}
	    	</div>
	    	{/strip}
	  {/if}
{/foreach}
<div class="submit">{$prev}{$submit}</div>
</div>
