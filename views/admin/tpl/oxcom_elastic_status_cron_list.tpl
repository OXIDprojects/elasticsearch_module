[{include file="headitem.tpl" title="CONTENT_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
</script>

<h1>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_CRON" }]</h1>
<p>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_CRON_TEXT" }]</p>

<h2>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE" }]</h2>
<hr>
<p>[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_TEXT" }]</p>
<table>
    <col width="20%">
    <col width="20%">
    <col width="20%">
    <col width="20%">
    <tr>
        <td>
            <a href="[{$sModuleUrl}]Cronjob/ArticleCreateIndex.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_CREATEINDEX" }]">
            </a>
        </td>
        <td>
            <a href="[{$sModuleUrl}]Cronjob/ArticleResetIndex.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_RESETINDEX" }]">
            </a>          
        </td>
        <td>
            <a href="[{$sModuleUrl}]Cronjob/ArticleIndexAllMissing.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_INDEXALLMISSING" }]">
            </a>     
        </td>
        <td>
            <a href="[{$sModuleUrl}]Cronjob/MarkAllArticles4Reimport.php" target="_blank">
                <input type="submit" value="[{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_MARK4REIMPORT" }]">
            </a>  
        </td>
    </tr>
    <tr>
        <td>
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_CREATEINDEX_HELP" }]
        </td>
        <td>
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_RESETINDEX_HELP" }]
        </td>
        <td>
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_INDEXALLMISSING_HELP" }]
        </td>
        <td>
            [{ oxmultilang ident="OXCOM_ELASTICSEARCH_ARTICLE_MARK4REIMPORT_HELP" }]
        </td>
    </tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
