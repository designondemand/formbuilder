{if $message != ''} <h4>{$message}</h4> {/if}

{$tabheaders}
{$start_formtab}
<table cellspacing="0" class="pagetable" width="80%">
<thead><tr>
    <th>{$title_form_name}</th>
    <th>{$title_form_alias}</th>
    <th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>
</tr>
</thead>

{foreach from=$forms item=entry}
	<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
		<td>{$entry->name}</td>
		<td>&#123;cms_module module='FormBuilder' form='{$entry->usage}'&#125;</td>
		<td>{$entry->xml}</td>
		<td>{$entry->editlink}</td>
		<td>{$entry->deletelink}</td>
	</tr>
{/foreach}

<tr class="row2"><td colspan="5">&nbsp;</td></tr>
<tr class="row1"><td colspan="5">

{if $addlink != ''}{$addlink}{$addform}{/if}
</td></tr>
</table>
{$end_tab}
{$start_configtab}
{if $may_config == 1}
{$start_configform}
	<div class="pageoverflow">
		<p class="pagetext">{$title_enable_fastadd}:</p>
		<p class="pageinput">{$input_enable_fastadd}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_hide_errors}:</p>
		<p class="pageinput">{$input_hide_errors}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_relaxed_email_regex}:</p>
		<p class="pageinput">{$input_relaxed_email_regex}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_show_version}:</p>
		<p class="pageinput">{$input_show_version}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_enable_antispam}:</p>
		<p class="pageinput">{$input_enable_antispam}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
	{$config_formend}

{else}
	<p>{$no_permission}</p>
{/if}
{$end_tab}
{$end_tabs}

