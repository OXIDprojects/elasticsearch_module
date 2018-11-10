<?php
namespace OxidCommunity\Elasticsearch\Application\Controller\Admin;
/**
 * Order class wrapper 
 */
 
class ElasticsearchStatus extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    protected $_sThisTemplate = "oxcom_elastic_status_admin_list.tpl";
    /*
     *
     */
    public function render()
    {
        parent::render();
        $this->_aViewData["oxcomstatus"] = self::elastcisearchstatus();
        return $this->_sThisTemplate;
    }
 
     public function elastcisearchstatus()
    {
        $client = \OxidCommunity\Elasticsearch\Application\Controller\Admin\ElasticsearchCron::elasticclient();
        $response = $client->cat()->health();
        return $response;
    }
}
