<p>{$text}</p> <br>

{if is_array($data)}
    {set $count = 1}
    <table style='width: 100%;'>
        {foreach $data as $key => $value}
            <tr {if $count++ % 2 != 0}style="background-color: #f8f8f8;"{/if}>
                <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>{$key}</b></td>
                <td style='padding: 10px; border: #e9e9e9 1px solid;'>{$value}</td>
            </tr>
        {/foreach}
    </table>
{/if}