<?php
/**
 * @package   elasticsearch
 * @category  OXID Module
 * @version   0.0.1
 * @license   GPL3 License http://opensource.org/licenses/GPL
 * @author    OXID Community / SysEleven / fleur-ami
 * @link      https://github.com/elastic/elasticsearch-php
 */
$sMetadataVersion = '2.0';
$aModule = [
    'id'          => 'oxcomelasticsearch',
    'title'       => [
        'de' => 'OXID Community Elasticsearch Module',
        'en' => 'OXID Community Elatsicsearch Module',
    ],
    'description' => [
        'de' => 'OXID Community Module für die Integration von Elasticsearch in Oxid (V6).',
        'en' => 'OXID Community Module for the Integration of Elasticsearch in Oxid (V6).',
    ],
    'thumbnail'   => '',
    'version'     => '0.0.1',
    'author'      => 'OXID Community',
    'url'         => 'https://github.com/OXIDprojects/elasticsearch_module',
    'email'       => '',
    'extend'      => [
    ],
    'controllers' => [
        'oxcom_elastic_status_admin_list' =>  \OxidCommunity\Elasticsearch\Application\Controller\Admin\ElasticsearchStatus::class,
        'oxcom_elastic_status_cron_list'  =>  \OxidCommunity\Elasticsearch\Application\Controller\Admin\ElasticsearchCron::class
    ],
    'events' => [
        'onActivate'   				=> '\OxidCommunity\Elasticsearch\Core\Events::onActivate',
        'onDeactivate' 				=> '\OxidCommunity\Elasticsearch\Core\Events::onDeactivate'
    ],
    'templates'   => [
    ],
    'settings' => [
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_index',
            'type'  => 'str',
            'value' => 'oxcomarticle'
        ),
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_data',
            'type'  => 'aarr',
            'value' => array(
                           'oxarticles' => array('row','all','oxid'),
                           'oxobject2attribute' => array('column','oxattrid','oxvalue','oxobjectid'),
                           'oxartextends' => array('row',array('oxlongdesc'),'oxid'),
                       )
        ),
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_type',
            'type'  => 'str',
            'value' => 'oxarticle'
        ),
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_shards',
            'type'  => 'str',
            'value' => '2'
        ),
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_replicas',
            'type'  => 'str',
            'value' => '0'
        ),        
        array(
            'group' => 'oxcomelasticsearchserverarticle',
            'name'  => 'oxcom_elasticsearch_article_loglevel',
            'type'  => 'str',
            'value' => 'Info'
        ),
        array(
            'group' => 'oxcomelasticsearchserver',
            'name'  => 'oxcom_elasticsearch_server_host',
            'type'  => 'str',
            'value' => 'localhost'
        ),
        array(
            'group' => 'oxcomelasticsearchserver',
            'name'  => 'oxcom_elasticsearch_server_port',
            'type'  => 'str',
            'value' => '9200'
        ),
        array(
            'group' => 'oxcomelasticsearchserver',
            'name'  => 'oxcom_elasticsearch_server_scheme',
            'type'  => 'str',
            'value' => 'https'
        ),
        array(
            'group' => 'oxcomelasticsearchserver',
            'name'  => 'oxcom_elasticsearch_server_user',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxcomelasticsearchserver',
            'name'  => 'oxcom_elasticsearch_server_pass',
            'type'  => 'str',
            'value' => ''
        )
    ]
];
