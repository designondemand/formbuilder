{if $message != ''}<h4>{$message}</h4>{/if}
{$backtoform_nav}<br />
{$start_form}{$fb_hidden}{$op}{$tab_start}
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
{if $add != '' or $del != ''}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$add}{$del}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$end_form}
