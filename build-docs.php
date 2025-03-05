<?php
// Ensure directories exist
$docs_dir = __DIR__ . '/documentation';
$build_dir = __DIR__ . '/build/docs';

if (!file_exists($docs_dir)) {
    mkdir($docs_dir, 0777, true);
}

if (!file_exists($build_dir)) {
    mkdir($build_dir, 0777, true);
}

// Default content if no changelog exists
$default_content = "# WillsX Documentation\n\nDocumentation is being set up.";

// Try to get changelog, use default if not found
$changelog_path = $docs_dir . '/CHANGELOG.md';
$content = file_exists($changelog_path) 
    ? file_get_contents($changelog_path) 
    : $default_content;

$html_content = convertMarkdownToHtml($content);
file_put_contents($build_dir . '/index.html', wrapInTemplate('Documentation', $html_content));

function wrapInTemplate($title, $content) {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>WillsX - {$title}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: system-ui; max-width: 800px; margin: 0 auto; padding: 2rem; }
        pre { background: #f6f8fa; padding: 1rem; border-radius: 6px; }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;
}

function convertMarkdownToHtml($markdown) {
    // Simple markdown conversion - you might want to use a proper parser
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $markdown);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $markdown);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $markdown);
    $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<li>.*<\/li>\n)+/', "<ul>\n$0</ul>\n", $html);
    return $html;
}
