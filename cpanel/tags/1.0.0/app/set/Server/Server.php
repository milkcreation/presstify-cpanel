<?php
/*
 Plugin Name: Server
 Plugin URI: http://presstify.com/plugins/hosting
 Description: Serveur d'hébergement
 Version: 1.1.161209
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 */

namespace tiFy\Plugins\CPanel\App\Set\Server;

class Server extends \tiFy\App\Set
{
    /* = ARGUMENTS = */
    // Liste des actions à déclencher
    protected $tFyAppActions            = array(
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
            'server', 
            array( 
                'install'        => true,
                'col_prefix'    => 'server_',
                'meta'            => true,
                'columns'        => array(
                    'id'                => array(
                          'type'                => 'BIGINT',
                           'size'                => 20,
                        'unsigned'            => true,
                        'auto_increment'    =>true
                    ),
                    'title'                => array(
                        'type'                => 'VARCHAR',
                        'size'                => 255
                    ),
                    'created'            => array(
                        'type'                => 'DATETIME',
                        'default'            => '0000-00-00 00:00:00'
                    ),
                    'modified'            => array(
                        'type'                => 'DATETIME',
                        'default'            => '0000-00-00 00:00:00'
                    ),
                    'status'            => array(
                        'type'                => 'VARCHAR',
                        'size'                => 20,
                        'default'            => 'publish'
                    )
                ),
                'sql_engine'    => new \wpdb( EXTDB_USER, EXTDB_PASSWORD, EXTDB_NAME, EXTDB_HOST )
            )
        );
    }
    
    /** == == **/
    public function tify_templates_register()
    {
        tify_templates_register( 
            'tiFyPluginCPanelServerListTable', 
            array(
                'route'             => 'Server/List', 
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Server\ListTable\ListTable',
                'db'                => 'server',                    
                'edit_template'     => 'tiFyPluginCPanelServerEditForm'    
            ),
            'front'
        );
        
        tify_templates_register( 
            'tiFyPluginCPanelServerEditForm', 
            array(
                'route'             => 'Server/Edit', 
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Server\EditForm\EditForm',
                'db'                => 'server',
                'list_template'     => 'tiFyPluginCPanelServerListTable'
            ),
            'front'
        );
    }
    
    /* = CONTROLEURS = */
    /** == Récupération de la liste des serveurs == **/
    public static function getList( $query_args = array() )
    {
        $defaults = array(
            
        );
        $query_args = wp_parse_args( $query_args, $defaults );
        
        return self::$Db->select()->rows( $query_args );
    }
            
    /** == Récupère le nom d'un serveur == **/
    public static function getTitle( $id )
    {        
        return self::$Db->select()->cell_by_id( $id, 'title' );
    }
    
    /** == Récupère le répertoire de stockage des hébergement de site web == **/
    public static function getHostingDir( $id )
    {        
        return self::$Db->meta()->get( $id, 'hosting_basedir', true );
    }
    
    /** == Récupère le répertoire de stockage de la configuration == **/
    public static function getConfDir( $id )
    {        
        return self::$Db->meta()->get( $id, 'conf_basedir', true );
    }
}