<?php
namespace OxidCommunity\Elasticsearch\Application\Controller\Admin;

use Elasticsearch\ClientBuilder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * ElasticsearchCron class wrapper 
 */
 
class ElasticsearchCron extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    protected $_sThisTemplate = "oxcom_elastic_status_cron_list.tpl";
    /*
     *
     */
    public function render()
    {
        parent::render();
        $oViewConf = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->_aViewData["sModuleUrl"] = $oViewConf->getModuleUrl('oxcom/elasticsearch');
        return $this->_sThisTemplate;
    }

     /*
     *
     */
    public function elasticclient()
    {
        $oViewConf = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $sPath = getcwd().'/../../../Logs/elasticsearch.log';

        $sLogValue = "Monolog\Logger::".self::GetModuleConfVar('oxcom_elasticsearch_article_loglevel');
        $logger = new Logger('elasticsearchlogging');
        if ($sLogValue == 'INFO') {
            $logger->pushHandler(new StreamHandler($sPath, Logger::INFO));
        } else {
            $logger->pushHandler(new StreamHandler($sPath, Logger::WARNING));
        }

        $retry = self::GetModuleConfVar('oxcom_elasticsearch_article_retry');

        $host1 = array();
        $host1['host'] = self::GetModuleConfVar('oxcom_elasticsearch_server_host');
        $host1['port'] = self::GetModuleConfVar('oxcom_elasticsearch_server_port');
        $host1['scheme'] = self::GetModuleConfVar('oxcom_elasticsearch_server_scheme');

        $sUser = self::GetModuleConfVar('');
        if (!empty($sUser)) {
            $host1['user'] = self::GetModuleConfVar('oxcom_elasticsearch_server_user');    
        }
     
        $sPass = self::GetModuleConfVar('');
        if (!empty($sPass)) {
            $host1['pass'] = self::GetModuleConfVar('oxcom_elasticsearch_server_pass'); 
        }
     
        $hosts = [
                $host1
        ];

        $client = ClientBuilder::create()->setHosts($hosts)->setLogger($logger)->setRetries($retry)->build();
        return $client;
    }
 
    /*
     *
     */
    protected function GetModuleConfVar($var)
    {
        $allowedConf = [
            'oxcom_elasticsearch_article_index',
            'oxcom_elasticsearch_article_data',
            'oxcom_elasticsearch_article_type',
            'oxcom_elasticsearch_article_shards',
            'oxcom_elasticsearch_article_replicas',
            'oxcom_elasticsearch_article_loglevel',
            'oxcom_elasticsearch_article_retry',
            'oxcom_elasticsearch_server_host',
            'oxcom_elasticsearch_server_port',
            'oxcom_elasticsearch_server_scheme',
            'oxcom_elasticsearch_server_user',
            'oxcom_elasticsearch_server_pass'
        ];

        if (in_array($var, $allowedConf)) {
            return \OxidEsales\Eshop\Core\Registry::getConfig()->getShopConfVar($var, null, 'module:oxcomelasticsearch');
        } else {
            return null;
        }
     }

    /*
     *
     */
    public function ExistIndex($index)
    {
        $client = self::elasticclient();
        $indexParams['index']  = $index;
        return $client->indices()->exists($indexParams);
     }

    /*
     *
     */
    public function CreateArticleIndex()
    {
        $aLanguages = self::GetAllLang();
        foreach ($aLanguages as $aLang) {
            self::CreateArticleIndexOneLang($aLang->id);
        }    
        return '1';
    } 
 
     /*
     *
     */
    public function CreateArticleIndexOneLang($Lang)
    {
         $index = self::GetModuleConfVar('oxcom_elasticsearch_article_index') . "_" . $Lang;
         $sExist = self::ExistIndex($index);
         if ($sExist == true) {
             echo "Index: ".$index." already exists!<br>";
             return '0';
         }
         $client = self::elasticclient();
         $params = [
             'index' => $index,
             'body'  => [
                 'settings' => [
                     'number_of_shards'   => self::GetModuleConfVar('oxcom_elasticsearch_article_shards'),
                     'number_of_replicas' => self::GetModuleConfVar('oxcom_elasticsearch_article_replicas')
                 ]
             ]
         ];
         $response = $client->indices()->create($params);
         return $response;
    }

    /*
     *
     */
    public function DeleteArticleIndex()
    {
        $aLanguages = self::GetAllLang();
        foreach ($aLanguages as $aLang) {
            self::DeleteArticleIndexOneLang($aLang->id);
        }  
        return '1';
    }  
 
 
    /*
     *
     */
    public function DeleteArticleIndexOneLang($Lang='0')
    {
        $index = self::GetModuleConfVar('oxcom_elasticsearch_article_index') . "_" . $Lang;
        $sExist = self::ExistIndex($index);
        if ($sExist == false) {
            echo "Index: ".$index." does not exists!<br>";
            return '0';
        }
        $client = self::elasticclient();
        $params = [
            'index' => $index
        ];
        $response = $client->indices()->delete($params);
        return $response;
    }  
 
     /*
     *
     */
    public function RecreateArticleIndex()
    {
        // Delete Index
        $info = self::DeleteArticleIndex();
        
        if ($info->acknowleged <> 1) {
            return 'Index could not be deleted!';
        }  
     
        // Create Index
        $info = self::CreateArticleIndex();
        
        if ($info->acknowleged <> 1) {
            return 'Index could not be created!';
        }          
     
        // Reset all Articles for new Import
        $info = self::MarkAllArticle4NewImport();
        
        if ($info->acknowleged <> 1) {
            return 'Articles were not reseted!';
        }  else {
            return '1';
        }
    } 
 
    /*
     *
     */
    public function RecreateArticleIndexOneLang($Lang='0')
    {
        // Delete Index
        $info = self::DeleteArticleIndexOneLang($Lang);
        
        if ($info->acknowleged <> 1) {
            return 'Index could not be deleted!';
        }  
     
        // Create Index
        $info = self::CreateArticleIndexOneLang($Lang);
        
        if ($info->acknowleged <> 1) {
            return 'Index could not be created!';
        }          
     
        // Reset all Articles for new Import
        $info = self::MarkAllArticle4NewImportOneLang($Lang);
        
        if ($info->acknowleged <> 1) {
            return 'Articles were not reseted!';
        }  else {
            return '1';
        }
    }   

     /*
     *
     */
    public function GetAllLang()
    {
        $myLang = \OxidEsales\Eshop\Core\Registry::getLang();
        return $myLang->getLanguageArray(null, true, false);
    }  
 
    /*
     *
     */
    public function MarkAllArticle4NewImport()
    {
        $aLanguages = self::GetAllLang();
        foreach ($aLanguages as $aLang) {
            self::MarkAllArticle4NewImportOneLang($aLang->id);
        }        
        return '1';
    }  
 
    /*
     *
     */
    public function MarkAllArticle4NewImportOneLang($iLang = '0')
    {
        $table = getViewName('oxarticles', $iLang);    
        $sQ = "UPDATE ".$table." SET oxcomelasticstat = '0'; UPDATE ".$table." SET oxcomelasticstat = '1' WHERE oxactive = '1'";
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oDb->execute($sQ);
        return '1';
    }   
 
    /*
     *
     */
    public function IndexArticle2Elasticsearch($oxid,$Lang = '0')
    {
         $client = self::elasticclient();
     
         //doesn't work! first article needs all parameters
         //$oxarticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);    
         //$oxarticle->load($oxid);    
     
         $oxarticle = self::GetArticleData($oxid, $Lang);
     
         $params = [
             'index' => self::GetModuleConfVar('oxcom_elasticsearch_article_index'),
             'type'  => self::GetModuleConfVar('oxcom_elasticsearch_article_type'),
             'id'    => $oxid,
             'body'  => [
                 (array) $oxarticle
             ]
         ];
         $response = $client->index($params);
         return $response;
    } 

    /*
     *
     */
    public function GetArticleData($oxid, $Lang='0')
    {
        $data = self::GetModuleConfVar('oxcom_elasticsearch_article_data');

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        
        $aFinalQuery = array();
        
        //we do magic
        foreach ($adata as $table => $param) {
            $table = getViewName($table, $Lang);
            $sQ = "";
            $rowparam = "0";
            $columnparam = "0";
        
            if ($param[0] == 'row') {
                $sQ = "SELECT ";
                if ($param[1] == 'all') {
                    $sQ .= $table.".* ";
                } else {
                    foreach ((array) $param[1] as $value) {
                        if ($rowparam == '1') {
                            $sQ .= ", ";
                        }
                        $sQ .= $table.".".$value;
                        $rowparam = '1';
                    }
                }
                $sQ .= " FROM ".$table." WHERE ".$param[2]."=".$oDb->quote($oxid);
        
                $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQ);
                //Fetch the results row by row
                if ($resultSet != false && $resultSet->count() > 0) {
                    while (!$resultSet->EOF) {
                        $row = $resultSet->getFields();
        
                        if($table == 'oxarticles') {
                            $aFinalQuery = $row;
                        } else {
                            $aFinalQuery[$table] = $row;
                        }
        
                        $resultSet->fetchRow();
                    }
                }
                continue;
            }
        
            if ($param[0] == 'column') {
                $sQ = "SELECT ";
                $sQD = "Select ".$table.".".$param[1]." as col FROM ".$table." GROUP BY ".$table.".".$param[1];
                $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sQD);
                //Fetch the results row by row
                if ($resultSet != false && $resultSet->count() > 0) {
                    while (!$resultSet->EOF) {
                        $row = $resultSet->getFields();
                        if ($columnparam == '1') {
                            $sQ .= ", ";
                        }
                        $sQ .= "GROUP_CONCAT(CASE WHEN ".$table.".".$param[1]." = ".$oDb->quote($row[0])." THEN  ".$table.".".$param[2]." ELSE NULL END) AS ".$oDb->quote($row[0]);
                        $columnparam = '1';
                        $resultSet->fetchRow();
                    }
                }
                $sQ .= "FROM ".$table." WHERE ".$param[3]."=".$oDb->quote($oxid);
        
                $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQ);
                //Fetch the results row by row
                if ($resultSet != false && $resultSet->count() > 0) {
                    while (!$resultSet->EOF) {
                        $row = $resultSet->getFields();
        
                        $aFinalQuery[$table] = $row;
        
                        $resultSet->fetchRow();
                    }
                }
        
                continue;
            }
        
        }
     
        return recursiveStripTags($aFinalQuery);
     
    }
 
    /*
     *
     */
    public function recursiveStripTags($data) {
        foreach ($data as $key => $value) {
            if(is_array($value)) {
                $data[$key] = recursiveStripTags($value);
            }
            else {
                $data[$key] = trim(preg_replace('/\s+/', ' ', strip_tags($value)));
            }
        }
        return $data;
    }
 
    /*
     *
     */
    public function SearchArticleFromElasticsearch($search,$Lang='0')
    {
         // Performance
         if (!is_array($params)) {
             return null;
         }
     
         $client = self::elasticclient(); 
     
         $params = [
             'index' => self::GetModuleConfVar('oxcom_elasticsearch_article_index'),
             'type'  => self::GetModuleConfVar('oxcom_elasticsearch_article_type'),
             'body'  => [
                 'match' => [
                     $search
                 ]
             ]
         ];
     
         $response = $client->search($params);
         return $response;
    }    
 
     /*
     *
     */
    public function CronAddArticle2Index($Limit, $Lang='0')
    {
         if (!is_numeric($Limit)) { 
             return 'Bullshit'; 
         }
        
         $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
         $table = getViewName('oxarticles', $Lang);
     
         $sQ = "Select oxid from ".$table." WHERE oxactive = '1' AND oxcomelasticstat= '1' LIMIT ".$Limit;

         $resultSet = $oDb->select($sQ);

         if ($resultSet != false && $resultSet->count() > 0) {
             while (!$resultSet->EOF) {
                 $row = $resultSet->getFields();
                 $final = self::IndexArticle2Elasticsearch($row['oxid']);
                 if ($final == '1') {
                    $sFinalQ = "UPDATE ".$table." SET oxcomelasticstat= '0' WHERE oxid=".$oDb->quote($row['oxid']);
                    $oDb::getDb()->execute($sQ);
                 }
                 $resultSet->fetchRow();
             }
          }
    
          $sQ2 = "Select oxid from ".$table." WHERE oxactive = '1' AND oxcomelasticstat= '1' LIMIT 1";
          $resultSet2 = $oDb::getDb()->select($sQ2);
          if ($resultSet != false && $resultSet->count() > 0) {
              return '0';
          } else {
              return '1';
          }
    } 
}
