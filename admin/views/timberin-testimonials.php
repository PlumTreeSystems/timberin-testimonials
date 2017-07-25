<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
<table>
    <tr>
        <td colspan='2'>
            <h2>Thumbnail</h2>
        </td>
        <td colspan='2'>
            <div class='image-preview-wrapper'>
                <img id='image-preview' src='<?php echo  wp_get_attachment_url($values['tt_img']) ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
            </div>
            <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
            <input type='hidden' name='tt_img' id='image_attachment_id' value='<?php echo $values['tt_img'] ?>'>

        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <h2>Client name</h2>
        </td>
        <td colspan='2'>
            <input type='text'
                   name='tt_client'
                   value='<?php echo $values['tt_client'] ?>'
                   style='width: 500px;'
            >
        </td>
    </tr>


    <tr>
        <td colspan='2'>
            <h2>Category</h2>
        </td>
        <td colspan='2'>
            <select name='tt_category'>
                <?php
                foreach ($categories as $k => $v){
                    $selected = $k == $values['tt_category'] ? 'selected' : '';
                    echo "<option $selected value='$k'>$v</option>";
                }
                ?>
            </select>
        </td>
    </tr>


    <tr>
        <td colspan='2'>
            <h2>Address</h2>
        </td>
        <td colspan='2'>
            <input
                   id='tt_address'
                   type='text'
                   name='tt_address'
                   value='<?php echo $values['tt_address'] ?>'
                   style='width: 500px;'
            >

        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <h2>Coordinates</h2>
        </td>
        <td colspan='2'>
            <div id="map"></div>
            Latitude:
            <input
                    readonly
                   id='tt_coord_long'
                   type='text'
                   name='tt_coord_long'
                   value='<?php echo $values['tt_coord_long'] ?>'

            >
            <br>
            Longitude:
            <input
                    readonly
                   id='tt_coord_lat'
                   type='text'
                   name='tt_coord_lat'
                   value='<?php echo $values['tt_coord_lat'] ?>'

            >
        </td>
    </tr>

</table>


<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('google_api_key')?>&callback=initMap">
</script>