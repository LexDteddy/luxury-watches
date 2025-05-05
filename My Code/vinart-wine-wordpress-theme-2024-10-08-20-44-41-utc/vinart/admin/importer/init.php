<?php
class vinartDashboard {

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 2.2.0
     *
     * @var object
     */
    private static $instance;
    
    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 2.2.0
     */
    public function __construct() {
        
        self::$instance = $this;

        add_action( 'admin_init', array($this, 'vinart_theme_activation') );
        add_action( 'admin_menu', array($this, 'vinart_add_admin') );
        add_action( 'admin_print_scripts', array($this, 'vinart_enqueue_admin_assets') );
    }

    // Custom assets for admin pages
    function vinart_enqueue_admin_assets() {
        wp_enqueue_style( 'vinart-theme-admin', get_template_directory_uri() . '/admin/importer/assets/admin-style.css' );
    }

    // Redirect to Demo Import page after Theme activation
    public function vinart_theme_activation() {
        global $pagenow;
        if ( is_admin() AND $pagenow == 'themes.php' AND isset( $_GET['activated'] ) ) {
            //Redirect to demo import
            header( 'Location: ' . admin_url( 'admin.php?page=vinart-home' ) );
        }
    }

    /**
     * Add Panel Page
     *
     * @since 2.2.0
     */
    public function vinart_add_admin() {
        //Output buffering
        ob_start();
        add_theme_page("About the theme", "About the theme", 'switch_themes', 'vinart-home', array($this, 'vinart_welcome_page'));
    }


    public function vinart_welcome_page() {

        $theme = wp_get_theme();
        if ( is_child_theme() ) {
            $theme = wp_get_theme( $theme->get( 'Template' ) );
        }
        $theme_name = $theme->get( 'Name' );
        $theme_version = $theme->get( 'Version' );

        $return_url = admin_url('admin.php?page=vinart-home');

        
        ?>
        
        <div class="gg-admin-welcome-page">
            

            <div class="welcome-panel">
                <div class="welcome-panel-content">
                    
                <div class="welcome-panel-header">
                    <h2>
                        <?php 
                        echo sprintf(
                            '%s <strong>%s</strong>',
                            esc_html__( 'Welcome to', 'vinart' ), 
                            $theme_name . ' ' . $theme_version );
                        ?>
                            
                    </h2>
                    <p class="about-description"><?php esc_html_e( 'Beautifully crafted WordPress theme ready to take your wine journey to the next level.', 'vinart' ) ?></p>
                    </div>

                    <div class="welcome-panel-column-container">
                        <!-- Install plugins -->
                        <div class="welcome-panel-column">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <rect width="48" height="48" rx="4" fill="#1E1E1E"></rect>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M22.5 16L22.5 20H25.5V16H27V20H28.5C29.0523 20 29.5 20.4477 29.5 21V25L26.5 29V31C26.5 31.5523 26.0523 32 25.5 32H22.5C21.9477 32 21.5 31.5523 21.5 31V29L18.5 25V21C18.5 20.4477 18.9477 20 19.5 20H21L21 16H22.5ZM23 28.5V30.5H25V28.5L28 24.5V21.5H20V24.5L23 28.5Z" fill="white"></path>
                            </svg>
                            <div class="welcome-panel-column-content">
                                <h3><?php esc_html_e( 'Install Plugins', 'vinart' ) ?></h3>
                                <p><?php echo sprintf( esc_html__( '%s has bundled popular premium plugins which greatly increases the flexibility of the theme.', 'vinart' ), $theme_name ); ?></p>
                                <a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=tgmpa-install-plugins' ); ?>"><?php esc_html_e( 'Install Plugins', 'vinart' ) ?></a>
                            </div>
                        </div>
                        <!-- Theme documentation -->
                        <div class="welcome-panel-column">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <rect width="48" height="48" rx="4" fill="#1E1E1E"></rect>
                                <path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="white"></path>
						        <path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="white"></path>
                            </svg>
                            <div class="welcome-panel-column-content">
                                <h3><?php esc_html_e( 'Theme Documentation', 'vinart' ) ?></h3>
                                <p><?php esc_html_e( 'Wondering where to start? Check out the theme online documentation.', 'vinart' ) ?></p>
                                <a class="button button-primary" target="_blank" href="http://okthemes.com/vinart/assets/doc/vinart_theme_documentation.pdf">
                                    <?php esc_html_e( 'Online Documentation', 'vinart' ) ?></a>
                            </div>
                        </div>
                        <!-- Customize -->
                        <div class="welcome-panel-column">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <rect width="48" height="48" rx="4" fill="#1E1E1E"></rect>
                                <path d="M33 18.75H23.1925C22.7954 17.7305 21.7239 17 20.4643 17C19.2047 17 18.1332 17.7305 17.736 18.75H15V20.5H17.736C18.1332 21.5195 19.2047 22.25 20.4643 22.25C21.7239 22.25 22.7954 21.5195 23.1925 20.5H33V18.75Z" fill="white"></path>
						        <path d="M33 27.5H30.264C29.8668 26.4805 28.7953 25.75 27.5357 25.75C26.2761 25.75 25.2046 26.4805 24.8075 27.5H15V29.25H24.8075C25.2046 30.2695 26.2761 31 27.5357 31C28.7953 31 29.8668 30.2695 30.264 29.25H33V27.5Z" fill="white"></path>
                            </svg>
                            <div class="welcome-panel-column-content">
                                <h3><?php esc_html_e( 'Customize Appearance', 'vinart' ) ?></h3>
                                <p><?php esc_html_e( 'Customize the look and feel of your site with the help of the Theme Options panel.', 'vinart' ) ?></p>
                                <?php if (class_exists('Okthemes_Toolkit')) : ?>
                                <a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=vinart_options' ); ?>"><?php esc_html_e( 'Go to Theme Options', 'vinart' ) ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer" style="display:none;">
                <ul">
                    <li>
                        <i class="dashicons dashicons-editor-help"></i>
                        <a href="#" target="_blank"><?php esc_html_e( 'Online Documentation', 'vinart' ) ?></a>
                    </li>
                    <li>
                        <i class="dashicons dashicons-sos"></i>
                        <a href="#" target="_blank"><?php esc_html_e( 'Support Portal', 'vinart' ) ?></a>
                    </li>
                    <li>
                        <i class="dashicons dashicons-backup"></i>
                        <a href="#" target="_blank"><?php esc_html_e( 'Theme Changelog', 'vinart' ) ?></a>
                    </li>
                </ul>
            </div>

        </div>
        <?php
    }  


}

new vinartDashboard;