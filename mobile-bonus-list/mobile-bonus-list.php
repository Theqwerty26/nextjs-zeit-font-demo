<?php
/**
 * Plugin Name: Mobile Bonus List
 * Plugin URI: https://example.com
 * Description: A WordPress plugin to manage and display bonus listings for betting/casino sites with mobile-first design.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: mobile-bonus-list
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MBL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MBL_PLUGIN_PATH', plugin_dir_path(__FILE__));

class MobileBonusList {
    
    public function __construct() {
        add_action('init', array($this, 'register_bonus_post_type'));
        add_action('init', array($this, 'register_bonus_taxonomy'));
        add_action('add_meta_boxes', array($this, 'add_bonus_meta_boxes'));
        add_action('save_post', array($this, 'save_bonus_meta_data'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('bonus_list', array($this, 'render_bonus_list_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_filter_bonuses', array($this, 'ajax_filter_bonuses'));
        add_action('wp_ajax_nopriv_filter_bonuses', array($this, 'ajax_filter_bonuses'));
    }
    
    /**
     * Register the bonus custom post type
     */
    public function register_bonus_post_type() {
        $labels = array(
            'name' => 'Bonuses',
            'singular_name' => 'Bonus',
            'menu_name' => 'Bonuses',
            'add_new' => 'Add New Bonus',
            'add_new_item' => 'Add New Bonus',
            'edit_item' => 'Edit Bonus',
            'new_item' => 'New Bonus',
            'view_item' => 'View Bonus',
            'search_items' => 'Search Bonuses',
            'not_found' => 'No bonuses found',
            'not_found_in_trash' => 'No bonuses found in trash'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'bonus'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-money-alt',
            'supports' => array('title', 'thumbnail')
        );
        
        register_post_type('bonus', $args);
    }
    
    /**
     * Register the bonus category taxonomy
     */
    public function register_bonus_taxonomy() {
        $labels = array(
            'name' => 'Bonus Categories',
            'singular_name' => 'Bonus Category',
            'search_items' => 'Search Categories',
            'all_items' => 'All Categories',
            'edit_item' => 'Edit Category',
            'update_item' => 'Update Category',
            'add_new_item' => 'Add New Category',
            'new_item_name' => 'New Category Name',
            'menu_name' => 'Categories'
        );
        
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'bonus-category')
        );
        
        register_taxonomy('bonus_category', array('bonus'), $args);
        
        // Create default terms
        if (!term_exists('trend', 'bonus_category')) {
            wp_insert_term('Trend', 'bonus_category', array('slug' => 'trend'));
        }
        if (!term_exists('recommended', 'bonus_category')) {
            wp_insert_term('Recommended', 'bonus_category', array('slug' => 'recommended'));
        }
    }
    
    /**
     * Add meta boxes for bonus fields
     */
    public function add_bonus_meta_boxes() {
        add_meta_box(
            'bonus_details',
            'Bonus Details',
            array($this, 'bonus_meta_box_callback'),
            'bonus',
            'normal',
            'high'
        );
    }
    
    /**
     * Meta box callback function
     */
    public function bonus_meta_box_callback($post) {
        wp_nonce_field('save_bonus_meta_data', 'bonus_meta_nonce');
        
        $bonus_text = get_post_meta($post->ID, '_bonus_text', true);
        $bonus_link = get_post_meta($post->ID, '_bonus_link', true);
        $logo_id = get_post_meta($post->ID, '_bonus_logo', true);
        
        echo '<table class="form-table">';
        
        // Logo field
        echo '<tr>';
        echo '<th><label for="bonus_logo">Logo</label></th>';
        echo '<td>';
        echo '<div id="bonus_logo_container">';
        if ($logo_id) {
            echo wp_get_attachment_image($logo_id, 'medium');
            echo '<br><button type="button" id="remove_logo_button" class="button">Remove Logo</button>';
        }
        echo '</div>';
        echo '<button type="button" id="upload_logo_button" class="button" ' . ($logo_id ? 'style="display:none;"' : '') . '>Upload Logo</button>';
        echo '<input type="hidden" id="bonus_logo" name="bonus_logo" value="' . esc_attr($logo_id) . '" />';
        echo '</td>';
        echo '</tr>';
        
        // Bonus text field
        echo '<tr>';
        echo '<th><label for="bonus_text">Bonus Text</label></th>';
        echo '<td><input type="text" id="bonus_text" name="bonus_text" value="' . esc_attr($bonus_text) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        // Bonus link field
        echo '<tr>';
        echo '<th><label for="bonus_link">Bonus Link</label></th>';
        echo '<td><input type="url" id="bonus_link" name="bonus_link" value="' . esc_attr($bonus_link) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '</table>';
        
        // Add media uploader script
        ?>
        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload_logo_button').click(function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose Logo',
                    button: {
                        text: 'Choose Logo'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#bonus_logo').val(attachment.id);
                    $('#bonus_logo_container').html('<img src="' + attachment.url + '" style="max-width: 200px;" /><br><button type="button" id="remove_logo_button" class="button">Remove Logo</button>');
                    $('#upload_logo_button').hide();
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove_logo_button', function(e) {
                e.preventDefault();
                $('#bonus_logo').val('');
                $('#bonus_logo_container').html('');
                $('#upload_logo_button').show();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_bonus_meta_data($post_id) {
        if (!isset($_POST['bonus_meta_nonce']) || !wp_verify_nonce($_POST['bonus_meta_nonce'], 'save_bonus_meta_data')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['bonus_text'])) {
            update_post_meta($post_id, '_bonus_text', sanitize_text_field($_POST['bonus_text']));
        }
        
        if (isset($_POST['bonus_link'])) {
            update_post_meta($post_id, '_bonus_link', esc_url_raw($_POST['bonus_link']));
        }
        
        if (isset($_POST['bonus_logo'])) {
            update_post_meta($post_id, '_bonus_logo', absint($_POST['bonus_logo']));
        }
    }
    
    /**
     * Enqueue assets conditionally
     */
    public function enqueue_assets() {
        global $post;
        
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bonus_list')) {
            wp_enqueue_style('mobile-bonus-list-css', MBL_PLUGIN_URL . 'assets/css/mobile-bonus-list.css', array(), '1.0.0');
            wp_enqueue_script('mobile-bonus-list-js', MBL_PLUGIN_URL . 'assets/js/mobile-bonus-list.js', array('jquery'), '1.0.0', true);
            
            wp_localize_script('mobile-bonus-list-js', 'mbl_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mbl_filter_nonce')
            ));
        }
        
        // Enqueue media uploader on bonus edit pages
        if (is_admin() && get_post_type() == 'bonus') {
            wp_enqueue_media();
        }
    }
    
    /**
     * Render bonus list shortcode
     */
    public function render_bonus_list_shortcode($atts) {
        $atts = shortcode_atts(array(), $atts, 'bonus_list');
        
        ob_start();
        
        // Get initial bonuses
        $bonuses = $this->get_bonuses();
        
        ?>
        <div class="mobile-bonus-list-container">
            <!-- Top Announcement Bar -->
            <div class="announcement-bar announcement-bar-top">
                🎰 Welcome to the Best Casino Bonuses! 🎰
            </div>
            
            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="search-container">
                    <input type="text" id="bonus-search" placeholder="Search bonuses..." />
                </div>
                
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">All</button>
                    <button class="filter-btn" data-category="trend">Trend</button>
                    <button class="filter-btn" data-category="recommended">Recommended</button>
                </div>
            </div>
            
            <!-- Bonus Cards Container -->
            <div id="bonus-cards-container" class="bonus-cards-container">
                <?php echo $this->render_bonus_cards($bonuses); ?>
            </div>
            
            <!-- Bottom Announcement Bar -->
            <div class="announcement-bar announcement-bar-bottom">
                💰 Don't miss out on these exclusive offers! 💰
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Get bonuses based on search and category
     */
    private function get_bonuses($search = '', $category = '') {
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        if (!empty($category) && $category !== 'all') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'bonus_category',
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }
        
        return new WP_Query($args);
    }
    
    /**
     * Render bonus cards HTML
     */
    private function render_bonus_cards($bonuses) {
        if (!$bonuses->have_posts()) {
            return '<div class="no-bonuses">No bonuses found.</div>';
        }
        
        $output = '';
        
        while ($bonuses->have_posts()) {
            $bonuses->the_post();
            $post_id = get_the_ID();
            $bonus_text = get_post_meta($post_id, '_bonus_text', true);
            $bonus_link = get_post_meta($post_id, '_bonus_link', true);
            $logo_id = get_post_meta($post_id, '_bonus_logo', true);
            
            $output .= '<div class="bonus-card">';
            
            // Logo
            if ($logo_id) {
                $output .= '<div class="bonus-logo">';
                $output .= wp_get_attachment_image($logo_id, 'medium', false, array('alt' => get_the_title()));
                $output .= '</div>';
            }
            
            // Site name
            $output .= '<h3 class="bonus-title">' . esc_html(get_the_title()) . '</h3>';
            
            // Bonus text
            if ($bonus_text) {
                $output .= '<p class="bonus-text">' . esc_html($bonus_text) . '</p>';
            }
            
            // Bonus button
            if ($bonus_link) {
                $output .= '<a href="' . esc_url($bonus_link) . '" class="bonus-button" target="_blank" rel="nofollow">Get Bonus</a>';
            }
            
            $output .= '</div>';
        }
        
        wp_reset_postdata();
        
        return $output;
    }
    
    /**
     * AJAX handler for filtering bonuses
     */
    public function ajax_filter_bonuses() {
        if (!wp_verify_nonce($_POST['nonce'], 'mbl_filter_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        
        $bonuses = $this->get_bonuses($search, $category);
        $html = $this->render_bonus_cards($bonuses);
        
        wp_send_json_success($html);
    }
}

// Initialize the plugin
new MobileBonusList();
