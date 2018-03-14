<?php
namespace tiFy\Plugins\CPanel\App\Set\Contacts;

use tiFy\Core\Db\Db;
use tiFy\Core\Templates\Templates;

class Contacts extends \tiFy\App\Set
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions            = array(
        'tify_db_register',
        'tify_templates_register'
    );

    /**
     * Type de contact
     * @var mixed
     */
    private static $Type                = array();

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Définition des types initiaux
        // Personne physique
        self::setType(
            'organisation',
            array(
                'title'     => __('Personne', 'tify'),
                'position'  => 1
            )
        );

        // Organisation
        self::setType(
            'organisation',
            array(
                'title'     => __('Organisation', 'tify'),
                'position'  => 2
            )
        );
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration d'une table de base de données
     *
     * @return void
     */
    public function tify_db_register()
    {
        Db::register(
            'tiFySetContacts',
            [
                'install'       => true,
                'name'          => 'contacts',
                'col_prefix'    => 'contact_',
                'columns'       => [
                    'id'            => [
                        'type'          => 'BIGINT',
                        'size'          => 20,
                        'unsigned'      => true,
                        'auto_increment'=> true
                    ],
                    'title'     => [
                        'type'          => 'TEXT'
                    ],
                    'type'      => [
                        'type'          => 'TEXT'
                    ],
                    'author'        => [
                        'type'          => 'BIGINT',
                        'size'          => 20,
                        'unsigned'      => true,
                        'default'       => 0,
                    ],
                    'created_date'  => [
                        'type'          => 'DATETIME',
                        'default'       => '0000-00-00 00:00:00'
                    ],
                    'modified_date' => [
                        'type'          => 'DATETIME',
                        'default'       => '0000-00-00 00:00:00'
                    ]
                ],
                'meta'          => true,
                'sql_engine'    => new \wpdb(EXTDB_USER, EXTDB_PASSWORD, EXTDB_NAME, EXTDB_HOST)
            ]
        );
    }

    /**
     * Déclaration de gabarit
     *
     * @return void
     */
    public function tify_templates_register()
    {
        Templates::register(
            'tiFyPluginCPanelContactsListTable',
            array(
                'route'             => 'Contact/List',
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Contacts\ListTable\ListTable',
                'db'                => 'tiFySetContacts',
                'edit_template'     => 'tiFyPluginCPanelContactsEditForm'
            ),
            'front'
        );

        Templates::register(
            'tiFyPluginCPanelContactsEditForm',
            array(
                'route'             => 'Contact/Edit',
                'cb'                => 'tiFy\Plugins\CPanel\App\Set\Contacts\EditForm\EditForm',
                'db'                => 'tiFySetContacts',
                'list_template'     => 'tiFyPluginCPanelContactsListTable'
            ),
            'front'
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Définition d'un type de contact
     *
     * @param string $id Identifiant unique de qualification du type de contact
     * @param array $attrs {
     *      Attributs de configuration du type
     *
     *      @param string $title Intitulé
     *      @param int $position Position d'affichage
     * }
     *
     * @return void
     */
    public static function setType($id, $attrs = array())
    {
        $defaults = array(
            'title'     => '',
            'position'  => 99
        );
        $attrs = \wp_parse_args($attrs, $defaults);

        if(!isset($attrs['title']))
            $attrs['title'] = $id;

        self::$Type[$id] = $attrs;
    }
}