<?php
// load oxid framework
require_once(dirname(__FILE__). "/../../../../bootstrap.php");
$result = \OxidCommunity\Elasticsearch\Application\Controller\Admin\ElasticsearchCron::DeleteArticleIndex();
if ($result['acknowleged'] == '1') {
    echo 'Done';
} else {
    echo $result;
}
