<?php 
/**
 * Plugin Name: CCR Client Testimonials
 * Plugin URI: http://www.codexcoder.com/plugins/ccr-client-testimonials
 * Description: Client Testimonials WordPress plugin from <a href="http://www.codexcoder.com/">CodexCoder</a>. This plugin help you to show your client feedback, comments or testimonials on your site sidebar (Widget area) or anywhere.
 * Version: 1.0.0
 * Author: CodexCoder
 * Author URI: http://codexcoder.com
 * License: GPL2
 * Text Domain: codexcoder
 */

/*
 * Creating custom cost type to  adding Testimonials.
 */

function ccr_testimonials_post_type() {

	$labels = array(
		'name'                => _x( 'Testimonials', 'codexcoder' ),
		'singular_name'       => _x( 'Testimonial', 'codexcoder' ),
		'menu_name'           => __( 'Testimonials', 'codexcoder' ),
		'parent_item_colon'   => __( 'Parent Testimonials:', 'codexcoder' ),
		'all_items'           => __( 'All Testimonials', 'codexcoder' ),
		'view_item'           => __( 'View Testimonial', 'codexcoder' ),
		'add_new_item'        => __( 'Add New Testimonial', 'codexcoder' ),
		'add_new'             => __( 'New Testimonial', 'codexcoder' ),
		'edit_item'           => __( 'Edit Testimonial', 'codexcoder' ),
		'update_item'         => __( 'Update Testimonial', 'codexcoder' ),
		'search_items'        => __( 'Search Testimonials', 'codexcoder' ),
		'not_found'           => __( 'No Testimonials found', 'codexcoder' ),
		'not_found_in_trash'  => __( 'No Testimonials found in Trash', 'codexcoder' ),
		);
	$args = array(
		'label'               => __( 'testimonials', 'codexcoder' ),
		'description'         => __( 'Codex Coder Testimonials Post Type', 'codexcoder' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 20,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		);
	register_post_type( 'testimonials', $args );
}

// Hook into the 'init' action
add_action( 'init', 'ccr_testimonials_post_type', 0 );


// Add Testimonials icon in dashboard
function ccr_testimonials_dashboard_icon(){
?>
 <style>
/*Testimonials Dashboard Icons*/
#adminmenu .menu-icon-testimonials div.wp-menu-image:before {
  content: "\f205";
}
</style>
<?php
}
add_action( 'admin_head', 'ccr_testimonials_dashboard_icon' );


/*
 * Custom Meta Box For to adding name and designation.
 */

add_action( 'add_meta_boxes', 'ccr_testimonials_meta' );

function ccr_testimonials_meta()
{
	add_meta_box( 'ccr-client-testimonials-meta', 'Client Informations', 'ccr_testimonial_info', 'testimonials', 'normal', 'high' );
}

function ccr_testimonial_info( $post )
{
	$values = get_post_custom( $post->ID );
	$name = isset( $values['ccr_testimonial_client_name'] ) ? esc_attr( $values['ccr_testimonial_client_name'][0] ) : '';
	$designation = isset( $values['ccr_testimonial_client_designaion'] ) ? esc_attr( $values['ccr_testimonial_client_designaion'][0] ) : '';
	wp_nonce_field( 'ccr_testimonials_meta_box_nonce', 'meta_box_nonce' );
	?>
	<p>  
        <label for="ccr_testimonial_client_name">Name:</label><br />
        <input type="text" name="ccr_testimonial_client_name" id="ccr_testimonial_client_name" value="<?php echo $name; ?>" width="100%"/>  
    </p>
    <p>  
        <label for="ccr_testimonial_client_designaion">Designation:</label><br />
        <input type="text" name="ccr_testimonial_client_designaion" id="ccr_testimonial_client_designaion" value="<?php echo $designation; ?>" width="100%"/>  
    </p>
	<?php	
}


add_action( 'save_post', 'ccr_testimonial_info_save_data' );

