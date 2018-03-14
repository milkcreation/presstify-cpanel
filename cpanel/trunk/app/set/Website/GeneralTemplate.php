<?php
namespace tiFy\Plugins\CPanel\App\Set\Website;

class GeneralTemplate
{	
	/* = CONTROLEURS = */
	/** == Liste des moteurs de site == **/
	public static function getEngines()
	{
		return array(
			'wp'		=> 'Wordpress',
			'presta'	=> 'Prestashop',
			'sf'		=> 'Symfony',
			'fs'		=> 'From Scratch',
			'joom'		=> 'Joomla',
			'magen'		=> 'Magento'
		);
	}	
	
	/** == Liste des moteurs de site == **/
	public static function getEngine( $id )
	{
		$engines = self::getEngines();
		if( ! empty( $engines[$id] ) )
			return $engines[$id];
	}	
	
	/** == Liste déroulante == **/
	public static function engineRadio( $args = array(), $echo = true )
	{
		$defaults = array(
			'name'						=> '',
			'class'						=> '',
			'checked'					=> '',
			'show_option_none'			=> false,
			'show_option_none_value'	=> ''
		);
		$args = wp_parse_args( $args, $defaults );		
		extract( $args );
				
		$output  = "";	
		if( $engines = self::getEngines() ) :
			if( $show_option_none ) :
				$output .= "<label". ( $class ? " class=\"{$class}\"" : "" ) .">";
				$output .= "<input type=\"radio\" name=\"{$name}\" value=\"{$show_option_none_value}\" ". checked( $show_option_none_value, $checked, false ) .">&nbsp;";
				$output .= is_bool( $show_option_none ) ? __( 'Aucun', 'tify' ) : (string) $show_option_none;
				$output .= "</label>"; 
			endif;
			
			foreach( $engines as $value => $label ) :
				$output .= "<label". ( $class ? " class=\"{$class}\"" : "" ) .">";
				$output .= "<input type=\"radio\" name=\"{$name}\" value=\"{$value}\" ". checked( $value, $checked, false ) .">&nbsp;";
				$output .= $label;
				$output .= "</label>"; 
			endforeach;
		endif;
		
		if( $echo )
			echo $output;
		
		return $output;		
	}	
}