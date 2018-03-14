<?php
use tiFy\Plugins\CPanel\App\Set\Website\GeneralTemplate;

/** == Liste de choix des serveurs == **/
function tify_website_engine_get( $id )
{
	return GeneralTemplate::getEngine( $id );
}


/** == Liste de choix des serveurs == **/
function tify_website_engine_select( $args = array(), $echo = true )
{
	return GeneralTemplate::engineRadio( $args, $echo );
}