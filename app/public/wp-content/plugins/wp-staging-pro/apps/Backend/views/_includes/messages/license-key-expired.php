<div class="error">
    <p>
        <?php
        echo sprintf( __(
                        'WP Staging pro license is expired. You need a valid license key to use the push feature and to get further updates. Updates are important to make sure that your version of WP Staging is compatible with your version of WordPress and to ensure to prevent any data loss while using WP Staging.' .
                        '<br><br><a href="%s" target="_blank"><strong>Renew Your License Key Now</strong></a>', 'wpstg'), 
                'https://wp-staging.com/checkout/?nocache=true&edd_license_key='.$licensekey.'&download_id=11'
                );
        ?>
    </p>
</div>