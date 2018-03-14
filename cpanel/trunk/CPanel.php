<?php
/**
 * @name Cpanel
 * @package PresstiFy
 * @subpackage Plugins
 * @namespace tiFy\Plugins\CPanel
 * @desc Gestion Hébergement
 * @author Jordy Manner
 * @copyright Milkcreation
 * @version 1.0.0
 */
namespace tiFy\Plugins\CPanel;

use tiFy\App\Plugin;

class CPanel extends \tiFy\App\Plugin
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions            = array(
        'init'
    );
    
    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale
     * 
     * @return void
     */
    final public function init()
    {
        $sets = self::__tFyAppGetConfig('set', array());
    }
}
