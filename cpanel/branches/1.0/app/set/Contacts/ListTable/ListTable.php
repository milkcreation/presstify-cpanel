<?php
namespace tiFy\Plugins\CPanel\App\Set\Contacts\ListTable;

class ListTable extends \tiFy\Core\Templates\Front\Model\ListTable\ListTable
{
    /**
     * Définition des actions sur un élément
     */
    public function set_row_actions()
    {
        return array('edit', 'trash');
    }
}