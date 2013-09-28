{foreach from=$database item=table_data}
 <h2>{$table_data.name}</h2>
 <table class="database">
  <tr>
   {foreach from=$table_data.columns item=column}
    <td><strong>{$column}</strong></td>
   {/foreach}
  </tr>

  {foreach from=$table_data.rows item=row}
   <tr>
    {foreach from=$row item=item}
	 <td>{$item|truncate:100}</td>
    {/foreach}
   </tr>
  {/foreach}
 </table>
{/foreach}