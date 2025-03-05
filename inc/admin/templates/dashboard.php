















// ...existing code...</div>    </ul>        ?>        }            echo '<li><a href="' . admin_url('admin.php?page=willsx-documentation&doc=' . $filename) . '">' . $title . '</a></li>';            $title = ucfirst(str_replace('-', ' ', pathinfo($filename, PATHINFO_FILENAME)));            $filename = basename($doc);        foreach ($docs as $doc) {        $docs = glob($docs_dir . '*.md');        $docs_dir = WILLSX_PLUGIN_PATH . 'DOCUMENTATION/';        <?php    <ul>    <h3>Documentation</h3><div class="willsx-dashboard-docs">// ...existing code...