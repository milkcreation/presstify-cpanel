<?php
namespace tiFy\Plugins\CPanel\App\Set\Contacts\GeneralTemplate;

class DropdownType extends \tiFy\Lib\Walkers\Dropdown
{
    public function __construct()
    {
        $this->setItems(
            array(
                array(
                    'id'        => 'none',
                    'content'   => __('Choix du type de contact', 'tify'),
                    'value'     => ''
                ),
                array(
                    'id'        => 'person',
                    'content'   => __('Personne', 'tify'),
                    'value'     => 'person'
                ),
                array(
                    'id'        => 'organisation',
                    'content'   => __('Organisation', 'tify'),
                    'value'     => 'organisation'
                )
            )
        );
        $this->Current = 'organisation';
    }
}