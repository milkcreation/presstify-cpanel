<?php
namespace tiFy\Plugins\CPanel\App\Set\Wallet;

use tiFy\tiFy;

class Wallet extends \tiFy\App\Factory
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions            = array(
        'tify_db_register'
    ); 

    /**
     * Classe de rappel des types d'accès déclarés
     */
    private static $Factory             = array();

    /**
     * Base de données
     */
    private static $Db                  = null;    
    
    /**
     * CONSTRUCTEUR
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        foreach(self::tFyAppConfig() as $id => $attrs) :
            $path[] = self::getOverrideNamespace() . "\\Plugins\\CPanel\\App\\Set\\Wallet\\" . ucfirst($id) ."\\" .ucfirst($id);
            $className =  self::getOverride("tiFy\\Plugins\\CPanel\\App\\Set\\Wallet\\Factory", $path);
            self::$Factory[$id] = new $className($id, $attrs);
        endforeach;
    }    
    
    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration de la table de base de données
     * 
     * @return void
     */
    public function tify_db_register()
    {
        self::$Db = tify_db_register( 
            'wallet', 
            array( 
                'install'           => true,
                'col_prefix'        => 'wallet_',
                'meta'              => true,
                'columns'           => array(
                    'id'                => array(
                        'type'              => 'BIGINT',
                        'size'              => 20,
                        'unsigned'          => true,
                        'auto_increment'    =>true
                    ),
                    'type'              => array(
                        'type'              => 'VARCHAR',
                        'size'              => 255
                    ),
                    'user'              => array(
                        'type'              => 'VARCHAR',
                        'size'              => 255
                    ),
                    'pass'              => array(
                        'type'              => 'LONGTEXT'
                    ),
                    'salt'              => array(
                        'type'              => 'VARCHAR',
                        'size'              => 64
                    ),
                    'status'            => array(
                        'type'              => 'VARCHAR',
                        'size'              => 20,
                        'default'           => 'publish'
                    )
                ),
                'sql_engine'    => new \wpdb( EXTDB_USER, EXTDB_PASSWORD, EXTDB_NAME, EXTDB_HOST )
            )
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Récupération d'un portefeuille
     */
    final public static function get($id)
    {
        if(isset(self::$Factory[$id]))
            return self::$Factory[$id];
    }
    
    /**
     * Récupération de l'objet base de données
     */
    final public static function getDb()
    {
        return self::$Db;
    }
}