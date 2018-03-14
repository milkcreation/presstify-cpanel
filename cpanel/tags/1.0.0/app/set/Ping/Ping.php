<?php
/*
 Plugin Name: Ping
 Plugin URI: http://presstify.com/plugins/ping
 Description: Serveur d'hébergement
 Version: 1.1.161212
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 */

namespace tiFy\Plugins\CPanel\App\Set\Ping;

class Ping extends \tiFy\App\Set
{
    /**
     * Liste des actions à déclencher
     */
    protected $tFyAppActions            = array(
        'tify_db_register',
        'tify_templates_register'
    );

    /**
     * Base de données
     */
    private static $Db;

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration de la table de base de données
     */
    public function tify_db_register()
    {
        self::$Db = tify_db_register( 
            'pingsite', 
            array( 
                'install'           => true,
                'col_prefix'        => 'pingsite_',
                'meta'              => true,
                'columns'           => array(
                    'id'                => array(
                        'type'              => 'BIGINT',
                        'size'              => 20,
                        'unsigned'          => true,
                        'auto_increment'    =>true
                    ),
                    'domain'            => array(
                        'type'              => 'VARCHAR',
                        'size'              => 255
                    ),
                    'subdomain'         => array(
                        'type'              => 'VARCHAR',
                        'size'              => 255,
                        'default'           => '@'
                    ),
                    'protocol'          => array(
                        'type'              => 'VARCHAR',
                        'size'              => 10,
                        'default'           => 'http'
                    ),
                    'active'            => array(
                        'type'              => 'TINYINT',
                        'size'              => 1,
                        'default'           => 0
                    ),
                    'httpcode'          => array(
                        'type'              => 'INT',
                        'size'              => 3,
                        'default'           => 0
                    ),
                    'coderepeat'        => array(
                        'type'              => 'BIGINT',
                        'size'              => 20,
                        'default'           => 0
                    ),
                    'response'          => array(
                        'type'              => 'VARCHAR',
                        'size'              => 5,
                    ),
                    'check_datetime'    => array(
                        'type'              => 'DATETIME',
                        'default'           => '0000-00-00 00:00:00'
                    ),
                    'error_timestamp'   => array(
                        'type'              => 'INT',
                        'size'              => 13
                    ),                    
                    'notice_timestamp'  => array(
                        'type'              => 'INT',
                        'size'              => 13
                    )
                ),
                'sql_engine'    => new \wpdb( EXTDB_USER, EXTDB_PASSWORD, EXTDB_NAME, EXTDB_HOST ),
                'search'        => array( 'domain', 'subdomain' )
            )
        );
    }

    /**
     * Déclaration des templates
     */
    public function tify_templates_register()
    {
        tify_templates_register( 
            'tiFyPluginCPanelPingListTable', 
            array(
                'route'             => 'Ping/List', 
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Ping\ListTable\ListTable',
                'db'                => 'pingsite',
                'edit_template'     => 'tiFyPluginCPanelPingEditForm'
            ),
            'front'
        );

        tify_templates_register( 
            'tiFyPluginCPanelPingEditForm', 
            array(
                'route'             => 'Ping/Edit', 
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Ping\EditForm\EditForm',
                'db'                => 'pingsite',
                'list_template'     => 'tiFyPluginCPanelPingListTable'
            ),
            'front'
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Retourne le nombre de sites indisponibles
     */
    public static function countUnavailables()
    {        
        return (int) self::$Db->sql( )->get_var( "SELECT COUNT(pingsite_id) FROM ". self::$Db->getName() ." WHERE pingsite_error_timestamp > 0" );        
    }
}