jQuery(document).ready(function($) {
    // Tab switching
    $('.dashboard-navigation a').on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        
        // Update navigation
        $('.dashboard-navigation li').removeClass('active');
        $(this).parent().addClass('active');
        
        // Show target section
        $('.dashboard-section').removeClass('active');
        $(targetId).addClass('active');
    });

    // Branding form handling
    $('.branding-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: willsx_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'willsx_save_partner_branding',
                nonce: willsx_ajax.nonce,
                formData: formData
            },
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Branding settings saved successfully!');
                } else {
                    alert('Error saving branding settings. Please try again.');
                }
            },
            error: function() {
                alert('Error saving branding settings. Please try again.');
            }
        });
    });

    // Preview changes
    $('.preview-changes').on('click', function() {
        const primaryColor = $('#primary_color').val();
        const secondaryColor = $('#secondary_color').val();
        const accentColor = $('#accent_color').val();
        const fontFamily = $('#font_family').val();

        // Apply preview styles
        $(':root').css({
            '--partner-primary-color': primaryColor,
            '--partner-secondary-color': secondaryColor,
            '--partner-accent-color': accentColor,
            '--partner-font-family': fontFamily
        });
    });

    // Analytics charts
    function initCharts() {
        // Client Growth Chart
        const clientGrowthCtx = document.querySelector('#client-growth-chart').getContext('2d');
        new Chart(clientGrowthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Total Clients',
                    data: [65, 78, 90, 105, 118, 128],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Revenue Chart
        const revenueCtx = document.querySelector('#revenue-chart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue (£)',
                    data: [3200, 3500, 3800, 4000, 4100, 4250],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Completion Rate Chart
        const completionCtx = document.querySelector('#completion-chart').getContext('2d');
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Not Started'],
                datasets: [{
                    data: [75, 15, 10],
                    backgroundColor: [
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(201, 203, 207)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Traffic Sources Chart
        const trafficCtx = document.querySelector('#traffic-chart').getContext('2d');
        new Chart(trafficCtx, {
            type: 'pie',
            data: {
                labels: ['Direct', 'Organic', 'Referral', 'Social'],
                datasets: [{
                    data: [40, 30, 20, 10],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(54, 162, 235)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Initialize charts when analytics tab is shown
    $('a[href="#analytics"]').on('click', function() {
        setTimeout(initCharts, 100);
    });

    // Client management
    const clientsTable = $('.clients-table').DataTable({
        pageLength: 10,
        order: [[3, 'desc']],
        responsive: true,
        language: {
            search: 'Search clients:',
            paginate: {
                previous: '&lt;',
                next: '&gt;'
            }
        }
    });

    // Client search
    $('.search-input').on('keyup', function() {
        clientsTable.search(this.value).draw();
    });

    // Status filter
    $('.status-filter').on('change', function() {
        const status = $(this).val();
        clientsTable
            .column(2)
            .search(status)
            .draw();
    });

    // Settings form handling
    $('.settings-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: willsx_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'willsx_save_partner_settings',
                nonce: willsx_ajax.nonce,
                formData: formData
            },
            success: function(response) {
                if (response.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error saving settings. Please try again.');
                }
            },
            error: function() {
                alert('Error saving settings. Please try again.');
            }
        });
    });

    // Generate new API key
    $('#generate_new_key').on('click', function() {
        if (!confirm('Are you sure you want to generate a new API key? The old key will be invalidated.')) {
            return;
        }

        $.ajax({
            url: willsx_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'willsx_generate_api_key',
                nonce: willsx_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#api_key').val(response.data.api_key);
                    alert('New API key generated successfully!');
                } else {
                    alert('Error generating API key. Please try again.');
                }
            },
            error: function() {
                alert('Error generating API key. Please try again.');
            }
        });
    });

    // Date range filter for analytics
    $('#date-range').on('change', function() {
        const days = $(this).val();
        // Reload charts with new date range
        // This would typically make an AJAX call to get new data
        // For now, we'll just reinitialize with sample data
        initCharts();
    });
}); 