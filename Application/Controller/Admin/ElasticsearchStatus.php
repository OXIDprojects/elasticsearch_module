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
        $this->_aViewData["oxcomstatus"] = elastcisearchstatus();
        return $this->_sThisTemplate;
    }
 
     public function elastcisearchstatus()
    {
        return 'todo';
    }
}
