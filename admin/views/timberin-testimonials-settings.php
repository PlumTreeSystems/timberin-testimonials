<div class="wrap">
<h1>Timberin testimonial settings</h1>

    <form method="post">
        <table class="form-table">
            <tr>
                <td colspan='2'>
                    <h2>Import existing testimonials (all posts with type "testimonial"):</h2>
                </td>
                <td colspan='2'>
                    <span><?php echo $import ?></span>
                    <input type="submit" name="pts_import_testimonials" value='import'>
                </td>
            </tr>
        </table>
    </form>

    <form method="post" action="options.php">
        <?php settings_fields( 'timberin-testimonials-settings-group' ); ?>
    <table class="form-table">
        <tr>
            <td colspan='2'>
                <h2>Export url:</h2>
            </td>
            <td colspan='2'>
                <input type="text" name="url" disabled value='<?php echo $url ?>'>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <h2>Google maps api key:</h2>
            </td>
            <td colspan='2'>
                <input type="text" name="google_api_key" value='<?php echo get_option('google_api_key') ?>'>
            </td>
        </tr>
    </table>
        <?php submit_button(); ?>
    </form>
</div>