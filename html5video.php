<?php
/*
Plugin Name: SP HTML5 Video gallery and Video Player(Responsive)
Plugin URL: http://sptechnolab.com
Description: HTML5 Video gallery and Video Player(Responsive) 
Version: 1.0
Author: SP Technolab
Author URI: http://sptechnolab.com
Contributors: SP Technolab
*/
/*
 * Register HTML5 Video Player and Video gallery (Responsive)
 *
 */
function html5video_setup_post_types() {

	$html5video_labels =  apply_filters( 'sp_html5video_labels', array(
		'name'                => 'Video Gallery',
		'singular_name'       => 'Video Gallery',
		'add_new'             => __('Add New', 'sp_html5video'),
		'add_new_item'        => __('Add New Video', 'sp_html5video'),
		'edit_item'           => __('Edit Video', 'sp_html5video'),
		'new_item'            => __('New Video', 'sp_html5video'),
		'all_items'           => __('All Video', 'sp_html5video'),
		'view_item'           => __('View Video Gallery', 'sp_html5video'),
		'search_items'        => __('Search Video Gallery', 'sp_html5video'),
		'not_found'           => __('No Video Gallery found', 'sp_html5video'),
		'not_found_in_trash'  => __('No Video Gallery found in Trash', 'sp_html5video'),
		'parent_item_colon'   => '',
		'menu_name'           => __('Video Gallery', 'sp_html5video'),
		'exclude_from_search' => true
	) );


	$html5video_args = array(
		'labels' 			=> $html5video_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'query_var' 		=> true,
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'menu_icon'           => plugins_url( 'Movies-icon.png', __FILE__ ),
		'hierarchical' 		=> false,
		'supports' => array('title','editor','thumbnail','excerpt'),
		'taxonomies' => array('category', 'post_tag')
	);
	register_post_type( 'sp_html5video', apply_filters( 'sp_html5video_post_type_args', $html5video_args ) );

}

add_action('init', 'html5video_setup_post_types');
/*
 * Add [sp_html5video limit="-1"] shortcode
 *
 */
function sp_html5video_shortcode( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		"limit" => ''
	), $atts));
	
	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	
	ob_start();

	// Create the Query
	$post_type 		= 'sp_html5video';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'orderby'        => $orderby, 
								'order'          => $order,
								'no_found_rows'  => 1
								) 
						);
	
	//Get post type count
	$option = 'html5video_option';
	$html5widthandheight = get_option( $option, $default ); 
	
	$post_count = $query->post_count;
	$i = 1;
	
	// Displays Custom post info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
		
		?>
		<div class="video_frame" style="width:<?php echo $html5widthandheight['html5video_width']; ?>px; margin:0 20px 20px 0; float:left;">
		 <video id="video_<?php echo get_the_ID(); ?>" class="video-js vjs-default-skin" controls preload="none" width="<?php echo $html5widthandheight['html5video_width']; ?>" height="<?php echo $html5widthandheight['html5video_height']; ?>"
				poster="<?php if (has_post_thumbnail( $post->ID ) ): ?>
<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); 
 echo $image[0]; endif; ?>"
 data-setup="{}">
	
		
		
		<?php echo get_the_content(); ?>
		
		</video>
		<h3 class="sp_festivals_title"><?php the_title(); ?></h3>
		</div>
		<?php
		$i++;
		endwhile;
		
	endif;
	
	// Reset query to prevent conflicts
	wp_reset_query();
	
	?>
	
	<?php
	
	return ob_get_clean();

}
wp_register_style( 'videoCSS', plugin_dir_url( __FILE__ ) . 'css/video-js.css' );
wp_register_script( 'videoJS', plugin_dir_url( __FILE__ ) . 'js/video.js', array( 'jquery' ) );			

	wp_enqueue_style( 'videoCSS' );
	wp_enqueue_script( 'videoJS' );
	
function html5script() {

	?>
	<script type="text/javascript">
	<?php $url = plugins_url(); echo $url;  ?>
  videojs.options.flash.swf = "<?php echo $url; ?>html5_videogallery_plus_player/video-js.swf"
</script>
	<?php
	}
add_action('wp_head', 'html5script');
add_shortcode("sp_html5video", "sp_html5video_shortcode");


class Html5videosetting
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Html5video Settings', 
            'manage_options', 
            'Html5video-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'html5video_option' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Video Gallery Setting</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );   
                do_settings_sections( 'Html5video-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'html5video_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'Html5video-setting-admin' // Page
        );  

        add_settings_field(
            'html5video_width', // ID
            'Video Player Width(px)', // Title 
            array( $this, 'html5video_width_callback' ), // Callback
            'Html5video-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'html5video_height', 
            'Video Player Height(px)', 
            array( $this, 'html5video_height_callback' ), 
            'Html5video-setting-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['html5video_width'] ) )
            $new_input['html5video_width'] = absint( $input['html5video_width'] );

        if( isset( $input['html5video_height'] ) )
            $new_input['html5video_height'] = sanitize_text_field( $input['html5video_height'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function html5video_width_callback()
    {
        printf(
            '<input type="text" id="html5video_width" name="html5video_option[html5video_width]" value="%s" />',
            isset( $this->options['html5video_width'] ) ? esc_attr( $this->options['html5video_width']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function html5video_height_callback()
    {
        printf(
            '<input type="text" id="html5video_height" name="html5video_option[html5video_height]" value="%s" />',
            isset( $this->options['html5video_height'] ) ? esc_attr( $this->options['html5video_height']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new Html5videosetting();