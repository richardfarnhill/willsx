<?php
/**
 * Cookie consent notice
 * 
 * @package WillsX
 */

// Check if cookie notice has been accepted
if (isset($_COOKIE['willsx_cookie_consent'])) {
    return;
}
?>

<div id="cookie-notice" class="cookie-notice">
    <div class="cookie-notice-container">
        <div class="cookie-notice-content">
            <h3><?php esc_html_e('Cookie Notice', 'willsx'); ?></h3>
            <p>
                <?php esc_html_e('We use cookies to enhance your experience on our website. By continuing to browse, you agree to our use of cookies, including third-party cookies used for analytics and advertising.', 'willsx'); ?>
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" class="cookie-more-info">
                    <?php esc_html_e('Learn more', 'willsx'); ?>
                </a>
            </p>
        </div>
        
        <div class="cookie-notice-actions">
            <button id="cookie-notice-accept-all" class="cookie-notice-button cookie-notice-accept-all">
                <?php esc_html_e('Accept All', 'willsx'); ?>
            </button>
            
            <button id="cookie-notice-preferences" class="cookie-notice-button cookie-notice-preferences">
                <?php esc_html_e('Preferences', 'willsx'); ?>
            </button>
            
            <button id="cookie-notice-accept-necessary" class="cookie-notice-button cookie-notice-accept-necessary">
                <?php esc_html_e('Necessary Only', 'willsx'); ?>
            </button>
        </div>
    </div>

    <div id="cookie-preferences-modal" class="cookie-preferences-modal">
        <div class="cookie-preferences-content">
            <h3><?php esc_html_e('Cookie Preferences', 'willsx'); ?></h3>
            
            <div class="cookie-preferences-section">
                <div class="cookie-preference">
                    <label>
                        <input type="checkbox" name="cookie_necessary" checked disabled>
                        <span><?php esc_html_e('Necessary', 'willsx'); ?></span>
                    </label>
                    <p><?php esc_html_e('These cookies are required for the website to function and cannot be disabled.', 'willsx'); ?></p>
                </div>
                
                <div class="cookie-preference">
                    <label>
                        <input type="checkbox" name="cookie_analytics" id="cookie-analytics">
                        <span><?php esc_html_e('Analytics', 'willsx'); ?></span>
                    </label>
                    <p><?php esc_html_e('These cookies help us understand how visitors interact with our website.', 'willsx'); ?></p>
                </div>
                
                <div class="cookie-preference">
                    <label>
                        <input type="checkbox" name="cookie_marketing" id="cookie-marketing">
                        <span><?php esc_html_e('Marketing', 'willsx'); ?></span>
                    </label>
                    <p><?php esc_html_e('These cookies are used to track visitors across websites to display relevant advertisements.', 'willsx'); ?></p>
                </div>
            </div>
            
            <div class="cookie-preferences-actions">
                <button id="cookie-preferences-save" class="cookie-notice-button">
                    <?php esc_html_e('Save Preferences', 'willsx'); ?>
                </button>
                <button id="cookie-preferences-cancel" class="cookie-notice-button cookie-notice-secondary">
                    <?php esc_html_e('Cancel', 'willsx'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cookie notice elements
    const cookieNotice = document.getElementById('cookie-notice');
    const acceptAllButton = document.getElementById('cookie-notice-accept-all');
    const preferencesButton = document.getElementById('cookie-notice-preferences');
    const acceptNecessaryButton = document.getElementById('cookie-notice-accept-necessary');
    
    // Preferences modal elements
    const preferencesModal = document.getElementById('cookie-preferences-modal');
    const savePreferencesButton = document.getElementById('cookie-preferences-save');
    const cancelPreferencesButton = document.getElementById('cookie-preferences-cancel');
    const analyticsCheckbox = document.getElementById('cookie-analytics');
    const marketingCheckbox = document.getElementById('cookie-marketing');
    
    // Set cookie function
    function setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
    }
    
    // Hide cookie notice
    function hideCookieNotice() {
        cookieNotice.style.display = 'none';
    }
    
    // Set consent
    function setConsent(analytics, marketing) {
        // First set the consent cookie
        setCookie('willsx_cookie_consent', 'accepted', 365);
        
        // Then set specific consent types
        setCookie('willsx_analytics_consent', analytics ? 'accepted' : 'rejected', 365);
        setCookie('willsx_marketing_consent', marketing ? 'accepted' : 'rejected', 365);
        
        // Then initialize GA4 with correct settings if available
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': analytics ? 'granted' : 'denied',
                'ad_storage': marketing ? 'granted' : 'denied',
                'ad_user_data': marketing ? 'granted' : 'denied',
                'ad_personalization': marketing ? 'granted' : 'denied'
            });
        }
        
        hideCookieNotice();
    }
    
    // Accept all
    acceptAllButton.addEventListener('click', function() {
        setConsent(true, true);
    });
    
    // Accept necessary only
    acceptNecessaryButton.addEventListener('click', function() {
        setConsent(false, false);
    });
    
    // Show preferences
    preferencesButton.addEventListener('click', function() {
        preferencesModal.style.display = 'flex';
    });
    
    // Cancel preferences
    cancelPreferencesButton.addEventListener('click', function() {
        preferencesModal.style.display = 'none';
    });
    
    // Save preferences
    savePreferencesButton.addEventListener('click', function() {
        const analyticsAccepted = analyticsCheckbox.checked;
        const marketingAccepted = marketingCheckbox.checked;
        
        setConsent(analyticsAccepted, marketingAccepted);
        preferencesModal.style.display = 'none';
    });
});
</script> 