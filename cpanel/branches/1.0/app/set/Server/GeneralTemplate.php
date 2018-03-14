<?php
namespace tiFy\Plugins\CPanel\App\Set\Server;

class GeneralTemplate
{
	/* = CONTROLEURS = */
	/** == Liste déroulante == **/
	public static function dropdown( $args = array(), $echo = true )
	{
		$defaults = array(
			'name'						=> '',
			'class'						=> '',
			'selected'					=> '',
			'show_option_none'			=> false,
			'show_option_none_value'	=> '',
			'query_args'	=> array( 'status' => 'publish', 'orderby' => 'server_title', 'order' => 'ASC'  )
		);
		$args = wp_parse_args( $args, $defaults );		
		extract( $args );
				
		$output  = "";	
		if( $servers = Server::getList( $query_args ) ) :
			$output .= "<select name=\"{$name}\"". ( $class ? " class=\"{$class}\"" : "" ) .">";
		
			if( $show_option_none ) :
				$output .= "<option value=\"{$show_option_none_value}\"". selected( $show_option_none_value, $selected, false ) .">";
				$output .= is_bool( $show_option_none ) ? __( 'Aucun', 'tify' ) : (string) $show_option_none;
				$output .= "</option>";
			endif;
			
			foreach( $servers as $s ) :
				$output .= "<option value=\"{$s->server_id}\" ". selected( $s->server_id, $selected, false ) .">";
				$output .= $s->server_title;
				$output .= "</option>"; 
			endforeach;
			$output .= "</select>";
		endif;
		
		if( $echo )
			echo $output;
		
		return $output;		
	}	
}