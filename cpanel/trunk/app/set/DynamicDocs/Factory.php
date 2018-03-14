<?php
namespace tiFy\Plugins\CPanel\App\Set\DynamicDocs;

use tiFy\Plugins\CPanel\App\Set\DynamicDocs\DynamicDocs;

abstract class Factory extends \tiFy\App\Factory
{
    /**
     * Identifiant du cookie de transport des données
     * @var string
     */
    protected $CookieName                   = null;

    /**
     * Expiration du cookie de transport des données
     * @var int
     */
    protected $CookieExpire                 = HOUR_IN_SECONDS;

    /**
     * Variables d'environnement declarées
     * @var array
     */
    protected $MergeVars                    = [];

    /**
     * Variables d'environnement de la requête
     * @var array
     */
    protected $RequestVars                  = [];

    /**
     * Liste des variables d'environnement déjà filtrée
     * @var array
     */
    protected $FilteredVars                 = [];
    
    /**
     * Variables d'environnement traité
     * @var array
     */
    protected $Vars                         = [];
    
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des variables d'environnement
        $this->MergeVars = $this->registerMergeVars();

        // Définition de l'identifiant de cookie
        $this->CookieName = 'tiFyDynamicDocs_' . md5(get_called_class());

        // Action après le chargement complet de Wordpress
        $this->tFyAppActionAdd('wp_loaded');
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Chargement de l'écran courant
     *
     * @return void
     */
    public function current_screen()
    {
        DynamicDocs::$Current = $this;
    }
    
    /**
     * Après le chargement complet de Wordpress
     */
    final public function wp_loaded()
    {
        $this->_parseRequestVars();
        $this->_filterVars();
        $this->_setCookie();
    }
    
    /**
     * Mise en file des scripts
     */
    public function wp_enqueue_scripts()
    {
        wp_register_style( 'prettify-desert', '//cdn.rawgit.com/google/code-prettify/master/loader/skins/desert.css', array(), '161021' );
        wp_enqueue_style( 'tiFyPluginDynamicDocs', self::tFyAppUrl(get_class()) .'/DynamicDocs.css', array( 'prettify-desert' ), '161021' );
        
        wp_register_script( 'prettify', '//cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js', array(), '161021', true );
        wp_enqueue_script( 'tiFyPluginDynamicDocs', self::tFyAppUrl(get_class()) .'/DynamicDocs.js', array( 'jquery', 'prettify' ), '161021', true );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Traitement des données de requête
     */
    private function _parseRequestVars()
    {
        foreach( (array) $this->MergeVars as $var ) :
            if( ! isset( $_REQUEST[$var] ) ) 
                continue;
            $this->RequestVars[$var] = $_REQUEST[$var];
        endforeach;        
    }
    
    /**
     * Filtrage des variables d'environnement
     */
    private function _filterVars()
    {
        foreach( (array) $this->MergeVars as $var ) :
            $this->_filterVar( $var );
        endforeach;
    }
    
    /**
     * Filtrage d'une variable d'environnement
     */
    private function _filterVar( $var )
    {
        array_push( $this->FilteredVars, $var );
        $value = isset( $this->RequestVars[$var] ) ? $this->getRequestVar( $var ) : $this->getCookieVar( $var );

        return $this->Vars[$var] = $this->parseVar( $var, $value );
    }

    /**
     * Définition du Cookie
     */
    private function _setCookie()
    {
        setcookie( $this->CookieName, base64_encode( serialize( ' ' ) ), time() - $this->CookieExpire, SITECOOKIEPATH );
        setcookie( $this->CookieName, base64_encode( serialize( $this->getVars() ) ), time() + $this->CookieExpire, SITECOOKIEPATH );
    }

    /**
     * Récupération des données cookie
     */
    final protected function getCookieVars()
    {
        if( isset( $_COOKIE[ $this->CookieName ] ) ) :
            return unserialize( base64_decode( $_COOKIE[ $this->CookieName ] ) );
        else :
            return array();
        endif;
    }

    /**
     * Récupération d'une donnée de cookie 
     */
    final protected function getCookieVar( $var )
    {
        $cookieVars = $this->getCookieVars();
        
        if( isset( $cookieVars[$var] ) ) :
            return $cookieVars[$var];
        endif;
    }

    /**
     * Récupération des variables d'environnement de la requête
     * @return array
     */
    final protected function getRequestVars()
    {
        return $this->RequestVars;
    }

    /**
     * Récupération d'une valeur de variable d'environnement de la requete
     */
    final protected function getRequestVar( $var, $default = '' )
    {
        if( isset( $this->RequestVars[$var] ) )
            return $this->RequestVars[$var];

        return $default;
    }

    /**
     * Récupération des variables d'environnement
     * @return array
     */
    final public function getVars()
    {
        return $this->Vars;
    }
    
    /**
     * Récupération d'une valeur de variable d'environnement
     */
    final public function getVar( $var, $default = '' )
    {
        if( in_array( $var, $this->MergeVars ) ) :
            if( ! in_array( $var, $this->FilteredVars ) ) :
                return $this->_filterVar( $var );
            else :
                return $this->Vars[$var];
            endif;
        endif;

        return $default;
    }
    
    /**
     * Déclaration des variables d'environnement
     */
    abstract protected function registerMergeVars();

    /**
     * Traitement d'une variable d'environnement
     */
    final public static function parseMergeVar($var)
    {
        return \tiFy\Lib\Chars::mergeVars($output, $this->Vars);
    }
    
    /**
     * SURCHARGE
     */
    /**
     * Traitement de la valeur d'une variable d'environnement
     */
    public function parseVar( $key, $value )
    {
        switch( $key ) :
            default :
                return $value;
                break;
        endswitch;
    }
   
    /**
     * Pré-traitement du rendu de la page
     */
    public function _render()
    {
        ob_start();
        $this->render();
        $output = ob_get_clean();

        echo \tiFy\Lib\Chars::mergeVars($output, $this->Vars);
    }
    
    /**
     * Rendu de la page
     */
    public function render(){ }
}