<?php
namespace tiFy\Plugins\CPanel\App\Set\Server\EditForm;

class EditForm extends \tiFy\Core\Templates\Front\Model\EditForm\EditForm
{	
	/* = PARAMETRES = */
	/** == Définition des champs de formulaire == **/
	public function set_fields()
	{
		return array(
			'server_title'		=> __( 'Intitulé du serveur', 'tify' ),
			'ipv4'				=> __( 'Interface IPV4', 'tify' ),
			'hosting_basedir'	=> __( 'Répertoire de stockage des hébergements de site web', 'tify' ),
			'conf_basedir'		=> __( 'Répertoire de stockage de la configuration', 'tify' ),
		);
	}
	
	/* = AFFICHAGE = */
	/** == Rendu == **/
	public function render()
	{
		get_header();
	?>
		<div class="wrap">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h2 class="page-header">
							<?php echo $this->label( 'edit_item' );?>
							<?php if( $this->NewItem ) : ?>
							&nbsp;<a class="btn btn-default" href="<?php echo $this->BaseUri;?>"><?php echo $this->label( 'new_item' );?></a>
							<?php endif;?>
						</h2>
					</div>
				</div>
                
                <?php $this->notices();?>
                			
				<form method="post">
					<?php $this->hidden_fields();?>
					<div class="row">
						<div class="col-lg-9">			
							<?php $this->form();?>							
						</div>
				
						<div class="col-lg-3">
							<?php $this->submitdiv();?>
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php
		get_footer();
		exit;
	}
	
	/** == Champ - Titre == **/
	public function field_server_title( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Nom du serveur', 'tify' );?>" name="server_title" value="<?php echo $item->server_title;?>">
		</div>
	<?php
	}
	
	/** == Champ - IPV4 du serveur == **/
	public function field_ipv4( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( '000.000.000.000', 'tify' );?>" name="item_meta[ipv4]" value="<?php echo $this->get_meta( 'ipv4' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Répertoire de stockage des hébergements de site web == **/
	public function field_hosting_basedir( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( '/srv/.../var/html', 'tify' );?>" name="item_meta[hosting_basedir]" value="<?php echo $this->get_meta( 'hosting_basedir' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Répertoire de stockage de la configuration == **/
	public function field_conf_basedir( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( '/srv/.../etc', 'tify' );?>" name="item_meta[conf_basedir]" value="<?php echo $this->get_meta( 'conf_basedir' );?>">
		</div>
	<?php
	}
}