<?php
/*
 Plugin Name: Website
 Plugin URI: http://presstify.com/plugins/hosting
 Description: <p>Hébergements de sites web</p>
 Version: 1.1.161209
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 */

namespace tiFy\Plugins\CPanel\App\Set\Website;

class Website extends \tiFy\App\Set
{
    /* = ARGUMENTS = */
    // Liste des actions à déclencher
    protected $tFyAppActions = array(
        'tify_db_register',
        'tify_templates_register'
    );

    // Base de données
    private static $Db;

    /* = CONSTRUCTEUR = */
    public function __construct()
    {
        parent::__construct();

        include self::tFyAppDirname() . '/Helpers.php';
    }

    /* = DECLENCHEURS = */
    /** == Déclaration de la table de base de données == **/
    public function tify_db_register()
    {
        self::$Db = tify_db_register(
            'website',
            array(
                'install' => true,
                'col_prefix' => 'website_',
                'meta' => true,
                'columns' => array(
                    'id' => array(
                        'type' => 'BIGINT',
                        'size' => 20,
                        'unsigned' => true,
                        'auto_increment' => true
                    ),
                    'title' => array(
                        'type' => 'VARCHAR',
                        'size' => 255
                    ),
                    'created' => array(
                        'type' => 'DATETIME',
                        'default' => '0000-00-00 00:00:00'
                    ),
                    'modified' => array(
                        'type' => 'DATETIME',
                        'default' => '0000-00-00 00:00:00'
                    ),
                    'status' => array(
                        'type' => 'VARCHAR',
                        'size' => 20,
                        'default' => 'publish'
                    )
                ),
                'sql_engine' => new \wpdb(EXTDB_USER, EXTDB_PASSWORD, EXTDB_NAME, EXTDB_HOST),
            )
        );
    }

    /** == == **/
    public function tify_templates_register()
    {
        tify_templates_register(
            'tiFyPluginCPanelWebsiteListTable',
            array(
                'route' => 'Website/List',
                'cb' => 'tiFy\Plugins\CPanel\App\Set\Website\ListTable\ListTable',
                'db' => 'website',

                'edit_template' => 'tiFyPluginCPanelWebsiteEditForm'
            ),
            'front'
        );

        tify_templates_register(
            'tiFyPluginCPanelWebsiteEditForm',
            array(
                'route' => 'Website/Edit',
                'cb' => 'tiFy\Plugins\CPanel\App\Set\Website\EditForm\EditForm',
                'db' => 'website',
                'list_template' => 'tiFyPluginCPanelWebsiteListTable'
            ),
            'front'
        );
    }
}	
