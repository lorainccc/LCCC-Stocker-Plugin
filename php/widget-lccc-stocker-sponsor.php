<?php
/**
 *
 * @package   LCCC_Stocker_Sponsor_Widget
 * @author    LCCC Web Developement Team
 * @license   GPL-2.0+
 * @link      http://www.lorainccc.edu
 * @copyright 2016 Lorain County Community College
 */

 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// TODO: change 'LCCC_Stocker_Sponsor_Widget' to the name of your plugin
class LCCC_Stocker_Sponsor_Widget extends WP_Widget {

    protected $widget_slug = 'lccc-stocker-sponsor-widget';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'Stocker Sponsor Widget', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Short description of the widget goes here.', $this->get_widget_slug() )
			)
		);
		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

			$sponsorargs=array(
					'post_type' => 'sponsor',
					'post_status' => 'publish',
					);
					$newsponsors = new WP_Query($sponsorargs);
					if ( $newsponsors->have_posts() ) :
            echo '<div class="grid-container">';
					echo '<div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-4">';
							while ( $newsponsors->have_posts() ) : $newsponsors->the_post();
		$assoclink = stocker_sponsor_metabox_get_meta('stocker_sponsor_metabox_associated_link');
										echo '<div class="cell lccc-sponsor">';
														echo '<a href="'.$assoclink.'">'; 			the_post_thumbnail();
														echo '</a>';
										echo '</div>';
							endwhile;
					echo '</div>';
          echo '</div>';
					endif;
	} // end widget


	public function flush_widget_cache()
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// TODO: Here is where you update your widget's old values with the new, incoming values

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		// TODO: Define default values for your variables
		$instance = wp_parse_args(
			(array) $instance
		);


	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'lccc-stocker-sponsor-widget' to the name of *your* plugin
		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate


} // end class

// TODO: Remember to change 'LCCC_Stocker_Sponsor_Widget' to match the class name definition
add_action( 'widgets_init', create_function( '', 'register_widget("LCCC_Stocker_Sponsor_Widget");' ) );
?>
