<?php
namespace OxidCommunity\Elasticsearch\Core;
/**
 * Order class wrapper 
 */
 
class Events 
{

    /**
     * An array of SQL statements, that will be executed only at the first time of module installation.
     * table: oxarticle
     *
     * @var array
     */
    private static $_aSetupOxcomOxarticleSQLs = array(
        "OXCOMELASTICSTAT"       => "ALTER TABLE `oxarticles` ADD `OXCOMELASTICSTAT` TINYINT( 1 ) UNSIGNED NOT NULL default '0';"
        "OXCOMELASTICSTAT_1"     => "ALTER TABLE `oxarticles` ADD `OXCOMELASTICSTAT_1` TINYINT( 1 ) UNSIGNED NOT NULL default '0';"
        "OXCOMELASTICSTAT_2"     => "ALTER TABLE `oxarticles` ADD `OXCOMELASTICSTAT_2` TINYINT( 1 ) UNSIGNED NOT NULL default '0';"
        "OXCOMELASTICSTAT_3"     => "ALTER TABLE `oxarticles` ADD `OXCOMELASTICSTAT_3` TINYINT( 1 ) UNSIGNED NOT NULL default '0';"
    );
    
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // Create all tables and fields
        self::setupModule();
        
        // Regeneration of views and cache
        //self::regenerateViews(); - at this state not possible! argh
        self::clearCache();
    }
     /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
        // Regeneration of views and cache
        self::regenerateViews();
        self::clearCache();
    }
    
    /**
     * Execute the sql at the first time of the module installation.
     */
    private static function setupModule()
    {
        // Check if oxarticles table has all needed fields, if not add them to the table.
        foreach (self::$_aSetupOxcomOxarticleSQLs as $sField => $sSql) {
            if (!self::fieldExists($sField, 'oxarticles')) {
                self::executeSQL($sSql);
            }
        }
    }
    
    /**
     * Regenerate views for changed tables
     */
    protected static function regenerateViews()
    {
        $oDbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $oDbMetaDataHandler->updateViews();
    }
    
    /**
     * Empty cache
     */
    private static function clearCache()
    {
        /** @var oxUtilsView $oUtilsView */
        $oUtilsView = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\UtilsView::class);
        $sSmartyDir = $oUtilsView->getSmartyDir();
        if( $sSmartyDir && is_readable( $sSmartyDir ) )
        {
            foreach( glob( $sSmartyDir . '*' ) as $sFile )
            {
                if ( !is_dir( $sFile ) )
                {
                    @unlink( $sFile );
                }
            }
        }
        // Initialise Smarty
        $oUtilsView->getSmarty( true );
    }
    
    /**
     * Check if table exists
     *
     * @param string $sTableName table name
     *
     * @return bool
     */
    protected static function tableExists($sTableName)
    {
        $oDbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        return $oDbMetaDataHandler->tableExists($sTableName);
    }
    
    /**
     * Check if field exists in table
     *
     * @param string $sFieldName field name
     * @param string $sTableName table name
     *
     * @return bool
     */
    protected static function fieldExists($sFieldName, $sTableName)
    {
        $oDbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        return $oDbMetaDataHandler->fieldExists($sFieldName, $sTableName);
    }
    
    /**
     * Executes given sql statement.
     *
     * @param string $sSQL Sql to execute.
     */
    private static function executeSQL( $sSQL )
    {
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute( $sSQL );
    }
 
}
