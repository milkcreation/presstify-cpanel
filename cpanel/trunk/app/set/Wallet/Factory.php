<?php 
namespace tiFy\Plugins\CPanel\App\Set\Wallet;

use tiFy\Core\Templates\Front\Front;

class Factory extends \tiFy\App\Factory
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
     * Identifiant
     */
    protected $WalletId                 = null;

    /**
     * Attributs du template de vue liste
     */
    protected $ListTable                = array();

    /**
     * Attributs du template de vue édition
     */
    protected $EditForm                 = array();

    /**
     * Clé secrète de hashage des données
     */
    protected static $SecretKey         = null;

    /**
     * CONSTRUCTEUR
     */
    public function __construct($id, $params = array())
    {
        parent::__construct();
        
        $this->WalletId = $id;

        // Traitement des templates
        $templates = array('edit', 'list');
        foreach($params as $template => $attrs) :
            if(! in_array($template, $templates))
                continue;
            
            switch($template) :
                case 'list' :
                    $this->ListTable = wp_parse_args(
                        $attrs,
                        array(
                            'route'         => "Wallet/{$this->WalletId}/ListTable",
                            'cb'            => 'tiFy\Plugins\CPanel\App\Set\Wallet\Admin\ListTable\ListTable',
                            'db'            => 'wallet',
                            'edit_template' => "tiFyPluginCPanelWallet{$this->WalletId}EditForm"
                        )
                    );
                    break;
                    
                case 'edit' :
                    $this->EditForm = wp_parse_args(
                        $attrs,
                        array(
                            'route'         => "Wallet/{$this->WalletId}/EditForm",
                            'cb'            => 'tiFy\Plugins\Wallet\Admin\EditForm\EditForm',
                            'db'            => 'wallet',
                            'list_template' => "tiFyPluginCPanelWallet{$this->WalletId}ListTable"
                        )
                    );
                    break;
            endswitch;
        endforeach;

        static::$SecretKey = ! empty( $params['secret_key'] ) ? $params['secret_key'] : SECURE_AUTH_SALT;
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration des templates
     */
    final public function tify_templates_register()
    {
        tify_templates_register( 
            "tiFyPluginCPanelWallet{$this->WalletId}ListTable", 
            $this->ListTable,
            'front'
        );

        tify_templates_register( 
            "tiFyPluginCPanelWallet{$this->WalletId}EditForm", 
            $this->EditForm,
            'front'
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Récupération de la clé secrète de hashage
     */
    final protected static function getSecretKey()
    {
        return static::$SecretKey;
    }
}