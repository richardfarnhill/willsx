<?php
/**
 * Main API class for WillsX
 */

if (!defined('ABSPATH')) {
    exit;
}

class WillsX_API {
    /**
     * API namespace
     */
    private $namespace = 'willsx/v1';

    /**
     * Initialize the API
     */
    public function init() {
        // Register REST routes
        add_action('rest_api_init', array($this, 'register_routes'));
        
        // Initialize components
        $this->init_components();
        
        // CORS handling
        add_action('init', array($this, 'handle_cors'));
    }
    
    /**
     * Initialize API components
     */
    private function init_components() {
        // Partners API
        $partners = new WillsX_Partners();
        $partners->init();
        
        // Auth API
        $auth = new WillsX_Auth();
        $auth->init();
        
        // Will Process API
        $will_process = new WillsX_Will_Process();
        $will_process->init();
        
        // Bookings API
        $bookings = new WillsX_Bookings();
        $bookings->init();
    }
    
    /**
     * Register core routes
     */
    public function register_routes() {
        // Core health check endpoint
        register_rest_route($this->namespace, '/health', array(
            'methods' => 'GET',
            'callback' => array($this, 'health_check'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * Handle CORS for API requests
     */
    public function handle_cors() {
        // Allow from trusted origins only
        $allowed_origins = array(
            'http://localhost:3000', // Local development
            'https://willsx.vercel.app', // Production frontend
        );
        
        $origin = get_http_origin();
        
        if ($origin && in_array($origin, $allowed_origins)) {
            header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Authorization, Content-Type');
            
            if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
                status_header(200);
                exit;
            }
        }
    }
    
    /**
     * API health check endpoint
     */
    public function health_check() {
        return array(
            'status' => 'ok',
            'version' => WILLSX_API_VERSION,
            'timestamp' => current_time('mysql')
        );
    }
}
