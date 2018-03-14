<?php
/*
 Plugin Name: 301
 Plugin URI: http://presstify.com/plugins/301
 Description: <p>Création des spécifications du fichier .htaccess relatives aux redirections 301 d'un site internet.</p><p>Elle se fait par le biais de l'import d'un fichier CSV de mapping des redirections attendues</p>
 Version: 1.160229
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 */
namespace tiFy\Plugins\CPanel\App\Set\Redirect301; 

class Redirect301 extends \tiFy\App\Set
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        parent::__construct();

        // Déclencheurs
        /// Traitement de l'upload
        add_action( 'wp_ajax_uploadfile_handle', array( $this, 'wp_ajax_uploadfile_handle' )  );
        /// Vérification des redirection
        add_action( 'wp_ajax_check_redirect_url', array( $this, 'wp_ajax_check_redirect_url' ) );
        
        $rewrite_base = parse_url(home_url());        
        if(isset($rewrite_base['path'])) :
            $rewrite_base = trailingslashit($rewrite_base['path']);
        else :
            $rewrite_base = '/';
        endif;

        /// Chargement du template
        if( preg_match('#^'. preg_quote( $rewrite_base . 'Redirect301', '/' )  .'#', $_SERVER['REQUEST_URI'] ) ) :
            /// Mise en file des scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
            add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        endif;
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Mise en file des scripts de l'interface utilisateur
     */
    public function wp_enqueue_scripts()
    {
        // Scripts
        wp_enqueue_script( 'Redirect301', self::tFyAppUrl() . '/Redirect301.js', array( 'jquery' ), '160205', true );
    }
    
    /**
     * Téléchargement de fichier via Ajax
     */
    public function wp_ajax_uploadfile_handle()
    {
        $offset     = 0;
        $passed     = -1;
        $delimiter     = ",";
        $enclosure     = "\"";
        $escape     = "\\";

        $file = current( $_FILES );

        $lines = file( $file['tmp_name'] );

        $total = count( $lines );
        $max = ( $passed > 0 ) ? ( $offset + $passed ) : ( $total+1 - $offset );
        if( $max > $total ) $max = $total;

        $datas = array();
        for( $i = $offset; $i < $max; $i++ ) :
        $s = $lines[$i];
        // Eviter les erreurs de BOM
        $s = ( substr( $s, 0, 3 ) == chr( hexdec( 'EF' ) ) . chr( hexdec( 'BB' ) ) . chr( hexdec( 'BF' ) ) ) ? substr( $s, 3 ) : $s;
        $datas[$i] = str_getcsv( utf8_encode( $s ), $delimiter, $enclosure, $escape );
        endfor;

        echo json_encode( $datas );
        exit;
    }
    
    /**
     * Vérification des redirection d'url via Ajax
     */
    public function wp_ajax_check_redirect_url()
    {
        $root_url    = $_POST['root_url'];
        $ori_url    = $_POST['ori_url'];
        $dest_url    = $_POST['dest_url'];
        $useragent     = $_SERVER['HTTP_USER_AGENT'];

        if( preg_match( '/^\//', $ori_url ) )
            $ori_url = untrailingslashit( $root_url ) . $ori_url;
        if( preg_match( '/^\//', $dest_url ) )
            $dest_url = untrailingslashit( $root_url ) . $dest_url;

        $options = array(
                CURLOPT_RETURNTRANSFER => true,      // return web page
                CURLOPT_HEADER         => false,     // do not return headers
                CURLOPT_FOLLOWLOCATION => false,      // follow redirects
                CURLOPT_USERAGENT      => $useragent, // who am i
                CURLOPT_AUTOREFERER    => true,       // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 2,          // timeout on connect (in seconds)
                CURLOPT_TIMEOUT        => 2,          // timeout on response (in seconds)
                CURLOPT_MAXREDIRS      => 10,         // stop after 10 redirects
                CURLOPT_SSL_VERIFYPEER => false,     // SSL verification not required
                CURLOPT_SSL_VERIFYHOST => false,     // SSL verification not required
        );
        $ch = curl_init( $ori_url );
        curl_setopt_array( $ch, $options );
        curl_exec( $ch );

        $redirect_url = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );
        curl_close($ch);

        echo json_encode( array( 'success' => (int) ( $redirect_url == $dest_url ) ) );
        exit;
    }
    
    /**
     * Redirection de template
     */
    public function template_redirect()
    {
        get_header();
    ?>    
        <div id="tify-301-redirect">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header" ><?php _e( 'Redirection 301' );?></h2>    
                    </div>                        
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="well">
                            <h3>Usage</h3>
                            <h4>Étape 1 - Renseigner un fichier CSV</h4>
                            <ul>
                                <li>De préférence utiliser OpenOffice plutôt qu'Excel</li>
                                <li>Le fichier doit contenir 2 colonnes</li>
                                <li>La première colonne doit contenir l'url d'origine</li>
                                <li>La seconde colonne, la nouvelle url de destination</li>
                                <li>Au moment de l'export choisir la virgule (,) comme séparateur. format DOS sous Excel</li>
                            </ul>
                            <h4>Étape 2 - Importer le fichier CSV depuis le bouton de cette interface</h4>
                            <h4>Étape 3 - Copier/Coller les règles dans le fichier .htaccess à la racine du site</h4>
                            <h4>Étape 4 - Lancer le test des url</h4>
                        </div>
                    </div>                        
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h2 class="box-title"><?php _e( 'Étape 1 - Renseigner le fichier CSV' );?></h2>
                            </div>
                            <div class="box-body">
                                <div id="download">
                                    
                                    <a href="<?php echo self::tFyAppUrl() . '/assets/sample.csv'; ?>" class="btn btn-default"><?php _e( 'Télécharger le fichier d\'exemple' );?></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="box">
                            <div class="box-header with-border">
                                <h2 class="box-title"><?php _e( 'Étape 2 - Importer le fichier CSV' );?></h2>
                            </div>
                            <div class="box-body">
                                <div id="upload">                                    
                                    <form id="#upload-csv" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <input type="file" id="uploadfile-trigger" autocomplete="off">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="box">
                            <div class="box-header with-border">
                                <h2 class="box-title"><?php _e( 'Étape 3 - Règles à copier dans le fichier .htaccess' );?></h2>
                            </div>
                            <div class="box-body">
                                <div id="rules">            
                                    <pre>
                                        <code></code>
                                    </pre>
                                    <h3 id="total">
                                        <?php _e( 'Total' );?> : <span class="label label-success">0</span> <?php _e( 'régles à appliquer' );?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="box">
                            <div class="box-header with-border">
                                <h2 class="box-title"><?php _e( 'Étape 4 - Tester les url' );?></h2>
                            </div>
                            <div class="box-body">
                                <div id="test">
                                    <div class="form-group">
                                           <label for="root_url"><?php _e( 'Racine de l\'url du site' );?></label>
                                        <input type="text" id="root_url" class="form-control" placeholder="http://domain.ltd">
                                    </div>
                                    <ol id="test-results">
                                    </ol>
                                    <button id="test-trigger" class="btn btn-primary"><?php _e( 'Lancer le test' );?></button>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    <?php
        get_footer();
        exit;
    }
}