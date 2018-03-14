<?php
namespace tiFy\Plugins\CPanel\App\Set\Ping\EditForm;

class EditForm extends \tiFy\Core\Templates\Front\Model\EditForm\EditForm
{	
	/* = PARAMETRES = */
	/** == Définition des champs de formulaire == **/
	public function set_fields()
	{
		return array(
			'url' => __( 'Url du site', 'tify' )
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
				<form method="post" class="form-inline">
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
	public function field_url( $item )
	{
	?>
		<div class="form-group">
			<select class="form-control" name="pingsite_protocol">
				<option value="http">http</option>
				<option value="https">https</option>
			</select>
			://
			<input type="text" class="form-control" placeholder="<?php _e( 'Sous domaine (@, www)', 'tify' );?>" name="pingsite_subdomain" value="<?php echo $item->pingsite_subdomain;?>">
			<input type="text" class="form-control" placeholder="<?php _e( 'Nom de domaine', 'tify' );?>" name="pingsite_domain" value="<?php echo $item->pingsite_domain;?>">
		</div>
	<?php
	}
	
	/** == Champ - IPV4 du serveur == **/
	public function field_ipv4( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Interface IPV4', 'tify' );?>" name="item_meta[ipv4]" value="<?php echo $this->get_meta( 'ipv4' );?>">
		</div>
	<?php
	}
}