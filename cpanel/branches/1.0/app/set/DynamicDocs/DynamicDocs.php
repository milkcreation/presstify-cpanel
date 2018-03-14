<?php
namespace tiFy\Plugins\CPanel\App\Set\DynamicDocs;

use tiFy\Core\Templates\Templates;

class DynamicDocs extends \tiFy\App\Set
{
    /**
     * Liste des documentations déclarées
     * @var array
     */
    private static $Registered          = array();
    
    /**
     * Vue courante
     * @var
     */
    public static $Current              = null;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        foreach (self::tFyAppConfig() as $view => $attrs) :
            if (!isset($attrs['route'])) :
                $attrs['route'] = $view;
            endif;

            self::$Registered[$view] = $attrs;
        endforeach;

        // Déclaration des événement de déclenchement
        $this->tFyAppActionAdd('tify_templates_register');

        // Chargement des fonctions d'aide à la saisie
        include self::tFyAppDirname() . '/Helpers.php';
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration des templates
     *
     * @return void
     */
    public function tify_templates_register()
    {
        foreach ((array)self::$Registered as $view => $attrs) :
            $attrs['route'] = 'DynamicDocs/' . $attrs['route'];
            if (isset($attrs['template_part'])) :
                add_action('get_template_part_' . $attrs['template_part'], [$this, 'get_template_part'], 10, 2);
            else :
                $attrs['render_cb'] = '_render';
            endif;

            Templates::register(
                'DynamicDocs' . $view,
                $attrs,
                'front'
            );
        endforeach;
    }

    /**
     * Récupération du fichier de template
     *
     * @return string
     */
    public function get_template_part($slug, $name)
    {
        if (!$temp = tify_templates_current()) :
            return;
        endif;
        if (!preg_match('/^DynamicDocs(.*)/', tify_templates_current()->getID(), $match)) :
            return;
        endif;

        $templates[] = "{$slug}.php";
        ob_start();
        locate_template($templates, true, false);
        $output = ob_get_clean();
        if ($vars = $temp->getTemplate()->getVars()) :
            $output = \tiFy\Lib\Chars::mergeVars($output, $vars);
        endif;

        echo $output;
        exit;
    }

    /**
     * Récupération d'un variable d'environnement dans la vue courante
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getVar($name, $default = '')
    {
        if (!$current = self::$Current) :
            return $default;
        endif;

        return $current->getVar($name, $default);
    }
}