<?php
use tiFy\Plugins\CPanel\App\Set\Server\GeneralTemplate;

/** == Liste déroulante de choix des serveurs == **/
function tify_server_dropdown( $args = array(), $echo = true )
{
	return GeneralTemplate::dropdown( $args, $echo );
}