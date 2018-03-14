<?php
/**
 * @see http://www.dns-lg.com/documentation
 **/
namespace tiFy\Plugins\CPanel\App\Set\DnsChecker;

class Template extends \tiFy\App\Factory
{
	/* = ARGUMENT = */
	// Liste des actions à déclencher
	protected $tFyAppActions            = array(
		'wp_ajax_tify_dns_check'	
	); 
		
	/* = DECLENCHEURS = */
	/** == == **/
	public function wp_enqueue_scripts()
	{
		wp_enqueue_script( 'tiFyPluginsDnsChecker', self::tFyAppUrl() .'/DnsChecker.js', array(), '161111', true );
	}
	
	/** == == **/
	public function wp_ajax_tify_dns_check()
	{
		
		$response = wp_remote_get( "http://www.dns-lg.com/". $_REQUEST['node'] ."/". $_REQUEST['domain'] . "/" . $_REQUEST['recordType'] );
				
		$error = false; 
		$return = array(
			'result'	=> '',
			'ttl'		=> '--',
			'status'	=> 'warning'
		);
		if( ! $body = wp_remote_retrieve_body( $response ) ) :
			$error = true;
			$return['result'] = __( 'Réponse vide', 'tify' );
			$return['status'] = 'danger';
		elseif( is_wp_error( $body ) ) :
			$error = true;
			$return['result'] = $body->get_error_message();
			$return['status'] = 'danger';
		else :
			$resp = json_decode( $body, true );
		
			if( isset( $resp['code'] ) ) :
				$error = true;
				$return['result'] = sprintf( __( '%s : %s', 'tify' ),  $resp['code'], $resp['message'] );
				$return['status'] = 'warning';
			elseif( empty( $resp['answer'] ) ) :
				$error = true;
				$return['result'] = __( 'Aucun enregistrement', 'tify' );
				$return['status'] = 'warning';
			else :	
				$results = array(); $ttls = array();
				foreach( (array) $resp['answer'] as $a ) :
					$results[] 	=  $a['rdata']; 
					$ttls[] 	= "<span class=\"ttl-tooltip\" data-toggle=\"tooltip\" title=\"". sprintf( __( '%d secondes', 'tify' ), $a['ttl'] ) ."\">". ( ceil( $a['ttl']/3600 ) ) ."h</span>";
				endforeach;				
				
				$return['result'] = join( '<br>', $results );
				$return['ttl'] = join( '<br>', $ttls );
				$return['status'] = 'success';
			endif;
		endif;
		
		if( $error ) :
			wp_send_json_error( $return );
		else :
			wp_send_json_success( $return );
		endif;
	}
	
	/* = AFFICHAGE = */
	/** == Rendu == **/
	public function render()
	{		
		$response = wp_remote_get( "http://www.dns-lg.com/nodes.json" );
		$body = wp_remote_retrieve_body( $response );
		
		$nodes = current( json_decode( $body ) );

		foreach( $nodes as $node ) :
			$node->flag = \tiFy\Lib\Country::flag( $node->isocc );
		endforeach;
				
		get_header();
	?>
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="page-header">
				<h1><?php _e( 'Vérification de propagation DNS', 'tify' );?></h1>
			</div>
	    </div>
	    <div class="col-md-4" id="toolbar">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e( 'Options', 'tify' );?></h3>
				</div>
				<div class="panel-body">
					<form id="tiFyDnsCheckerForm" autocomplete="on" target="autocomplete_host" method="get" action="">
						<div class="form-group">
							<label for="domain"><?php _e( 'Domaine', 'tify' );?></label>
							<div class="input-group">
								<span class="input-group-addon">http://</span>
								<input type="text" class="form-control" id="domain" name="domain" required>
							</div>
						</div>
						<div class="form-group">
							<label for="record-type"><?php _e( 'Type d\'enregistrement', 'tify' );?></label><br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-sm btn-default active">
									<input type="radio" name="recordType" value="a" checked>A
								</label>
								<label class="btn btn-sm btn-default">
									<input type="radio" name="recordType" value="cname">CNAME
								</label>
								<label class="btn btn-sm btn-default">
									<input type="radio" name="recordType" value="mx">MX
								</label>
								<label class="btn btn-sm btn-default">
									<input type="radio" name="recordType" value="ns">NS
								</label>
								<label class="btn btn-sm btn-default">
									<input type="radio" name="recordType" value="spf">SPF
								</label>
								<label class="btn btn-sm btn-default">
									<input type="radio" name="recordType" value="txt">TXT
								</label>
							</div>
						</div>
						<div class="form-group">
							<button class="btn btn-primary" type="submit" id="tiFyDnsCheckerForm-submit" data-loading-text="<?php _e( 'En cours ...', 'tify' )?>"><?php _e( 'Lancer', 'tify' );?></button>
						</div>	
					</form>
				</div>
				<div class="panel-footer">
					<div class="progress tiFyDnsCheckerProgress">
						<div class="progress-bar progress-bar-success tiFyDnsCheckerProgressBar tiFyDnsCheckerProgressBar-success" style="width: 0%"></div>
						<div class="progress-bar progress-bar-danger tiFyDnsCheckerProgressBar tiFyDnsCheckerProgressBar-danger" style="width: 0%"></div>
						<div class="progress-bar progress-bar-warning tiFyDnsCheckerProgressBar tiFyDnsCheckerProgressBar-warning" style="width: 0%"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="table-responsive">
					<table id="tiFyDnsCheckerListTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?php _e( 'Serveur DNS', 'tify' );?></th>
								<th><?php _e( 'Résultat', 'tify' );?></th>
								<th><?php _e( 'TTL', 'tify' );?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e( 'Serveur DNS', 'tify' );?></th>
								<th><?php _e( 'Résultat', 'tify' );?></th>
								<th><?php _e( 'TTL', 'tify' );?></th>
							</tr>
						</tfoot>
						<tbody>
						<?php foreach( $nodes as $node) :?>
							<tr class="tiFyDnsCheckServer" data-node="<?php echo $node->name;?>">
								<td class="tiFyDnsCheckServer-infos" style="width:200px;">
									<div class="media">
										<div class="media-left media-top">
											<img src="data:image/svg+xml;base64,<?php echo base64_encode( $node->flag );?>" width="24" height="auto"/>
										</div>
										<div class="media-body">
											<h4 class="media-heading"><?php echo $node->asn;?></h4>
											<em style="clear:both;font-size:0.9em;color:#999;"><?php echo $node->operator;?></em>
										</div>
									</div>
								</td>
								<td class="tiFyDnsCheckServer-result"></td>
								<td class="tiFyDnsCheckServer-ttl" style="width:50px;"></td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>	
	</div>
	<?php 
		get_footer();
	}
}