function ccr_testimonial_info_save_data( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'ccr_testimonials_meta_box_nonce' ) ) return;
	
	if( !current_user_can( 'edit_post' ) ) return;
	
	$allowed = array( 
		'a' => array(
			'href' => array()
		)
	);

	if( isset( $_POST['ccr_testimonial_client_name'] ) )
		update_post_meta( $post_id, 'ccr_testimonial_client_name', wp_kses( $_POST['ccr_testimonial_client_name'], $allowed ) );

	if( isset( $_POST['ccr_testimonial_client_designaion'] ) )
		update_post_meta( $post_id, 'ccr_testimonial_client_designaion', wp_kses( $_POST['ccr_testimonial_client_designaion'], $allowed ) );
}

/*
 * Render Client Info
 */

function get_ccr_testimonial_client_name() {
	if ( get_post_meta( get_the_ID(), 'ccr_testimonial_client_name', true ) ) { 
		echo get_post_meta( get_the_ID(), 'ccr_testimonial_client_name', true );
	}
}

function get_ccr_testimonial_client_designaion() {
	if ( get_post_meta( get_the_ID(), 'ccr_testimonial_client_designaion', true ) ) { 
		echo get_post_meta( get_the_ID(), 'ccr_testimonial_client_designaion', true );
	}
}

/*
 * Enqueue Bootstrap According JS and Styleseets
 */

function ccr_load_ct_script_style() {
	wp_enqueue_script('jquery' );
	wp_enqueue_style( 'ccr-ct-style', plugins_url('/assets/css/style.css', __FILE__), array(), '1.0.0', 'all' );
	wp_enqueue_script( 'ccr-ctbs-js', plugins_url('/assets/js/bootstrap.js', __FILE__), array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'ccr_load_ct_script_style' );

/*
 * Testimonials Shotcode
 */
function ccr_testimonial_shortcode($atts , $content = null) {

	extract( shortcode_atts(
		array(
			'posts' => '5',
			), $atts )
	);
	$args = array (
		'post_type'              => 'testimonials',
		'posts_per_page'         => $posts,
		);

	$ccrctquery = new WP_Query( $args );
	$firstActive = 0;
?>

<div id="ccr-testimonials">
	<div id="ccr-testimonials-carousel" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
		<!-- Wrapper for slides -->
		<?php if ( $ccrctquery->have_posts() ) {
			while ( $ccrctquery->have_posts() ) {
				$ccrctquery->the_post(); ?>
			<div class="item <?php echo !$firstActive ? "active":"";?>">
				<div class="ccr-tfix">
					<div class="testimonial-content">
						<?php the_content(); ?>
					</div>
				</div>
				<div class="testimonial-meta">
					<div class="client-photo">
						<?php the_post_thumbnail('thumbnail'); ?>
					</div>
					<div class="client-info">
						<p class="client-name"><?php get_ccr_testimonial_client_name(); ?></p>
						<p class="client-designation"><?php get_ccr_testimonial_client_designaion(); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div> <!-- /.item -->
		
		<?php $firstActive = 1; }	} else { echo "No Testimonials Found";	} wp_reset_postdata(); ?>
		</div> <!-- /.carousel-inner -->
		<!-- Controls -->
		<div class="testimonial-control">
			<a class="ccr-carousel-control left" href="#ccr-testimonials-carousel" data-slide="prev">
				<i class="previcon"></i>
			</a>
			<a class="ccr-carousel-control right" href="#ccr-testimonials-carousel" data-slide="next">
				<i class="nexticon"></i>
			</a>
		</div><!-- /.testimonial-control -->
		<div class="clear"></div>
	</div>
</div> <!--/#ccr-testimonials -->
<?php return;

}
add_shortcode( 'ccr_client_testimonials', 'ccr_testimonial_shortcode' );

// Support Shortcode in sidebar
add_filter('widget_text', 'do_shortcode');