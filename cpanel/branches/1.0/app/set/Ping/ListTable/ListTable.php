<?php
namespace tiFy\Plugins\CPanel\App\Set\Ping\ListTable;

use tiFy\Plugins\CPanel\App\Set\Ping\Ping;

class ListTable extends \tiFy\Core\Templates\Front\Model\ListTable\ListTable
{
    /**
     * PARAMETRES
     */
    /**
     * Définition des vues filtrées
     */
    public function set_views()
    {
        return array(
            array(
                'label'             => __( 'Tous', 'tify' ),
                'count'             => $this->count_items()
            ),
            array(
                'label'             => array(
                    'plural'            => __( 'Indisponibles', 'tify' ),
                    'singular'          => __( 'Indisponible', 'tify' ),                    
                ),
                'hide_empty'        => true,
                'add_query_args'    => array( 'unavailable' => true ),
                'count'                 => Ping::countUnavailables()
            ),
            array(
                'label'             => array(
                    'plural'            => __( 'Actifs', 'tify' ),
                    'singular'          => __( 'Actif', 'tify' ),
                    
                ),
                'add_query_args'    => array( 'active' => 1 ),
                'count'             => $this->count_items( array( 'active' => 1 ) )
            ),
            array(
                'label'             => array(
                    'plural'            => __( 'Désactivés', 'tify' ),
                    'singular'          => __( 'Désactivé', 'tify' )
                ),
                'add_query_args'    => array( 'active' => 0 ),
                'count'             => $this->count_items( array( 'active' => 0 ) )
            ),
        );
    }

    /**
     * Définition des colonnes de la table
     */
    public function set_columns()
    {
        return array(
            'url'            => __( 'Url du site', 'tify' ),
            'access'        => __( 'Accès', 'tify' )
        );
    }

    /**
     * Définition des colonnes pouvant être ordonnées
     */
    public function set_sortable_columns()
    {
        return array( 'url' => 'pingsite_domain' );
    }

    /**
     * Définition des actions sur un élément
     */
    public function set_row_actions()
    {
        return array( 
            'edit', 
            'activate', 
            'deactivate',
            'delete'
        );
    }

    /**
     * Définition de l'ajout automatique des actions de l'élément pour la colonne principale
     */
    public function set_handle_row_actions()
    {
        return false;
    }

    /**
     * TRAITEMENT
     */
    /**
     * Éxecution de l'action - Désactivation
     */
    public function process_bulk_action_deactivate()
    {
        $item_ids = $this->current_item();

        // Vérification des permissions d'accès
        if( ! wp_verify_nonce( @$_REQUEST['_wpnonce'], 'bulk-'. $this->Plural ) ) :
            check_admin_referer( $this->get_item_nonce_action( 'deactivate', reset( $item_ids ) ) );
        endif;
        
        // Bypass
        if( ! $this->db()->isCol( 'active' ) )
            return;

        // Traitement de l'élément
        foreach( (array) $item_ids as $item_id ) :
            /// Modification du statut
            $this->db()->handle()->update( $item_id, array( 'active' => 0, 'error_timestamp' => 0, 'notice_timestamp' => 0 ) );
        endforeach;

        // Traitement de la redirection
        $sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
        $sendback = add_query_arg( 'message', 'deactivated', $sendback );    

        wp_redirect( $sendback );
        exit;
    }

    /**
     * AFFICHAGE
     */
    /**
     * 
     */
    public function single_row( $item ) 
    {
        $class = "";
        
        if( $item->pingsite_error_timestamp ) :
            $class = 'highlighted-error';
        elseif( ! $item->pingsite_active ) :
            $class = 'highlighted-warning';
        endif;

    ?><tr class="<?php echo $class;?>"><?php $this->single_row_columns( $item );?></tr><?php
    }

    /**
     * Rendu
     */
    public function render()
    {        
        get_header();
    ?>
        <div id="tiFyPluginsPing-ListTable">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header">
                            <h1>
                                <?php _e( 'Surveillance d\'activité de site Web', 'tify' );?>
                                
                                <?php if( $this->EditBaseUri ) : ?>
                                <a class="btn btn-default" href="<?php echo $this->EditBaseUri;?>"><?php echo $this->label( 'add_new' );?></a>
                                <?php endif;?>
                            </h1>
                        </div>    
                    </div>                        
                </div>
                <div class="row">
                    <div class="col-lg-12">
                    
                        <?php $this->notices();?>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?php _e( 'Liste des sites', 'tify' );?></h3>
                            </div>
                            <div class="panel-body">
                                <?php $this->views(); ?>
            
                                <form method="get" action="<?php echo $this->getConfig( 'base_url' );?>">
                                    <div class="pull-right">
                                        <?php $this->search_box( $this->label( 'search_items' ), $this->template()->getID() );?>
                                    </div>
                                    <?php $this->display();?>
                                </form>                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>    
    <?php
        get_footer();    
        exit;
    }
    
    /**
     * Contenu de la colonne - Hôte
     */
    public function column_url( $item )
    {
        $protocol = ( in_array( $item->pingsite_protocol, array( 'http', 'https' ) ) ) ? $item->pingsite_protocol : '';
        $subdomain = ( $item->pingsite_subdomain === '@' ) ? '' : $item->pingsite_subdomain;
        
        $url = ( $protocol ? $protocol .':' : '' ) . '//'. ( $subdomain ? $subdomain .'.' : '' ) . $item->pingsite_domain;
        $label = '';
        
        // Définition des actions sur l'élément
        if( ! $item->pingsite_active ) :
            $label = ' - '.__( 'inactif', 'tify' );
            $row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'activate', 'delete' ) ) );
        else :
            
            $row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'deactivate' ) ) );
        endif;
        
        return sprintf('<strong><a href="%1$s" title="%3$s" target="blank">%1$s</a> %2$s</strong>%4$s', $url, $label, sprintf( __( 'Visiter %s', 'tify' ), $url ), $row_actions );   
    }
    
    /**
     * Contenu de la colonne - Accès
     */
    public function column_access( $item )
    {
        if( ! $item->pingsite_active ) :
            return '--';
        elseif( $item->pingsite_check_datetime === '0000-00-00 00:00:00' ) :
        else :
    ?>
        <ul style="padding:0; margin:0; list-style-type:none;">
            <li>
                <label><?php _e( 'Dernière visite', 'tify' );?></label>
                <span><?php echo mysql2date( 'd M Y à H:i', $item->pingsite_check_datetime );?></span>
            </li>
            <li>
                <label><?php _e( 'Temps d\'accès', 'tify' );?></label>
                <span>
                    <?php printf( __( '%s sec.', 'tify' ), $item->pingsite_response );?>
                </span>
            </li>
        </ul>
    <?php
        endif;
    }
}