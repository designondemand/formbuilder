{literal}
<script type="text/javascript">
function send_order_var(form_desc)
   {
   var elem = document.getElementById('parent0');
   if (elem)
      {
      var str = '';
      var nodes = elem.childNodes;
      for (i=0;i<nodes.length;i++)
         {
         str = str + nodes[i].id + ',';
         }
      }
   var fld = document.getElementById('orderpass');
   if (fld)
		{
		fld.value = str;
		}
   return true;
   }
</script>
{/literal}

{$start_form}
<ul id="parent0" class="sortableList">
{foreach from=$fields item=thisField}
		<li id="{$thisField->id}"><strong>{$thisField->name}</strong> - {$thisField->type}</li>
{/foreach}

</ul>
<br />
{$fb_hidden}<input type="hidden" id="orderpass" name="{$id}order" value="" />
{$submit}
{$end_form}

{literal}
<script type="text/javascript">
Sortable.create('parent0',{tag:'li'});
</script>
{/literal}