<?php
/**
 * Plugin Name: Mobile Bonus List
 * Plugin URI: https://www.timeseoagencija.me
 * Description: Bahis/casino siteleri için bonus listelerini yönetmek ve görüntülemek için mobil öncelikli tasarımlı WordPress eklentisi.
 * Version: 1.0.0
 * Author: Time SEO Agencija
 * Author URI: https://www.timeseoagencija.me
 * License: GPL v2 or later
 * Text Domain: mobile-bonus-list
 * Domain Path: /languages
 */

// Doğrudan erişimi engelle
if (!defined('ABSPATH')) {
    exit;
}

// Eklenti sabitlerini tanımla
define('MBL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MBL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MBL_VERSION', '1.0.0');

class MobileBonusList {
    
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        add_action('init', array($this, 'register_bonus_post_type'));
        add_action('init', array($this, 'register_bonus_taxonomy'));
        add_action('add_meta_boxes', array($this, 'add_bonus_meta_boxes'));
        add_action('save_post', array($this, 'save_bonus_meta_data'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_save_mbl_settings', array($this, 'save_settings'));
        add_shortcode('bonus_list', array($this, 'render_bonus_list_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_filter_bonuses', array($this, 'ajax_filter_bonuses'));
        add_action('wp_ajax_nopriv_filter_bonuses', array($this, 'ajax_filter_bonuses'));
        
        // Cron job for auto show/hide bonuses
        add_action('wp', array($this, 'schedule_bonus_check'));
        add_action('mbl_check_bonus_dates', array($this, 'check_bonus_dates'));
    }
    
    /**
     * Plugin aktivasyonu
     */
    public function activate_plugin() {
        $this->register_bonus_post_type();
        $this->register_bonus_taxonomy();
        flush_rewrite_rules();
        
        // Varsayılan ayarları kaydet
        $default_settings = array(
            'background_color' => '#0b1224',
            'card_background' => '#1d2236',
            'primary_color' => '#f7931e',
            'text_color' => '#ffffff',
            'button_text_color' => '#ffffff'
        );
        update_option('mbl_settings', $default_settings);
        
        // Örnek bonusları ekle
        $this->create_sample_bonuses();
    }
    
    /**
     * Örnek bonusları oluştur
     */
    private function create_sample_bonuses() {
        if (get_posts(array('post_type' => 'bonus', 'numberposts' => 1))) {
            return; // Zaten bonus var
        }
        
        $sample_bonuses = array(
            array(
                'title' => 'Betboo Casino',
                'bonus_text' => '1000₺ Hoşgeldin Bonusu + 100 Freespin',
                'bonus_link' => 'https://example.com/betboo',
                'category' => 'trend',
                'order' => 1,
                'monthly_payment' => 500
            ),
            array(
                'title' => 'Bets10 Spor',
                'bonus_text' => '500₺ İlk Yatırım Bonusu',
                'bonus_link' => 'https://example.com/bets10',
                'category' => 'recommended',
                'order' => 2,
                'monthly_payment' => 300
            ),
            array(
                'title' => 'Superbahis',
                'bonus_text' => '750₺ Kayıp Bonusu + %25 Cashback',
                'bonus_link' => 'https://example.com/superbahis',
                'category' => 'trend',
                'order' => 3,
                'monthly_payment' => 400
            )
        );
        
        foreach ($sample_bonuses as $bonus) {
            $post_id = wp_insert_post(array(
                'post_title' => $bonus['title'],
                'post_type' => 'bonus',
                'post_status' => 'publish'
            ));
            
            if ($post_id) {
                update_post_meta($post_id, '_bonus_text', $bonus['bonus_text']);
                update_post_meta($post_id, '_bonus_link', $bonus['bonus_link']);
                update_post_meta($post_id, '_bonus_order', $bonus['order']);
                update_post_meta($post_id, '_monthly_payment', $bonus['monthly_payment']);
                wp_set_object_terms($post_id, $bonus['category'], 'bonus_category');
            }
        }
    }
    
    /**
     * Bonus özel yazı türünü kaydet
     */
    public function register_bonus_post_type() {
        $labels = array(
            'name' => 'Bonuslar',
            'singular_name' => 'Bonus',
            'menu_name' => 'Bonuslar',
            'add_new' => 'Yeni Bonus Ekle',
            'add_new_item' => 'Yeni Bonus Ekle',
            'edit_item' => 'Bonus Düzenle',
            'new_item' => 'Yeni Bonus',
            'view_item' => 'Bonus Görüntüle',
            'search_items' => 'Bonus Ara',
            'not_found' => 'Bonus bulunamadı',
            'not_found_in_trash' => 'Çöp kutusunda bonus bulunamadı'
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
     * Bonus kategori taksonomisini kaydet
     */
    public function register_bonus_taxonomy() {
        $labels = array(
            'name' => 'Bonus Kategorileri',
            'singular_name' => 'Bonus Kategorisi',
            'search_items' => 'Kategori Ara',
            'all_items' => 'Tüm Kategoriler',
            'edit_item' => 'Kategori Düzenle',
            'update_item' => 'Kategori Güncelle',
            'add_new_item' => 'Yeni Kategori Ekle',
            'new_item_name' => 'Yeni Kategori Adı',
            'menu_name' => 'Kategoriler'
        );
        
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'bonus-kategori')
        );
        
        register_taxonomy('bonus_category', array('bonus'), $args);
        
        // Varsayılan terimleri oluştur
        if (!term_exists('trend', 'bonus_category')) {
            wp_insert_term('Trend', 'bonus_category', array('slug' => 'trend'));
        }
        if (!term_exists('recommended', 'bonus_category')) {
            wp_insert_term('Önerilen', 'bonus_category', array('slug' => 'recommended'));
        }
    }
    
    /**
     * Admin menüsü ekle
     */
    public function add_admin_menu() {
        add_menu_page(
            'Mobile Bonus List',
            'Bonus Yönetimi',
            'manage_options',
            'mobile-bonus-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-money-alt',
            21
        );
        
        add_submenu_page(
            'mobile-bonus-dashboard',
            'Ayarlar',
            'Ayarlar',
            'manage_options',
            'mobile-bonus-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Dashboard sayfası
     */
    public function dashboard_page() {
        $total_bonuses = wp_count_posts('bonus')->publish;
        $active_bonuses = $this->get_active_bonuses_count();
        $expired_bonuses = $this->get_expired_bonuses_count();
        $upcoming_bonuses = $this->get_upcoming_bonuses_count();
        $total_monthly_payment = $this->get_total_monthly_payment();
        
        ?>
        <div class="wrap">
            <h1>Bonus Yönetim Paneli</h1>
            
            <div class="mbl-dashboard">
                <div class="mbl-stats-grid">
                    <div class="mbl-stat-card">
                        <div class="mbl-stat-icon">📊</div>
                        <div class="mbl-stat-content">
                            <h3><?php echo $total_bonuses; ?></h3>
                            <p>Toplam Bonus</p>
                        </div>
                    </div>
                    
                    <div class="mbl-stat-card active">
                        <div class="mbl-stat-icon">✅</div>
                        <div class="mbl-stat-content">
                            <h3><?php echo $active_bonuses; ?></h3>
                            <p>Aktif Bonuslar</p>
                        </div>
                    </div>
                    
                    <div class="mbl-stat-card expired">
                        <div class="mbl-stat-icon">❌</div>
                        <div class="mbl-stat-content">
                            <h3><?php echo $expired_bonuses; ?></h3>
                            <p>Süresi Bitmiş</p>
                        </div>
                    </div>
                    
                    <div class="mbl-stat-card upcoming">
                        <div class="mbl-stat-icon">⏰</div>
                        <div class="mbl-stat-content">
                            <h3><?php echo $upcoming_bonuses; ?></h3>
                            <p>Yaklaşan Başlangıç</p>
                        </div>
                    </div>
                    
                    <div class="mbl-stat-card payment">
                        <div class="mbl-stat-icon">💰</div>
                        <div class="mbl-stat-content">
                            <h3><?php echo number_format($total_monthly_payment); ?>₺</h3>
                            <p>Toplam Aylık Ödeme</p>
                        </div>
                    </div>
                </div>
                
                <div class="mbl-quick-actions">
                    <h2>Hızlı İşlemler</h2>
                    <div class="mbl-action-buttons">
                        <a href="<?php echo admin_url('post-new.php?post_type=bonus'); ?>" class="button button-primary">
                            Yeni Bonus Ekle
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=bonus'); ?>" class="button">
                            Tüm Bonusları Görüntüle
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=mobile-bonus-settings'); ?>" class="button">
                            Ayarlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .mbl-dashboard {
            margin-top: 20px;
        }
        .mbl-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .mbl-stat-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .mbl-stat-card.active { border-left: 4px solid #46b450; }
        .mbl-stat-card.expired { border-left: 4px solid #dc3232; }
        .mbl-stat-card.upcoming { border-left: 4px solid #ffb900; }
        .mbl-stat-card.payment { border-left: 4px solid #00a0d2; }
        .mbl-stat-icon {
            font-size: 24px;
            margin-right: 15px;
        }
        .mbl-stat-content h3 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .mbl-stat-content p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .mbl-quick-actions {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
        .mbl-action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        </style>
        <?php
    }
    
    /**
     * Ayarlar sayfası
     */
    public function settings_page() {
        $settings = get_option('mbl_settings', array());
        ?>
        <div class="wrap">
            <h1>Mobile Bonus List Ayarları</h1>
            
            <form id="mbl-settings-form">
                <?php wp_nonce_field('mbl_settings_nonce', 'mbl_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Arka Plan Rengi</th>
                        <td>
                            <input type="color" name="background_color" value="<?php echo esc_attr($settings['background_color'] ?? '#0b1224'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Kart Arka Plan Rengi</th>
                        <td>
                            <input type="color" name="card_background" value="<?php echo esc_attr($settings['card_background'] ?? '#1d2236'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Ana Renk (Butonlar)</th>
                        <td>
                            <input type="color" name="primary_color" value="<?php echo esc_attr($settings['primary_color'] ?? '#f7931e'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Yazı Rengi</th>
                        <td>
                            <input type="color" name="text_color" value="<?php echo esc_attr($settings['text_color'] ?? '#ffffff'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Buton Yazı Rengi</th>
                        <td>
                            <input type="color" name="button_text_color" value="<?php echo esc_attr($settings['button_text_color'] ?? '#ffffff'); ?>" />
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button-primary">Ayarları Kaydet</button>
                </p>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#mbl-settings-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: $(this).serialize() + '&action=save_mbl_settings',
                    success: function(response) {
                        if (response.success) {
                            alert('Ayarlar kaydedildi!');
                        } else {
                            alert('Hata: ' + response.data);
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Ayarları kaydet
     */
    public function save_settings() {
        if (!wp_verify_nonce($_POST['mbl_nonce'], 'mbl_settings_nonce')) {
            wp_send_json_error('Güvenlik kontrolü başarısız');
        }
        
        $settings = array(
            'background_color' => sanitize_hex_color($_POST['background_color']),
            'card_background' => sanitize_hex_color($_POST['card_background']),
            'primary_color' => sanitize_hex_color($_POST['primary_color']),
            'text_color' => sanitize_hex_color($_POST['text_color']),
            'button_text_color' => sanitize_hex_color($_POST['button_text_color'])
        );
        
        update_option('mbl_settings', $settings);
        wp_send_json_success('Ayarlar kaydedildi');
    }
    
    /**
     * Meta kutularını ekle
     */
    public function add_bonus_meta_boxes() {
        add_meta_box(
            'bonus_details',
            'Bonus Detayları',
            array($this, 'bonus_meta_box_callback'),
            'bonus',
            'normal',
            'high'
        );
    }
    
    /**
     * Meta kutu callback fonksiyonu
     */
    public function bonus_meta_box_callback($post) {
        wp_nonce_field('save_bonus_meta_data', 'bonus_meta_nonce');
        
        $bonus_text = get_post_meta($post->ID, '_bonus_text', true);
        $bonus_link = get_post_meta($post->ID, '_bonus_link', true);
        $logo_id = get_post_meta($post->ID, '_bonus_logo', true);
        $bonus_order = get_post_meta($post->ID, '_bonus_order', true);
        $start_date = get_post_meta($post->ID, '_start_date', true);
        $end_date = get_post_meta($post->ID, '_end_date', true);
        $monthly_payment = get_post_meta($post->ID, '_monthly_payment', true);
        
        echo '<table class="form-table">';
        
        // Logo alanı
        echo '<tr>';
        echo '<th><label for="bonus_logo">Logo</label></th>';
        echo '<td>';
        echo '<div id="bonus_logo_container">';
        if ($logo_id) {
            echo wp_get_attachment_image($logo_id, 'medium');
            echo '<br><button type="button" id="remove_logo_button" class="button">Logo Kaldır</button>';
        }
        echo '</div>';
        echo '<button type="button" id="upload_logo_button" class="button" ' . ($logo_id ? 'style="display:none;"' : '') . '>Logo Yükle</button>';
        echo '<input type="hidden" id="bonus_logo" name="bonus_logo" value="' . esc_attr($logo_id) . '" />';
        echo '</td>';
        echo '</tr>';
        
        // Bonus metni
        echo '<tr>';
        echo '<th><label for="bonus_text">Bonus Metni</label></th>';
        echo '<td><input type="text" id="bonus_text" name="bonus_text" value="' . esc_attr($bonus_text) . '" style="width: 100%;" placeholder="Örn: 1000₺ Hoşgeldin Bonusu" /></td>';
        echo '</tr>';
        
        // Bonus linki
        echo '<tr>';
        echo '<th><label for="bonus_link">Bonus Linki</label></th>';
        echo '<td><input type="url" id="bonus_link" name="bonus_link" value="' . esc_attr($bonus_link) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        // Sıra numarası
        echo '<tr>';
        echo '<th><label for="bonus_order">Sıra Numarası</label></th>';
        echo '<td><input type="number" id="bonus_order" name="bonus_order" value="' . esc_attr($bonus_order) . '" min="1" /></td>';
        echo '</tr>';
        
        // Başlangıç tarihi
        echo '<tr>';
        echo '<th><label for="start_date">Başlangıç Tarihi</label></th>';
        echo '<td><input type="datetime-local" id="start_date" name="start_date" value="' . esc_attr($start_date) . '" /></td>';
        echo '</tr>';
        
        // Bitiş tarihi
        echo '<tr>';
        echo '<th><label for="end_date">Bitiş Tarihi</label></th>';
        echo '<td><input type="datetime-local" id="end_date" name="end_date" value="' . esc_attr($end_date) . '" /></td>';
        echo '</tr>';
        
        // Aylık ödeme
        echo '<tr>';
        echo '<th><label for="monthly_payment">Aylık Ödeme Tutarı (₺)</label></th>';
        echo '<td><input type="number" id="monthly_payment" name="monthly_payment" value="' . esc_attr($monthly_payment) . '" min="0" step="0.01" /></td>';
        echo '</tr>';
        
        echo '</table>';
        
        // Medya yükleyici scripti
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
                    title: 'Logo Seç',
                    button: {
                        text: 'Logo Seç'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#bonus_logo').val(attachment.id);
                    $('#bonus_logo_container').html('<img src="' + attachment.url + '" style="max-width: 200px;" /><br><button type="button" id="remove_logo_button" class="button">Logo Kaldır</button>');
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
     * Meta kutu verilerini kaydet
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
        
        $fields = array(
            'bonus_text' => 'sanitize_text_field',
            'bonus_link' => 'esc_url_raw',
            'bonus_logo' => 'absint',
            'bonus_order' => 'absint',
            'start_date' => 'sanitize_text_field',
            'end_date' => 'sanitize_text_field',
            'monthly_payment' => 'floatval'
        );
        
        foreach ($fields as $field => $sanitize_func) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, $sanitize_func($_POST[$field]));
            }
        }
    }
    
    /**
     * Varlıkları koşullu olarak yükle
     */
    public function enqueue_assets() {
        global $post;
        
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bonus_list')) {
            wp_enqueue_style('mobile-bonus-list-css', MBL_PLUGIN_URL . 'assets/css/mobile-bonus-list.css', array(), MBL_VERSION);
            wp_enqueue_script('mobile-bonus-list-js', MBL_PLUGIN_URL . 'assets/js/mobile-bonus-list.js', array('jquery'), MBL_VERSION, true);
            
            // Ayarları JavaScript'e aktar
            $settings = get_option('mbl_settings', array());
            wp_localize_script('mobile-bonus-list-js', 'mbl_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mbl_filter_nonce'),
                'settings' => $settings
            ));
        }
    }
    
    /**
     * Admin scriptlerini yükle
     */
    public function admin_enqueue_scripts() {
        if (get_post_type() == 'bonus') {
            wp_enqueue_media();
        }
    }
    
    /**
     * Bonus listesi shortcode'unu render et
     */
    public function render_bonus_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'template' => 'vertical'
        ), $atts, 'bonus_list');
        
        ob_start();
        
        // Başlangıç bonuslarını al
        $bonuses = $this->get_bonuses();
        $settings = get_option('mbl_settings', array());
        
        ?>
        <div class="mobile-bonus-list-container" data-template="<?php echo esc_attr($atts['template']); ?>">
            <!-- Üst Duyuru Barı -->
            <div class="announcement-bar announcement-bar-top">
                🎰 En İyi Casino Bonuslarına Hoş Geldiniz! 🎰
            </div>
            
            <!-- Arama ve Filtre Bölümü -->
            <div class="search-filter-section">
                <div class="search-container">
                    <input type="text" id="bonus-search" placeholder="Bonus ara..." />
                </div>
                
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">Tümü</button>
                    <button class="filter-btn" data-category="trend">Trend</button>
                    <button class="filter-btn" data-category="recommended">Önerilen</button>
                </div>
            </div>
            
            <!-- Bonus Kartları Konteyneri -->
            <div id="bonus-cards-container" class="bonus-cards-container template-<?php echo esc_attr($atts['template']); ?>">
                <?php echo $this->render_bonus_cards($bonuses, $atts['template']); ?>
            </div>
            
            <!-- Alt Duyuru Barı -->
            <div class="announcement-bar announcement-bar-bottom">
                💰 Bu özel teklifleri kaçırmayın! 💰
            </div>
        </div>
        
        <style>
        :root {
            --mbl-bg-color: <?php echo esc_attr($settings['background_color'] ?? '#0b1224'); ?>;
            --mbl-card-bg: <?php echo esc_attr($settings['card_background'] ?? '#1d2236'); ?>;
            --mbl-primary: <?php echo esc_attr($settings['primary_color'] ?? '#f7931e'); ?>;
            --mbl-text: <?php echo esc_attr($settings['text_color'] ?? '#ffffff'); ?>;
            --mbl-btn-text: <?php echo esc_attr($settings['button_text_color'] ?? '#ffffff'); ?>;
        }
        </style>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Arama ve kategoriye göre bonusları al
     */
    private function get_bonuses($search = '', $category = '') {
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => '_bonus_order',
            'order' => 'ASC'
        );
        
        // Tarih kontrolü ekle
        $current_time = current_time('mysql');
        $meta_query = array('relation' => 'AND');
        
        // Başlangıç tarihi kontrolü
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key' => '_start_date',
                'value' => $current_time,
                'compare' => '<=',
                'type' => 'DATETIME'
            ),
            array(
                'key' => '_start_date',
                'compare' => 'NOT EXISTS'
            )
        );
        
        // Bitiş tarihi kontrolü
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key' => '_end_date',
                'value' => $current_time,
                'compare' => '>=',
                'type' => 'DATETIME'
            ),
            array(
                'key' => '_end_date',
                'compare' => 'NOT EXISTS'
            )
        );
        
        $args['meta_query'] = $meta_query;
        
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
     * Bonus kartlarını render et
     */
    private function render_bonus_cards($bonuses, $template = 'vertical') {
        if (!$bonuses->have_posts()) {
            return '<div class="no-bonuses">Bonus bulunamadı.</div>';
        }
        
        $output = '';
        
        if ($template === 'slider') {
            $output .= '<div class="bonus-slider-wrapper"><div class="bonus-slider">';
        } elseif ($template === 'accordion') {
            $output .= '<div class="bonus-accordion">';
        }
        
        while ($bonuses->have_posts()) {
            $bonuses->the_post();
            $post_id = get_the_ID();
            $bonus_text = get_post_meta($post_id, '_bonus_text', true);
            $bonus_link = get_post_meta($post_id, '_bonus_link', true);
            $logo_id = get_post_meta($post_id, '_bonus_logo', true);
            
            if ($template === 'accordion') {
                $output .= '<div class="accordion-item">';
                $output .= '<div class="accordion-header">';
                if ($logo_id) {
                    $output .= '<div class="bonus-logo-small">';
                    $output .= wp_get_attachment_image($logo_id, 'thumbnail', false, array('alt' => get_the_title()));
                    $output .= '</div>';
                }
                $output .= '<h3 class="bonus-title-accordion">' . esc_html(get_the_title()) . '</h3>';
                $output .= '<span class="accordion-toggle">+</span>';
                $output .= '</div>';
                $output .= '<div class="accordion-content">';
                if ($bonus_text) {
                    $output .= '<p class="bonus-text">' . esc_html($bonus_text) . '</p>';
                }
                if ($bonus_link) {
                    $output .= '<a href="' . esc_url($bonus_link) . '" class="bonus-button" target="_blank" rel="nofollow">Bonusu Al</a>';
                }
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<div class="bonus-card">';
                
                // Logo
                if ($logo_id) {
                    $output .= '<div class="bonus-logo">';
                    $output .= wp_get_attachment_image($logo_id, 'medium', false, array('alt' => get_the_title()));
                    $output .= '</div>';
                }
                
                // Site adı
                $output .= '<h3 class="bonus-title">' . esc_html(get_the_title()) . '</h3>';
                
                // Bonus metni
                if ($bonus_text) {
                    $output .= '<p class="bonus-text">' . esc_html($bonus_text) . '</p>';
                }
                
                // Bonus butonu
                if ($bonus_link) {
                    $output .= '<a href="' . esc_url($bonus_link) . '" class="bonus-button" target="_blank" rel="nofollow">Bonusu Al</a>';
                }
                
                $output .= '</div>';
            }
        }
        
        if ($template === 'slider') {
            $output .= '</div><div class="slider-nav"><button class="slider-prev">‹</button><button class="slider-next">›</button></div></div>';
        } elseif ($template === 'accordion') {
            $output .= '</div>';
        }
        
        wp_reset_postdata();
        
        return $output;
    }
    
    /**
     * AJAX bonus filtreleme işleyicisi
     */
    public function ajax_filter_bonuses() {
        if (!wp_verify_nonce($_POST['nonce'], 'mbl_filter_nonce')) {
            wp_send_json_error('Geçersiz nonce');
        }
        
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $template = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : 'vertical';
        
        $bonuses = $this->get_bonuses($search, $category);
        $html = $this->render_bonus_cards($bonuses, $template);
        
        wp_send_json_success($html);
    }
    
    /**
     * Bonus tarihlerini kontrol et
     */
    public function schedule_bonus_check() {
        if (!wp_next_scheduled('mbl_check_bonus_dates')) {
            wp_schedule_event(time(), 'hourly', 'mbl_check_bonus_dates');
        }
    }
    
    public function check_bonus_dates() {
        // Bu fonksiyon otomatik olarak bonus görünürlüğünü kontrol eder
        // Şu an için basit bir implementasyon
    }
    
    /**
     * İstatistik fonksiyonları
     */
    private function get_active_bonuses_count() {
        $current_time = current_time('mysql');
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_start_date',
                        'value' => $current_time,
                        'compare' => '<=',
                        'type' => 'DATETIME'
                    ),
                    array(
                        'key' => '_start_date',
                        'compare' => 'NOT EXISTS'
                    )
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_end_date',
                        'value' => $current_time,
                        'compare' => '>=',
                        'type' => 'DATETIME'
                    ),
                    array(
                        'key' => '_end_date',
                        'compare' => 'NOT EXISTS'
                    )
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    private function get_expired_bonuses_count() {
        $current_time = current_time('mysql');
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_end_date',
                    'value' => $current_time,
                    'compare' => '<',
                    'type' => 'DATETIME'
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    private function get_upcoming_bonuses_count() {
        $current_time = current_time('mysql');
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_start_date',
                    'value' => $current_time,
                    'compare' => '>',
                    'type' => 'DATETIME'
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    private function get_total_monthly_payment() {
        $args = array(
            'post_type' => 'bonus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_monthly_payment',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $bonuses = get_posts($args);
        $total = 0;
        
        foreach ($bonuses as $bonus) {
            $payment = get_post_meta($bonus->ID, '_monthly_payment', true);
            if ($payment) {
                $total += floatval($payment);
            }
        }
        
        return $total;
    }
}

// Eklentiyi başlat
new MobileBonusList();
