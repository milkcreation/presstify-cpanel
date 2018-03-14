<?php
namespace tiFy\Plugins\CPanel\App\Set\Server\ListTable;

class ListTable extends \tiFy\Core\Templates\Front\Model\ListTable\ListTable
{
	/* = PARAMETRES = */			
	/** == Définition des colonnes de la table == **/
	public function set_columns()
	{
		return array(
			'server_title'		=> __( 'Nom du serveur', 'tify' ),
			'interfaces'		=> __( 'Interfaces', 'tify' ),
			'directories'		=> __( 'Répertoires de stockage', 'tify' )
		);
	}
	
	/** == Définition des arguments de requête == **/
	public function set_query_args()
	{		
		$query_args = array();
		
	    if( ! empty( $_REQUEST['orderby'] ) ) :
			$query_args['orderby'] = 'title';
		      $query_args['order'] = 'ASC';
		endif;
		
		return $query_args;	
	}
	
	/** == Définition des colonnes pouvant être ordonnées == **/
	public function set_sortable_columns()
	{
		return array( 'server_title' => 'title' );
	}
	
	/** == Définition des actions sur un élément == **/
	public function set_row_actions()
	{
		return array( 'edit', 'trash', 'untrash', 'delete' );
	}
	
	/** == Définition de l'ajout automatique des actions de l'élément pour la colonne principale == **/
	public function set_handle_row_actions()
	{
		return false;
	}
	
	/* = AFFICHAGE = */
	/** == Rendu == **/
	public function render()
	{		
		get_header();
	?>
		<div id="tiFyPluginsServer-ListTable">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="page-header">
							<h1>
								<?php _e( 'Serveurs d\'hébergement', 'tify' );?>
								
								<?php if( $this->EditBaseUri ) : ?>
	    						<a class="btn btn-default" href="<?php echo $this->EditBaseUri;?>"><?php echo $this->label( 'add_new' );?></a>
	    						<?php endif;?>
							</h1>
						</div>	
					</div>						
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title"><?php _e( 'Liste des serveurs', 'tify' );?></h3>
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
	public function column_server_title( $item )
	{
		$title = ! $item->server_title ? __( '(Pas de nom)', 'tify' ) : $item->server_title;			
		$label = '';		
		
		// Définition des actions sur l'élément
		if( $item->server_status !== 'trash' ) :
			$row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'trash' ) ) );
		else :
			$label = ' - '.__( 'à la corbeille', 'tify' );
			$row_actions = $this->row_actions( $this->item_row_actions(  $item, array( 'edit', 'untrash', 'delete' ) ) );
		endif;
		
		return sprintf('<strong><a href="%2$s">%1$s</a> %3$s</strong>%4$s', $title, $this->get_edit_uri( $item->server_id ), $label, $row_actions );    
	}
	
	/** == Contenu de la colonne - Interfaces == **/
	public function column_interfaces( $item )
	{
		return $this->db()->meta()->get( $item->server_id, 'ipv4' );
	}
	
	/** == Contenu de la colonne - Répertoires de stockage == **/
	public function column_directories( $item )
	{
	?>
		<ul style="margin:0;padding:0;list-style-type:none;">
			<li style="margin:0;padding:0;">
				<label style="margin:0;padding:0;"><?php _e( 'Hébergements de site web :', 'tify' );?></label><br>
				<?php echo ( $dir = $this->db()->meta()->get( $item->server_id, 'hosting_basedir' ) ) ? $dir : '--';?>
			</li>
			<li style="margin:0;padding:0;">
				<label style="margin:0;padding:0;"><?php _e( 'Configuration :', 'tify' );?></label><br>
				<?php echo ( $dir = $this->db()->meta()->get( $item->server_id, 'conf_basedir' ) ) ? $dir: '--';?>
			</li>
		</ul>
	<?php	
	}
}