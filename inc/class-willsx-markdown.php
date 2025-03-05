<?php
if (!defined('ABSPATH')) exit;

class WillsX_Markdown {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function convert($text) {
        // Basic Markdown conversion
        $text = $this->escape_html($text);
        
        // Headers
        $text = preg_replace('/^##### (.*?)$/m', '<h5>$1</h5>', $text);
        $text = preg_replace('/^#### (.*?)$/m', '<h4>$1</h4>', $text);
        $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);

        // Lists
        $text = preg_replace('/^\- (.*?)$/m', '<li>$1</li>', $text);
        $text = preg_replace('/^\* (.*?)$/m', '<li>$1</li>', $text);
        $text = str_replace("<li>\n", "<li>", $text);
        $text = '<ul>' . $text . '</ul>';
        
        // Links
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $text);
        
        // Paragraphs
        $text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';
        
        // Clean up
        $text = str_replace('<ul><p>', '<ul>', $text);
        $text = str_replace('</p></ul>', '</ul>', $text);
        
        return $text;
    }

    private function escape_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
