<?php
namespace tiFy\Plugins\CPanel\App\Set\Website\EditForm;

use tiFy\Plugins\CPanel\App\Set\Server\GeneralTemplate;

class EditForm extends \tiFy\Core\Templates\Front\Model\EditForm\EditForm
{	
	/* = PARAMETRES = */
	/** == Définition des champs de formulaire == **/
	public function set_fields()
	{
		return array(
			'website_title'			=> __( 'Nom du site', 'tify' ),
			'online_date'			=> __( 'Date de mise en ligne', 'tify' ),
			'offline_date'			=> __( 'Date de mise hors ligne', 'tify' ),
				
			'primary_domain'		=> __( 'Domaine principal', 'tify' ),
			//'secondary_domains'		=> __( 'Domaines secondaires', 'tify' ),			
				
			'server'				=> __( 'Serveur d\'hébergement', 'tify' ),
			'os_user'				=> __( 'Utilisateur système', 'tify' ),
			'os_group'				=> __( 'Groupe système', 'tify' ),	
			'httpdocs_dir'			=> __( 'Répertoire d\'installation du site', 'tify' ),			
			
			'engine'				=> __( 'Moteur de site', 'tify' ),
			'fo_url'				=> __( 'Url du front-office', 'tify' ),
			'bo_url'				=> __( 'Url du back-office', 'tify' ),	
				
			//'subscription'			=> __( 'Abonnements', 'tify' ),
		);
	}
	
	/* = DECLENCHEURS = */
	/** == Mise en file des scripts == **/
	public function wp_enqueue_scripts()
	{
		tify_control_enqueue( 'touch_time' );
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
						
						<?php $this->notices();?>
						
					</div>
				</div>			
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
	public function field_website_title( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Nom du site', 'tify' );?>" name="website_title" value="<?php echo $item->website_title;?>">
		</div>
	<?php
	}
	
	/** == Champ - Date de mise en ligne == **/
	public function field_online_date( $item )
	{
	?>
		<div class="form-group">
		<?php 
			tify_control_touch_time(
				array(
					'name'			=> 'item_meta[online_date]',
					'type'			=> 'date',
					'value'			=> ( $date = $this->get_meta( 'online_date' ) ) ? $date : '0000-00-00',	
					'show_none'		=> true,
				)	
			)
		?>
		</div>
	<?php
	}
	
	/** == Champ - Date de mise hors ligne == **/
	public function field_offline_date( $item )
	{
		$offline = $this->get_meta( 'offline_date' );
	?>		
		<div class="form-group">
			<label><input type="checkbox" <?php checked( ( $offline && ( $offline !== '0000-00-00' ) ) ,true, true);?> /> <?php _e( 'Mise hors ligne', 'tify' );?></label>
			<?php 
				tify_control_touch_time(
					array(
						'name'			=> 'item_meta[offline_date]',
						'type'			=> 'date',
						'value'			=> $offline ? $offline : '0000-00-00',	
						'show_none'		=> true,
					)	
				)
			?>
		</div>
	<?php
	}
	
	/** == Champ - Domaine principal == **/
	public function field_primary_domain( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Domaine principal', 'tify' );?>" name="item_meta[primary_domain]" value="<?php echo $this->get_meta( 'primary_domain' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Utilisateur système == **/
	public function field_os_user( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Utilisateur système', 'tify' );?>" name="item_meta[os_user]" value="<?php echo $this->get_meta( 'os_user' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Groupe système == **/
	public function field_os_group( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Groupe système', 'tify' );?>" name="item_meta[os_group]" value="<?php echo $this->get_meta( 'os_group' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Choix du serveur d'hébergement == **/
	public function field_server( $item )
	{
	?>
		<div class="form-group">
		<?php 
			tify_server_dropdown( 
				array(
					'name'				=> 'item_meta[server]',
					'class'				=> 'form-control',
					'selected'			=> ( $value = (int) $this->get_meta( 'server' ) ) ? $value : 0,
					'show_option_none'	=> true
				)
			);
		?>
		</div>
	<?php
	}
	
	/** == Champ - Répertoire d'installation du site == **/
	public function field_httpdocs_dir( $item )
	{
	?>
		<div class="form-group">
			<input type="text" class="form-control" placeholder="<?php _e( 'Groupe système', 'tify' );?>" name="item_meta[httpdocs_dir]" value="<?php echo $this->get_meta( 'httpdocs_dir' );?>">
		</div>
	<?php
	}
	
	/** == Champ - Choix du moteur de site == **/
	public function field_engine( $item )
	{
	?>
		<div class="form-group">
		<?php 
			tify_website_engine_select( 
				array(
					'name'				=> 'item_meta[engine]',
					'class'				=> 'radio-inline',
					'checked'			=> ( $value = $this->get_meta( 'engine' ) ) ? $value : '',
					'show_option_none'	=> true
				)
			);
		?>
		</div>
	<?php
	}
	
	/** == Champ - Url du front office == **/
	public function field_fo_url( $item )
	{
	?>
		<div class="form-group">
			<div class="input-group">											
				<span class="input-group-addon">http(s)://</span>
				<input type="text" class="form-control" placeholder="<?php _e( 'http://domain.ltd', 'tify' );?>" name="item_meta[fo_url]" value="<?php echo $this->get_meta( 'fo_url' );?>">
			</div>
		</div>
	<?php
	}
	
	/** == Champ - Url du back office == **/
	public function field_bo_url( $item )
	{
	?>
		<div class="form-group">
			<div class="input-group">											
				<span class="input-group-addon">http(s)://</span>
				<input type="text" class="form-control" placeholder="<?php _e( 'http://domain.ltd/admin', 'tify' );?>" name="item_meta[bo_url]" value="<?php echo $this->get_meta( 'bo_url' );?>">
			</div>
		</div>
	<?php
	}
}