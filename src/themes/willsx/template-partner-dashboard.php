<?php
/**
 * Template Name: Partner Dashboard
 *
 * @package WillsX
 */

// Redirect if user is not a partner
if (!willsx_user_has_partner_cap()) {
    wp_redirect(home_url());
    exit;
}

get_header();

// Get partner data
$partner_id = willsx_get_current_partner_id();
$partner_data = get_post_meta($partner_id);
$logo_url = wp_get_attachment_url($partner_data['_partner_logo'][0]);
?>

<div class="partner-dashboard">
    <div class="dashboard-header">
        <div class="partner-branding">
            <?php if ($logo_url) : ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="Partner Logo" class="partner-logo">
            <?php endif; ?>
            <h1><?php echo get_the_title($partner_id); ?> Dashboard</h1>
        </div>
        
        <div class="quick-stats">
            <div class="stat-box">
                <h3>Active Clients</h3>
                <span class="stat-number"><?php echo willsx_get_partner_client_count($partner_id); ?></span>
            </div>
            <div class="stat-box">
                <h3>Completed Wills</h3>
                <span class="stat-number"><?php echo willsx_get_partner_completed_wills_count($partner_id); ?></span>
            </div>
            <div class="stat-box">
                <h3>Revenue</h3>
                <span class="stat-number">£<?php echo willsx_get_partner_revenue($partner_id); ?></span>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-section">
            <h2>Client Management</h2>
            <table id="clients-table" class="display">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Will Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $clients = willsx_get_partner_clients($partner_id);
                    foreach ($clients as $client) :
                        $will_status = willsx_get_client_will_status($client->ID);
                    ?>
                    <tr>
                        <td><?php echo esc_html($client->display_name); ?></td>
                        <td><?php echo esc_html($client->user_email); ?></td>
                        <td><?php echo esc_html($will_status); ?></td>
                        <td><?php echo get_user_meta($client->ID, 'last_updated', true); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=willsx-client&id=' . $client->ID); ?>" class="button">View Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-section">
            <h2>Branding Settings</h2>
            <form id="partner-branding-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('update_partner_branding', 'partner_branding_nonce'); ?>
                <div class="form-group">
                    <label for="partner_logo">Logo</label>
                    <div class="logo-preview">
                        <?php if ($logo_url) : ?>
                            <img src="<?php echo esc_url($logo_url); ?>" alt="Current Logo">
                        <?php endif; ?>
                    </div>
                    <input type="file" id="partner_logo" name="partner_logo" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="primary_color">Primary Color</label>
                    <input type="color" id="primary_color" name="primary_color" 
                           value="<?php echo esc_attr($partner_data['_primary_color'][0] ?? '#000000'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="secondary_color">Secondary Color</label>
                    <input type="color" id="secondary_color" name="secondary_color"
                           value="<?php echo esc_attr($partner_data['_secondary_color'][0] ?? '#ffffff'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="accent_color">Accent Color</label>
                    <input type="color" id="accent_color" name="accent_color"
                           value="<?php echo esc_attr($partner_data['_accent_color'][0] ?? '#0066cc'); ?>">
                </div>
                
                <button type="submit" class="button button-primary">Save Branding Settings</button>
            </form>
        </div>

        <div class="dashboard-section">
            <h2>Analytics</h2>
            <div class="analytics-container">
                <canvas id="revenue-chart"></canvas>
                <canvas id="clients-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize DataTables
    $('#clients-table').DataTable();

    // Initialize Charts
    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
    const clientsCtx = document.getElementById('clients-chart').getContext('2d');

    // Revenue Chart
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(willsx_get_revenue_labels($partner_id)); ?>,
            datasets: [{
                label: 'Monthly Revenue',
                data: <?php echo json_encode(willsx_get_revenue_data($partner_id)); ?>,
                borderColor: '<?php echo esc_js($partner_data['_primary_color'][0] ?? '#0066cc'); ?>',
                fill: false
            }]
        }
    });

    // Clients Chart
    new Chart(clientsCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(willsx_get_clients_labels($partner_id)); ?>,
            datasets: [{
                label: 'New Clients',
                data: <?php echo json_encode(willsx_get_clients_data($partner_id)); ?>,
                backgroundColor: '<?php echo esc_js($partner_data['_secondary_color'][0] ?? '#0066cc'); ?>'
            }]
        }
    });

    // Handle logo upload preview
    $('#partner_logo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.logo-preview img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<style>
.partner-dashboard {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.partner-branding {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.partner-logo {
    max-width: 150px;
    height: auto;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-box {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--partner-primary-color, #0066cc);
}

.dashboard-section {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.logo-preview {
    margin: 1rem 0;
}

.logo-preview img {
    max-width: 200px;
    height: auto;
}

.analytics-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        text-align: center;
    }

    .quick-stats {
        grid-template-columns: 1fr;
    }

    .analytics-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>