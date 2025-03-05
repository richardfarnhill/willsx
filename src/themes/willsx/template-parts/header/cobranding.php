<?php
/**
 * Co-branding header component
 * 
 * Displays co-branding information for partners
 * 
 * @package WillsX
 */

// Get the partner data
$partner = isset($args['partner']) ? $args['partner'] : false;

if (!$partner) {
    return;
}

// Get partner name and logo
$partner_name = $partner['name'] ?? '';
$partner_logo = $partner['logo'] ?? '';

if (empty($partner_name)) {
    return;
}
?>
<div class="cobranding-header">
    <div class="cobranding-content">
        <?php if (!empty($partner_logo)) : ?>
            <div class="cobranding-logo">
                <img src="<?php echo esc_url($partner_logo); ?>" alt="<?php echo esc_attr($partner_name); ?> logo" />
            </div>
        <?php endif; ?>
        
        <div class="cobranding-text">
            <p>
                <?php 
                printf(
                    esc_html__('In partnership with %s', 'willsx'),
                    esc_html($partner_name)
                );
                ?>
            </p>
        </div>
    </div>
</div> 