[{include file="headitem.tpl" title="CONTENT_MAIN_TITLE"|oxmultilangassign}]


<script type="text/javascript">
</script>

<h1>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_CRON" }]</h1>
<p>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_CRON_TEXT" }]</p>

<h2>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE" }]</h2>
<hr>
<p>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_TEXT" }]</p>
<table cellspacing="2px" cellpadding="2px" style="padding: 0px; margin: 0px; border: 0px;">
    <col width="20%">
    <col width="1%">
    <col width="20%">
    <col width="1%">
    <col width="20%">
    <col width="1%">
    <col width="20%">
    <tr>
        <td style="background-color: #eee">
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_CREATEINDEX_HELP" }]
        </td>
        <td></td>
        <td style="background-color: #eee">
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_DELETEINDEX_HELP" }]
        </td>
        <td></td>
        <td style="background-color: #eee">
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_RESETINDEX_HELP" }]
        </td>
        <td></td>
        <td>
        </td>
    </tr>
    <tr style="margin-bottom: 20px;">
        <td style="background-color: #ddd">
            <a href="[{$sModuleUrl}]Cronjob/ArticleCreateIndex.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_CREATEINDEX" }]">
            </a>
        </td>
        <td></td>
        <td style="background-color: #ddd">
            <a href="[{$sModuleUrl}]Cronjob/ArticleDeleteIndex.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_DELETEINDEX" }]">
            </a>          
        </td>
        <td></td>
        <td style="background-color: #ddd">
            <a href="[{$sModuleUrl}]Cronjob/ArticleResetIndex.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_RESETINDEX" }]">
            </a>
        </td>
        <td></td>
        <td>
        </td>
    </tr>
    <tr>
        <td colspan="7" style="height:20px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td style="background-color: #eee">
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_INDEXALLMISSING_HELP" }]
        </td>
        <td></td>
        <td style="background-color: #eee">
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_MARK4REIMPORT_HELP" }]
        </td>
        <td></td>
        <td>

        </td>
        <td></td>
        <td>

        </td>
    </tr>
    <tr>
        <td style="background-color: #ddd">
            <a href="[{$sModuleUrl}]Cronjob/ArticleIndexAllMissing.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_INDEXALLMISSING" }]">
            </a>
        </td>
        <td></td>
        <td style="background-color: #ddd">
            <a href="[{$sModuleUrl}]Cronjob/MarkAllArticles4Reimport.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_MARK4REIMPORT" }]">
            </a>
        </td>
        <td></td>
        <td>

        </td>
        <td></td>
        <td>

        </td>
    </tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
