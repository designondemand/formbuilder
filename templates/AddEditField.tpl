{if $message != ''}<h4>{$message}</h4>{/if}
{$start_form}{$hidden}{$op}{$tab_start}
{$maintab_start}
	{foreach from=$mainList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<p class="pageinput">{$entry->input}</p>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{$tab_end}
{$advancedtab_start}
	{foreach from=$advList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<p class="pageinput">{$entry->input}</p>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{$tab_end}
{$tabs_end}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$end_form}
