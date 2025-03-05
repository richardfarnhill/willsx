<?php
if (!defined('ABSPATH')) exit;

$file = isset($_GET['doc']) ? sanitize_text_field($_GET['doc']) : '';
$doc_path = WILLSX_PLUGIN_PATH . 'DOCUMENTATION/' . $file;

if (!empty($file) && file_exists($doc_path)) {
    $markdown = WillsX_Markdown::get_instance();
    $content = file_get_contents($doc_path);
    echo '<div class="wrap willsx-doc-viewer">';
    echo $markdown->convert($content);
    echo '</div>';
} else {
    echo '<div class="wrap"><p>Documentation not found.</p></div>';
}
