<?php
// load oxid framework
require_once(dirname(__FILE__). "/../../../../bootstrap.php");

$result = \OxidCommunity\Elasticsearch\Application\Controller\Admin\ElasticsearchCron::CronAddArticle2Index(5);

if ($result == '1') {
    echo 'LOAD NEW';
    echo '<script type="text/javascript">location.reload(true);</script>';
} elseif ($result == '0') {
    echo 'DONE';
} else {
    echo $result;
}
