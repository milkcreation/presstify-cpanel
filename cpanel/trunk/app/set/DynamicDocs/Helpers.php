<?php
use tiFy\Plugins\CPanel\App\Set\DynamicDocs\DynamicDocs;

function tify_dynamic_docs_get_var($var)
{
    return DynamicDocs::getVar($var);
}