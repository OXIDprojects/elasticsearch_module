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
            $data = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopConfVar($var, null, 'module:oxcomelasticsearch');
            if ($var == 'oxcom_elasticsearch_article_data') {
                $datafinal = array();
                foreach ($data as $item => $value)
                {
                    $datafinal[$item] = explode(",", $value);
                }
                return $datafinal;
            } else {
                return $data;
            }

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
        $aLanguages = self::GetAllLang();
        foreach ($aLanguages as $aLang) {
            self::RecreateArticleIndexOneLang($aLang->id);
        }
        return '1';
    } 
 
    /*
     *
     */
    public function RecreateArticleIndexOneLang($Lang='0')
    {
        // Delete Index
        $info = self::DeleteArticleIndexOneLang($Lang);

        // Create Index
        $info = self::CreateArticleIndexOneLang($Lang);

        if (!empty($info)) {
            echo 'Index was created!<br>';
        } else {
            echo 'Something went wrong!<br>';
            return null;
        }
     
        // Reset all Articles for new Import
        $info = self::MarkAllArticle4NewImportOneLang($Lang);

        if ($info == '1') {
            echo 'Articles were reseted and should be reimported!<br>';
        }  else {
            echo 'Articles were not reseted!<br>';
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

        $index = self::GetModuleConfVar('oxcom_elasticsearch_article_index')."_".$Lang;
        $type = self::GetModuleConfVar('oxcom_elasticsearch_article_type');
        $id = self::md5_hex_to_dec($oxid);
        $data = self::GetArticleData($oxid, $Lang);

        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id,
            'body' => $data
        ];

         $response = $client->index($params);
         if (!empty($response)) {
             return '1';
         } else {
             return '0';
         }
    }

    /*
     *
     */
    public function md5_hex_to_dec($hex_str)
    {
        $arr = str_split($hex_str, 4);
        foreach ($arr as $grp) {
            $dec[] = str_pad(hexdec($grp), 5, '0', STR_PAD_LEFT);
        }
        return implode('', $dec);
    }


    /*
     *
     */
    public function GetArticleData($oxid, $Lang='0')
    {
        $adata = self::GetModuleConfVar('oxcom_elasticsearch_article_data');

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

                        $aFinalQuery += self::GetArticleDataProcessGeneratedQuerys($table,$row);

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

                        $aFinalQuery += self::GetArticleDataProcessGeneratedQuerys($table,$row);
        
                        $resultSet->fetchRow();
                    }
                }
        
                continue;
            }
        
        }

        //Strip Tags
        $aReturnData = self::recursiveStripTags($aFinalQuery);
        //Cleanup corrupt MySQL-Data and return
        return self::recursiveStripCorruptData($aReturnData);

    }

    /*
    *
    */
    public function GetArticleDataProcessGeneratedQuerys($table,$data)
    {
        $aFinalData = array();

        foreach ($data as $key => $value) {
            $aFinalData[$table."__".$key] = $value;
        }

        return $aFinalData;
    }

    /*
     *
     */
    public function recursiveStripCorruptData($data) {
        $aFinalData = array();
        $abadData = array('0000-00-00','0000-00-00 00:00:00');
        foreach ($data as $key => $value) {
            if(in_array($value, $abadData)) {
                continue;
            } else {
                $aFinalData[$key] = $value;
            }
        }
        return $data;
    }

    /*
     *
     */
    public function recursiveStripTags($data) {
        foreach ($data as $key => $value) {
            if(is_array($value)) {
                $data[$key] = self::recursiveStripTags($value);
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
         if (!is_array($search)) {
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
    public function CronAddArticle2IndexOneLang($Limit, $Lang='0')
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
                 $final = self::IndexArticle2Elasticsearch($row[0]);
                 if ($final == '1') {
                    $sFinalQ = "UPDATE ".$table." SET oxcomelasticstat= '0' WHERE oxid=".$oDb->quote($row[0]);
                    $oDb->execute($sFinalQ);
                 }
                 $resultSet->fetchRow();
             }
          }
    
          $sQ2 = "Select oxid from ".$table." WHERE oxactive = '1' AND oxcomelasticstat= '1' LIMIT 1";
          $resultSet2 = $oDb->select($sQ2);
          if ($resultSet2 != false && $resultSet2->count() > 0) {
              return '1';
          } else {
              return '0';
          }
    }

    /*
    *
    */
    public function CronAddArticle2Index($Limit)
    {
        $aLanguages = self::GetAllLang();
        $sReturn = 0;
        foreach ($aLanguages as $aLang) {
            $sReturn = $sReturn + self::CronAddArticle2IndexOneLang($Limit,$aLang->id);
        }
        if ($sReturn > '0') {
            return '0';
        } else {
            return '1';
        }
    }
}
