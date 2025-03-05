<?php
/**
 * WillsX Auto Linker
 *
 * Automatically adds links to specified keywords in blog posts
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WillsX_Auto_Linker
 */
class WillsX_Auto_Linker {
    /**
     * Keywords to URLs mapping
     *
     * @var array
     */
    private $keywords = array();

    /**
     * Settings
     *
     * @var array
     */
    private $settings = array();

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize settings
        $this->settings = get_option('willsx_autolinker_settings', array(
            'max_links_per_post' => 5,
            'max_links_per_keyword' => 1,
            'link_target' => '_self',
            'case_sensitive' => false,
            'post_types' => array('post'),
            'exclude_headings' => true,
            'exclude_links' => true,
        ));

        // Load keywords
        $this->load_keywords();

        // Add content filter
        add_filter('the_content', array($this, 'auto_link_content'));

        // Admin hooks
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Load keywords from database
     */
    private function load_keywords() {
        $this->keywords = get_option('willsx_autolinker_keywords', array());
    }

    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_submenu_page(
            'willsx-dashboard',
            __('Auto Linker Settings', 'willsx'),
            __('Auto Linker', 'willsx'),
            'manage_options',
            'willsx-autolinker',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('willsx_autolinker', 'willsx_autolinker_settings');
        register_setting('willsx_autolinker', 'willsx_autolinker_keywords');

        add_settings_section(
            'willsx_autolinker_general',
            __('General Settings', 'willsx'),
            array($this, 'render_general_section'),
            'willsx_autolinker'
        );

        add_settings_field(
            'max_links_per_post',
            __('Max Links Per Post', 'willsx'),
            array($this, 'render_max_links_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'max_links_per_keyword',
            __('Max Links Per Keyword', 'willsx'),
            array($this, 'render_max_links_per_keyword_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'link_target',
            __('Link Target', 'willsx'),
            array($this, 'render_link_target_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'case_sensitive',
            __('Case Sensitive', 'willsx'),
            array($this, 'render_case_sensitive_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'post_types',
            __('Post Types', 'willsx'),
            array($this, 'render_post_types_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'exclude_headings',
            __('Exclude Headings', 'willsx'),
            array($this, 'render_exclude_headings_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );

        add_settings_field(
            'exclude_links',
            __('Exclude Existing Links', 'willsx'),
            array($this, 'render_exclude_links_field'),
            'willsx_autolinker',
            'willsx_autolinker_general'
        );
    }

    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . __('Configure how the auto linker works.', 'willsx') . '</p>';
    }

    /**
     * Render max links field
     */
    public function render_max_links_field() {
        $value = isset($this->settings['max_links_per_post']) ? $this->settings['max_links_per_post'] : 5;
        echo '<input type="number" name="willsx_autolinker_settings[max_links_per_post]" value="' . esc_attr($value) . '" min="0" max="100" />';
        echo '<p class="description">' . __('Maximum number of links to add per post. Set to 0 for unlimited.', 'willsx') . '</p>';
    }

    /**
     * Render max links per keyword field
     */
    public function render_max_links_per_keyword_field() {
        $value = isset($this->settings['max_links_per_keyword']) ? $this->settings['max_links_per_keyword'] : 1;
        echo '<input type="number" name="willsx_autolinker_settings[max_links_per_keyword]" value="' . esc_attr($value) . '" min="1" max="10" />';
        echo '<p class="description">' . __('Maximum number of times to link the same keyword in a post.', 'willsx') . '</p>';
    }

    /**
     * Render link target field
     */
    public function render_link_target_field() {
        $value = isset($this->settings['link_target']) ? $this->settings['link_target'] : '_self';
        echo '<select name="willsx_autolinker_settings[link_target]">';
        echo '<option value="_self" ' . selected($value, '_self', false) . '>' . __('Same Window (_self)', 'willsx') . '</option>';
        echo '<option value="_blank" ' . selected($value, '_blank', false) . '>' . __('New Window (_blank)', 'willsx') . '</option>';
        echo '</select>';
    }

    /**
     * Render case sensitive field
     */
    public function render_case_sensitive_field() {
        $value = isset($this->settings['case_sensitive']) ? $this->settings['case_sensitive'] : false;
        echo '<input type="checkbox" name="willsx_autolinker_settings[case_sensitive]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('If checked, keywords must match case exactly.', 'willsx') . '</p>';
    }

    /**
     * Render post types field
     */
    public function render_post_types_field() {
        $value = isset($this->settings['post_types']) ? $this->settings['post_types'] : array('post');
        $post_types = get_post_types(array('public' => true), 'objects');
        
        foreach ($post_types as $post_type) {
            echo '<label>';
            echo '<input type="checkbox" name="willsx_autolinker_settings[post_types][]" value="' . esc_attr($post_type->name) . '" ';
            checked(in_array($post_type->name, $value), true);
            echo ' />';
            echo esc_html($post_type->label);
            echo '</label><br />';
        }
    }

    /**
     * Render exclude headings field
     */
    public function render_exclude_headings_field() {
        $value = isset($this->settings['exclude_headings']) ? $this->settings['exclude_headings'] : true;
        echo '<input type="checkbox" name="willsx_autolinker_settings[exclude_headings]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('If checked, keywords in headings (h1-h6) will not be linked.', 'willsx') . '</p>';
    }

    /**
     * Render exclude links field
     */
    public function render_exclude_links_field() {
        $value = isset($this->settings['exclude_links']) ? $this->settings['exclude_links'] : true;
        echo '<input type="checkbox" name="willsx_autolinker_settings[exclude_links]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('If checked, keywords inside existing links will not be linked.', 'willsx') . '</p>';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Save keywords if form submitted
        if (isset($_POST['willsx_save_keywords']) && isset($_POST['willsx_keywords']) && check_admin_referer('willsx_save_keywords')) {
            $keywords = array();
            $raw_keywords = $_POST['willsx_keywords'];
            $raw_urls = $_POST['willsx_urls'];
            
            foreach ($raw_keywords as $index => $keyword) {
                if (!empty($keyword) && !empty($raw_urls[$index])) {
                    $keywords[sanitize_text_field($keyword)] = esc_url_raw($raw_urls[$index]);
                }
            }
            
            update_option('willsx_autolinker_keywords', $keywords);
            $this->keywords = $keywords;
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Keywords saved successfully.', 'willsx') . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="#settings" class="nav-tab nav-tab-active"><?php _e('Settings', 'willsx'); ?></a>
                <a href="#keywords" class="nav-tab"><?php _e('Keywords', 'willsx'); ?></a>
            </h2>
            
            <div id="settings" class="tab-content">
                <form action="options.php" method="post">
                    <?php
                    settings_fields('willsx_autolinker');
                    do_settings_sections('willsx_autolinker');
                    submit_button();
                    ?>
                </form>
            </div>
            
            <div id="keywords" class="tab-content" style="display: none;">
                <form method="post">
                    <?php wp_nonce_field('willsx_save_keywords'); ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Keyword', 'willsx'); ?></th>
                                <th><?php _e('URL', 'willsx'); ?></th>
                                <th><?php _e('Actions', 'willsx'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="keyword-list">
                            <?php
                            if (!empty($this->keywords)) {
                                foreach ($this->keywords as $keyword => $url) {
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="text" name="willsx_keywords[]" value="<?php echo esc_attr($keyword); ?>" class="regular-text" />
                                        </td>
                                        <td>
                                            <input type="url" name="willsx_urls[]" value="<?php echo esc_url($url); ?>" class="regular-text" />
                                        </td>
                                        <td>
                                            <button type="button" class="button remove-keyword"><?php _e('Remove', 'willsx'); ?></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <input type="text" name="willsx_keywords[]" value="" class="regular-text" />
                                </td>
                                <td>
                                    <input type="url" name="willsx_urls[]" value="" class="regular-text" />
                                </td>
                                <td>
                                    <button type="button" class="button remove-keyword"><?php _e('Remove', 'willsx'); ?></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button type="button" class="button" id="add-keyword"><?php _e('Add Keyword', 'willsx'); ?></button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php submit_button(__('Save Keywords', 'willsx'), 'primary', 'willsx_save_keywords'); ?>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Tab navigation
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                // Update active tab
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Show target content
                $('.tab-content').hide();
                $(target).show();
            });
            
            // Add keyword
            $('#add-keyword').on('click', function() {
                var row = '<tr>' +
                    '<td><input type="text" name="willsx_keywords[]" value="" class="regular-text" /></td>' +
                    '<td><input type="url" name="willsx_urls[]" value="" class="regular-text" /></td>' +
                    '<td><button type="button" class="button remove-keyword"><?php _e('Remove', 'willsx'); ?></button></td>' +
                    '</tr>';
                $('#keyword-list').append(row);
            });
            
            // Remove keyword
            $(document).on('click', '.remove-keyword', function() {
                $(this).closest('tr').remove();
            });
        });
        </script>
        <?php
    }

    /**
     * Auto link content
     *
     * @param string $content The post content
     * @return string The modified content
     */
    public function auto_link_content($content) {
        // Check if we should process this post type
        if (!in_array(get_post_type(), $this->settings['post_types'])) {
            return $content;
        }

        // If no keywords, return content unchanged
        if (empty($this->keywords)) {
            return $content;
        }

        // Create a DOM document
        $dom = new DOMDocument();
        
        // Load the content with UTF-8 encoding
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $content);
        $xpath = new DOMXPath($dom);
        
        // Get the body node
        $body = $dom->getElementsByTagName('body')->item(0);
        
        // If no body, return content unchanged
        if (!$body) {
            return $content;
        }
        
        // Process text nodes
        $this->process_node($body, $xpath);
        
        // Get the modified HTML
        $html = $dom->saveHTML($body);
        
        // Remove the body tags
        $html = preg_replace('/<\/?body>/', '', $html);
        
        return $html;
    }

    /**
     * Process a node for auto linking
     *
     * @param DOMNode $node The node to process
     * @param DOMXPath $xpath The XPath object
     * @param int $link_count Reference to the link count
     * @return void
     */
    private function process_node($node, $xpath, &$link_count = 0) {
        // Check if we've reached the maximum links per post
        $max_links = $this->settings['max_links_per_post'];
        if ($max_links > 0 && $link_count >= $max_links) {
            return;
        }
        
        // Skip if this is a heading and we're excluding headings
        if ($this->settings['exclude_headings'] && preg_match('/^h[1-6]$/i', $node->nodeName)) {
            return;
        }
        
        // Skip if this is a link or inside a link and we're excluding links
        if ($this->settings['exclude_links']) {
            if ($node->nodeName === 'a' || $xpath->query('ancestor::a', $node)->length > 0) {
                return;
            }
        }
        
        // If this is a text node, process it
        if ($node->nodeType === XML_TEXT_NODE) {
            $this->process_text_node($node, $link_count);
            return;
        }
        
        // Process child nodes
        if ($node->hasChildNodes()) {
            // Create a copy of the child nodes to avoid modification issues
            $children = array();
            foreach ($node->childNodes as $child) {
                $children[] = $child;
            }
            
            foreach ($children as $child) {
                $this->process_node($child, $xpath, $link_count);
            }
        }
    }

    /**
     * Process a text node for auto linking
     *
     * @param DOMText $node The text node to process
     * @param int $link_count Reference to the link count
     * @return void
     */
    private function process_text_node($node, &$link_count) {
        $text = $node->nodeValue;
        $modified = false;
        
        // Track keywords used in this node
        $keyword_counts = array();
        
        foreach ($this->keywords as $keyword => $url) {
            // Skip empty keywords
            if (empty($keyword)) {
                continue;
            }
            
            // Check if we've reached the maximum links per post
            $max_links = $this->settings['max_links_per_post'];
            if ($max_links > 0 && $link_count >= $max_links) {
                break;
            }
            
            // Check if we've reached the maximum links for this keyword
            if (!isset($keyword_counts[$keyword])) {
                $keyword_counts[$keyword] = 0;
            }
            
            $max_per_keyword = $this->settings['max_links_per_keyword'];
            if ($keyword_counts[$keyword] >= $max_per_keyword) {
                continue;
            }
            
            // Prepare the pattern
            $pattern = preg_quote($keyword, '/');
            $flags = $this->settings['case_sensitive'] ? '' : 'i';
            
            // Look for the keyword with word boundaries
            if (preg_match('/\b' . $pattern . '\b/' . $flags, $text)) {
                // Replace only the first occurrence
                $replacement = '<a href="' . esc_url($url) . '" target="' . esc_attr($this->settings['link_target']) . '">' . $keyword . '</a>';
                $text = preg_replace('/\b' . $pattern . '\b/' . $flags, $replacement, $text, 1);
                
                $modified = true;
                $keyword_counts[$keyword]++;
                $link_count++;
                
                // Check if we've reached the maximum links
                if ($max_links > 0 && $link_count >= $max_links) {
                    break;
                }
                
                // Check if we've reached the maximum links for this keyword
                if ($keyword_counts[$keyword] >= $max_per_keyword) {
                    continue;
                }
            }
        }
        
        // If the text was modified, replace the node
        if ($modified) {
            $fragment = $node->ownerDocument->createDocumentFragment();
            @$fragment->appendXML($text);
            $node->parentNode->replaceChild($fragment, $node);
        }
    }
}

// Initialize the auto linker
$willsx_auto_linker = new WillsX_Auto_Linker(); 