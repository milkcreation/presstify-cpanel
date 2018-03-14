<?php
namespace tiFy\Plugins\CPanel\App\Set\Contacts\EditForm;

use tiFy\Plugins\CPanel\App\Set\Contacts\GeneralTemplate\DropdownType;

class EditForm extends \tiFy\Core\Templates\Front\Model\EditForm\EditForm
{
    /**
     * Définition des champs de formulaire
     *
     * @return array [
     *      "$attr" => "$label"
     *      ...
     * ]
     */
    public function set_fields()
    {
        return [
            'contact_title'     => __('Nom à afficher', 'tify'),
            'contact_type'      => __('Type de contact', 'tify')
        ];
    }

    /**
     * Champs Type de contact
     */
    public function field_contact_type()
    {
        return DropdownType::output();
    }

    /**
     * Affichage de la page de l'interface d'administration
     */
    public function render()
    {
        get_header();
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    <?php echo $this->label( 'edit_item' );?>
                    <?php if( $this->NewItem ) : ?>
                        &nbsp;<a class="btn btn-default" href="<?php echo $this->BaseUri;?>"><?php echo $this->label( 'new_item' );?></a>
                    <?php endif;?>
                </h2>
            </div>
        </div>

        <?php $this->notices();?>

        <form method="post">
            <?php $this->hidden_fields();?>
            <div class="row">
                <div class="col-lg-9">
                    <?php $this->form();?>
                </div>

                <div class="col-lg-3">
                    <?php $this->submitdiv();?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
        get_footer();
        exit;
    }
}