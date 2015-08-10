<?php
/**
 * Plugin Name: ThoughtBubble
 * Plugin URI: http://mobdev-blog.com/
 * Description: A short message widget for everything that's too short for a real post
 * Version: 0.1
 * Author: Aljoscha Pörtner
 * Author URI: http://mobdev-blog.com/
 **/
function debug_to_console( $data ) {

    $output = '';

    if ( is_array( $data ) ) {
        $output .= "<script>console.warn( 'Debug Objects with Array.' ); console.log( '" . implode( ',', $data) . "' );</script>";
    } else if ( is_object( $data ) ) {
        $data    = var_export( $data, TRUE );
        $data    = explode( "\n", $data );
        foreach( $data as $line ) {
            if ( trim( $line ) ) {
                $line    = addslashes( $line );
                $output .= "console.log( '{$line}' );";
            }
        }
        $output = "<script>console.warn( 'Debug Objects with Object.' ); $output</script>";
    } else {
        $output .= "<script>console.log( 'Debug Objects: {$data}' );</script>";
    }

    echo $output;
}


function register_cpt_thought_bubble() {
    wp_enqueue_script('jquery');
    $labels = array(
        'name' => _x( 'ThoughtBubble', 'thought_bubble' ),
        'singular_name' => _x( 'ThoughtBubble', 'thought_bubble' ),
        'add_new' => _x( 'New Thought', 'thought_bubble' ),
        'add_new_item' => _x( 'Add New Thought', 'thought_bubble' ),
        'edit_item' => _x( 'Edit Thought', 'thought_bubble' ),
        'new_item' => _x( 'New Thought', 'thought_bubble' ),
        'view_item' => _x( 'View Thought', 'thought_bubble' ),
        'search_items' => _x( 'Search Thoughts', 'thought_bubble' ),
        'not_found' => _x( 'No Thought found', 'thought_bubble' ),
        'not_found_in_trash' => _x( 'No thoughts found in Trash', 'thought_bubble' ),
        'parent_item_colon' => _x( 'Parent Thought:', 'thought_bubble' ),
        'menu_name' => _x( 'Thoughts', 'thought_bubble' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Thoughts filterable by genre',
        'supports' => array('title','editor', 'author', 'thumbnail','revisions', 'page-attributes' ),
        'taxonomies' => array( 'genres' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-status',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'thought_bubble', $args );
}

add_action( 'init', 'register_cpt_thought_bubble' );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'smashing_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'smashing_post_meta_boxes_setup' );

/* Meta box setup function. */
function smashing_post_meta_boxes_setup() {

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'smashing_add_post_meta_boxes' );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function smashing_add_post_meta_boxes() {

    add_meta_box(
        'smashing-post-class',      // Unique ID
        esc_html__( 'ThoughtBubble', 'example' ),    // Title
        'smashing_post_class_meta_box',   // Callback function
        'thought_bubble',         // Admin page (or post type)
        'normal',         // Context
        'high'         // Priority
    );
}

/* Display the post meta box. */
function smashing_post_class_meta_box( $object, $box ) { ?>

    <?php wp_nonce_field( basename( __FILE__ ), 'smashing_post_class_nonce' ); ?>

    <p>
        <label for="smashing-post-class" id="charAlert" style="visibility: visible" ><?php _e( "Don't use more than 140 characters", 'example' ); ?></label>
        <br />

    </p>

   <script>$j=jQuery.noConflict();

        // Use jQuery via $j(...)
        $j(document).ready(function(){
            console.log($j('#content_ifr').contents().find('#tinymce').html());
        });</script>
<?php }


// Function used to automatically create Music Reviews page.
function create_thoughts_pages(){

    //post status and options
    $post = array(
        'comment_status' => 'closed',
        'ping_status' =>  'closed' ,
        'post_date' => date('Y-m-d H:i:s'),
        'post_name' => 'thought_bubble',
        'post_status' => 'publish' ,
        'post_title' => 'Thoughts',
        'post_type' => 'page',
    );

    //insert page and save the id
    //$newvalue = wp_insert_post( $post, false );
    //save the id in the database
    //update_option( 'mrpage', $newvalue );
}
// // Activates function if plugin is activated
register_activation_hook( __FILE__, 'create_thoughts_pages');

// Creating the widget
class thoughts_widget extends WP_Widget {



    function __construct() {
        parent::__construct(
// Base ID of your widget
            'thoughts_widget',

// Widget name will appear in UI
            __('ThoughtsBubble Widget', 'thought_widget_domain'),

// Widget description
            array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'thought_widget_domain' ), title => __('ThoughtBubble'))
        );
        //add_filter( 'posts_where' , 'posts_where' );
    }




// Creating widget front-end
// This is where the action happens
    function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo '<div id="thought-bubble-sidebar" style="display: inline-block;
  padding: 16px;
  margin: 10px 0;
  max-width: 468px;
  border: #ddd 1px solid;
  border-top-color: #eee;
  border-bottom-color: #bbb;
  border-radius: 5px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15);
  font: bold 14px/18px Helvetica, Arial, sans-serif;
  color: #000;">';
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }


        function get_actual_posts_thought_bubble() {

            global $wpdb;
            $result = $wpdb->get_row( "SELECT post_content,post_title,post_date, guid FROM $wpdb->posts WHERE post_modified_gmt = (SELECT MAX(post_modified_gmt) FROM $wpdb->posts WHERE post_type = 'thought_bubble')" );
            return $result;
        }

// This is where you run the code and display the output
        $resultVar = get_actual_posts_thought_bubble();
        $postDate = date('F j, Y, g:i a',strtotime($resultVar->post_date));
        echo "<div style='text-decoration: underline;font: normal 16px/22px Georgia, Palatino, serif;margin: 0 5px 10px 0;'><h1>{$resultVar->post_title}</h1></div>";
        echo "<div style='font-weight: normal;color: #666;font-size: 12px;'>{$resultVar->post_content} - <a href='{$resultVar->guid}'>{$postDate}</a></div>";
        //echo __( get_actual_posts_thought_bubble()->post_content, 'thought_widget_domain' );
        echo '</div>';
        echo '</div>';
        echo $args['after_widget'];
    }

// Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'ThoughtBubble', 'thought_widget_domain' );
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function thought_load_widget() {
    register_widget( 'thoughts_widget' );
}
add_action( 'widgets_init', 'thought_load_widget' );

?>