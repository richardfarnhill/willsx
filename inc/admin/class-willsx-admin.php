public function add_menu_items() {
    // ...existing code...
    
    // Update capability for main menu
    add_menu_page(
        'WillsX Dashboard',
        'WillsX',
        'edit_posts', // Change from 'manage_options' to 'edit_posts'
        'willsx-dashboard',
        array($this, 'render_dashboard'),
        'dashicons-businessman',
        2
    );

    // Update submenu capabilities
    add_submenu_page(
        'willsx-dashboard',
        'Partner Management',
        'Partners',
        'edit_posts',
        'willsx-partners',
        array($this, 'render_partners_page')
    );

    // Add documentation viewer page
    add_submenu_page(
        'willsx-dashboard',
        'Documentation',
        'Documentation',
        'edit_posts',
        'willsx-documentation',
        array($this, 'render_documentation_viewer')
    );
    
    // ...existing code...
}

public function render_documentation_viewer() {
    require_once WILLSX_PLUGIN_PATH . 'inc/admin/templates/documentation-viewer.php';
}

public function modify_doc_links($content) {
    // Convert documentation links to use viewer
    $content = preg_replace(
        '/<a href="([^"]+\.md)"/',
        '<a href="' . admin_url('admin.php?page=willsx-documentation&doc=$1') . '"',
        $content
    );
    return $content;
}
