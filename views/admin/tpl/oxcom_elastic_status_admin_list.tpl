[{include file="headitem.tpl" title="CONTENT_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
</script>

<h1>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_STATUS" }]</h1>
<p style="height="20px;">&nbsp;</p>

<table>
    <col width="100px">
    <col width="40px">
    <col width="100px">
    <tr>
        <td>
            <h2>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_STATUS" }]<h2>
        </td>
        <td>
            [{if $oxcomstatus.0.status eq "green"}]
                <span style="height: 25px; width: 25px; background-color: green; border-radius: 50%; display: inline-block;"></span>
                </td>
                 <td>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_STATUS_OK" }]
            [{else}]
                <span style="height: 25px; width: 25px; background-color: red; border-radius: 50%; display: inline-block;"></span>
                </td>
                <td>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_STATUS_FAIL" }]
            [{/if}]
        </td>
    </tr>
</table>
<p style="height="40px;">&nbsp;</p>
<h1>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_STATUS_DETAIL" }]<h1>
<hr>
[{foreach from=$oxcomstatus key=k item=i}]
    <table>
        [{foreach from=$i key=m item=n}]
        <tr>
            <td>[{$m}]:</td>
            <td>[{$n}]</td>
        </tr>
        [{/foreach}]
    </table>
[{/foreach}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
