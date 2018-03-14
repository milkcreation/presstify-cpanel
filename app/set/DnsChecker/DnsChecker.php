<?php
namespace tiFy\Plugins\CPanel\App\Set\DnsChecker;

use tiFy\Core\Templates\Templates;

class DnsChecker extends \tiFy\App\Set
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions            = array(
        'tify_templates_register'
    ); 
    
    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration de gabarit
     * 
     * @return void
     */
    public function tify_templates_register()
    {
        Templates::register(
            'tiFySetDnsChecker',
            array(
                'route'         => 'DnsChecker', 
                'cb'            => 'tiFy\Plugins\CPanel\App\Set\DnsChecker\Template',
                'db'            => null
            ),
            'front'
        );
    }
}