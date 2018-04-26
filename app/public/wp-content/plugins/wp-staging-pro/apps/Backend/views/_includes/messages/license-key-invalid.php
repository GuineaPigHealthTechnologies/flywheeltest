<div class="error">
    <p>
        <?php
        echo sprintf( __(
                        '<strong>WP Staging Pro:</strong> Your license of WP Staging Pro is invalid or deactivated. You need a valid license key to use the push feature and to get further updates. Updates are important to make sure that your version of WP Staging is compatible with your version of WordPress and to ensure to prevent any data loss while using WP Staging. Get the license key from <a href="%1$s" target="_blank">your account</a>.' .
                        '<br><br><a href="%2$s" target="_self"><strong>Enter License Key Now</strong></a>', 'wpstg'), 
                'https://wp-staging.com/?utm_source=admin_notice&utm_medium=plugin&utm_campaign=license_invalid',
                admin_url() . 'admin.php?page=wpstg-license',
                'your account' 
        );
        ?>
    </p>
</div>