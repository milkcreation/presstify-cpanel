<?php
namespace tiFy\Plugins\CPanel\App\Set\Website\ListTable;

use tiFy\Plugins\CPanel\App\Set\Server\Server;
use tiFy\Plugins\CPanel\App\Set\Server\GeneralTemplate;

class ListTable extends \tiFy\Core\Templates\Front\Model\ListTable\ListTable
{
	/* = PARAMETRES = */			
	/** == Définition des colonnes de la table == **/
	public function set_columns()
	{
		return array(
			'title'					=> __( 'Nom du site', 'tify' ),
		    'domains'				=> __( 'Noms de domaine', 'tify' ),
			'urls'					=> __( 'Accès au site', 'tify' ),			
			'server'				=> __( 'Serveur', 'tify' )					
		);
	}
	
	/** == Définition des actions sur un élément == **/
	public function set_row_actions()
	{
		return array( 'edit', 'trash', 'untrash', 'delete'  );
	}
	
	/** == Définition de l'ajout automatique des actions de l'élément pour la colonne principale == **/
	public function set_handle_row_actions()
	{
		return false;
	}
	
	/** == Définition des colonnes pouvant être ordonnées == **/
	public function set_sortable_columns()
	{
		return array( 
			'title' 	=> 'website_title',
			//'domains' 	=> 'primary_domain'
		);
	}
	
	/** == Définition des arguments de requête == **/
	public function set_query_args()
	{		
		$query_args = array();
		
		if( ! empty( $_GET['server'] ) ) :
			$query_args['meta_query'][] = array(
				'relation'	=> 'OR',
				array(
					'key'	=> 'server',
					'value'	=> $_GET['server']
				)
			);
		endif;
		
		if( ! empty( $_GET['s'] ) ) :
			$query_args['meta_query'] = array(
				'relation'	=> 'OR',
				array(
					'key'		=> 'primary_domain',
					'value'		=> $_GET['s'],
					'compare'	=> 'LIKE'
				)
			);
		endif;
			
		return $query_args;
	}
	
	/* = AFFICHAGE = */
	/** == Liste de filtrage du formulaire courant == **/
	public function extra_tablenav( $which ) 
	{			
		$output = "<div class=\"alignleft actions\">";
		if ( 'top' == $which ) :
			$selected = ! empty( $_GET['server'] ) ? $_GET['server'] : '';
			$output  .= GeneralTemplate::dropdown( 
				array( 
					'name' 				=> 'server', 
					'selected'			=> $selected,
					'show_option_none'	=> __( 'Tous les serveurs', 'tify' )
				), 
				false 
			);
			$output  .= "<input type=\"submit\" name=\"\" class=\"btn btn-default\" value=\"". __( 'Filter', 'tify' ) ."\">";
		endif;
		$output .= "</div>";

		echo $output;
	}
	
	/** == Rendu == **/
	public function render()
	{		
		get_header();
	?>
		<div id="tiFyPluginsWebsite-List">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="page-header">
							<h1>
								<?php _e( 'Hébergements de site internet', 'tify' );?>
								
								<?php if( $this->EditBaseUri ) : ?>
	    						<a class="btn btn-default" href="<?php echo $this->EditBaseUri;?>"><?php echo $this->label( 'add_new' );?></a>
	    						<?php endif;?>
							</h1>
							
							<?php $this->notices();?>
							
						</div>	
					</div>						
				</div>
				<div class="row">
					<div class="col-lg-12">
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
	
	/** == Contenu de la colonne - Hôte == **/
	public function column_title( $item )
	{
		$title = ! $item->website_title ? __( '(Pas de nom)', 'tify' ) : $item->website_title;			
		$label = '';		
		
		// Définition des actions sur l'élément
		if( $item->website_status !== 'trash' ) :
			$row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'trash' ) ) );
		else :
			$label = ' - '.__( 'à la corbeille', 'tify' );
			$row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'untrash', 'delete' ) ) );
		endif;
		
		return sprintf('<strong><a href="%2$s">%1$s</a> %3$s</strong>%4$s', $title, $this->get_edit_uri( $item->website_id ), $label, $row_actions );    
	}
	
	/** == Contenu de la colonne - Date de mise en ligne == **/
	public function column_urls( $item )
	{
		$output = "";
		$fo_url = $this->db()->meta()->get( $item->website_id, 'fo_url', true );
		$bo_url = $this->db()->meta()->get( $item->website_id, 'bo_url', true );
		
		if( ! $fo_url && ! $bo_url )
			return $output;
		$output .= "<ul>";
		if( $fo_url )
			$output .= "<li><a href=\"{$fo_url}\" title=\"". __( 'Accès à l\'interface utilisateur du site', 'tify' ) ."\" target=\"_blank\">{$fo_url}</a></li>";
		if( $bo_url )
			$output .= "<li><a href=\"{$bo_url}\" title=\"". __( 'Accès à l\'interface utilisateur du site', 'tify' ) ."\" target=\"_blank\" >{$bo_url}</a></li>";
		$output .= "</ul>";
			
		return $output;			
	}
	
	/** == Contenu de la colonne - Date de mise en ligne == **/
	public function column_online_date( $item )
	{
		return ( ( $date = $this->db()->meta()->get( $item->website_id, 'online_date', true ) ) && ( $date != '0000-00-00' ) ) ? mysql2date( 'd m Y', $date ) : '--' ;			  
	}
	
	/** == Contenu de la colonne - Nom de domaines == **/
	public function column_domains( $item )
	{
		return $this->db()->meta()->get( $item->website_id, 'primary_domain', true );
	}
	
	/** == Contenu de la colonne - Serveur d'hébergement == **/
	public function column_server( $item )
	{
		if( $id = (int) $this->db()->meta()->get( $item->website_id, 'server', true ) )
			return Server::getTitle($id);
	}
}