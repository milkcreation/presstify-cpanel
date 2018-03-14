<?php
namespace tiFy\Plugins\CPanel\MustUse;

use tiFy\Set;

class Sets extends \tiFy\App\Factory
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions            = array(
        'tify_set_load',
        'tify_set_register'
    ); 
    
    /**
     * Enregistrement
     * 
     * @return void
     */
    final public function tify_set_load()
    {
        Set::load(
            'tFyPluginCPanelContacts',
            array(
                // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Contacts',
                // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Contacts',
                // Nom de la classe d'initialisation
                'bootstrap'         => 'Contacts'
            )
        );
        Set::load(
            'DnsChecker',
            array(
                // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\DnsChecker',
                // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/DnsChecker'    
            )
        );
        Set::load(
            'DynamicDocs',
            array(
                // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\DynamicDocs',
                // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/DynamicDocs'
            )
        );
        Set::load(
            'Ping',
            array(
                // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Ping',
                // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Ping'
            )
        );
        Set::load(
            'Redirect301',
            array(
                // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Redirect301',
                // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Redirect301'
            )
        );
        Set::load(
            'Server',
            array(
                  // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Server',
                   // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Server'
            )
        );
        Set::load(
            'Wallet',
            array(
                  // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Wallet',
                   // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Wallet'
            )
        );
        Set::load(
            'Website',
            array(
                  // Espace de nom du jeu de fonctionnalités
                'namespace'         => 'tiFy\Plugins\CPanel\App\Set\Website',
                   // Répertoire de stockage du jeu de fonctionnalités
                'base_dir'          => dirname(dirname(__FILE__)) . '/app/set/Website'
            )
        );
    }
    
    /**
     * Déclaration
     * 
     * @param string $id
     * 
     * @return object
     */
    public static function tify_set_register()
    {
        Set::register('tFyPluginCPanelContacts');
        Set::register('DnsChecker');
        Set::register('DynamicDocs');
        Set::register('Ping');
        Set::register('Redirect301');
        Set::register('Server');
        Set::register('Wallet');
        Set::register('Website');
    }
}
new Sets;