<?php
/*
Plugin Name: HTML5 GEO Users Location
Plugin URI: http://coderspress.com/
Description: This plugin will attempt to GEO locate your website visitors
Version: 2015.0508
Updated: 8th May 2015
Author: sMarty
Author URI: http://coderspress.com
WP_Requires: 3.8.1
WP_Compatible: 4.2.2
License: http://creativecommons.org/licenses/GPL/2.0
*/
add_action( 'init', 'HTML5_plugin_updater' );
function HTML5_plugin_updater() {
	if ( is_admin() ) { 
	include_once( dirname( __FILE__ ) . '/updater.php' );
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'HTML5-GEO-Location',
			'api_url' => 'https://api.github.com/repos/CodersPress/HTML5-GEO-Location',
			'raw_url' => 'https://raw.github.com/CodersPress/HTML5-GEO-Location/master',
			'github_url' => 'https://github.com/CodersPress/HTML5-GEO-Location',
			'zip_url' => 'https://github.com/CodersPress/HTML5-GEO-Location/zipball/master',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '4.2.2',
			'readme' => 'README.md',
			'access_token' => '94bb5a8d990740f396f0eafa9bf2bf570288071d',
		);
		new WP_HTML5_UPDATER( $config );
	}
}

function html_geo_menu() {
	add_menu_page('HTML5 GEO Users Location Settings', 'HTML5 GEO', 'administrator', __FILE__, 'html_geo_settings_page',plugins_url('/images/icon-arrow.gif', __FILE__));
	add_action( 'admin_init', 'register_html_geo_settings' );
}
add_action('admin_menu', 'html_geo_menu');
function register_html_geo_settings() {
	register_setting( 'html-geo-settings-group', 'mobile_only_option' );
}
function html_geo_defaults()
{
    $option = array(
        'mobile_only_option' => 'no',
    );
    foreach ( $option as $key => $value )
    {
       if (get_option($key) == NULL) {
        update_option($key, $value);
       }
    }
    return;
}
register_activation_hook(__FILE__, 'html_geo_defaults');
function html_geo_settings_page() {
if ($_REQUEST['settings-updated']=='true') {
echo '<div id="message" class="updated fade"><p><strong>Plugin setting saved.</strong></p></div>';
}
?>
<div class="wrap">
    <h2>HTML5 GEO Users Location Setting</h2>
    <hr />
<form method="post" action="options.php">
    <?php settings_fields("html-geo-settings-group");?>
    <?php do_settings_sections("html-geo-settings-group");?>
    <table class="widefat" style="width:600px;">
        <thead style="background:#2EA2CC;color:#fff;">
            <tr>
                <th style="color:#fff;">Usuage</th>
                <th style="color:#fff;"></th>
                <th style="color:#fff;"></th>
            </tr>
        </thead>
<tr>
<td>Mobile Only</td>
<td> 
        <select name="mobile_only_option" />
        <option value="yes" <?php if ( get_option('mobile_only_option') == 'yes' ) echo 'selected="selected"'; ?>>Yes</option>
        <option value="no" <?php if ( get_option('mobile_only_option') == 'no' ) echo 'selected="selected"'; ?>>No</option>
         </select>
</td>
<td></td>
        </tr>
  </table>
    <?php submit_button(); ?>
</form>
</div>
<?php
} 
add_action( 'wp_footer', 'location', 100);
function location(){
if ( is_home() ) {
?>
<script type='text/javascript'>
if(typeof google == 'undefined'){
        document.write('<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></'+'script>');
  }
</script>
<!-- HTML5 GEO Users Location Version 9 -->
<script type='text/javascript'>
function user_geo() {
    jQuery(document).ready(function () {
        var lat = "<?php echo $_SESSION['mylocation']['lat']?>";
        var log = "<?php echo $_SESSION['mylocation']['log']?>";
        function loadLocation() {
            if (navigator.geolocation) {
                if (lat) {
                    latitude = lat;
                    longitude = log;
                    updateDisplay();
                } else {
                    navigator.geolocation.getCurrentPosition(
                    success_handler,
                    error_handler, {
                        timeout: 10000
                    });
                }
            }
        }
        loadLocation();
        function success_handler(position) {
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(latitude, longitude);
            geocoder.geocode({
                'latLng': latlng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    console.log(results)
                    if (results[1]) {
                        for (var i = 0; i < results[0].address_components.length; i++) {
                            for (var b = 0; b < results[0].address_components[i].types.length; b++) {
                                if (results[0].address_components[i].types[b] == "country") {
                                    country = results[0].address_components[i];
                                    break;
                                }
                            }
                        }
                        jQuery("input[name=lat]").val(latitude);
                        jQuery("input[name=log]").val(longitude);
                        jQuery("input[name=country]").val(country.short_name);
                        jQuery('.modal-footer > form:nth-child(1)').submit();
                    }
                }
            });
        }
        function updateDisplay() {
          if(typeof map != 'undefined'){
            map.setOptions({
                'zoom': <?php echo $GLOBALS['CORE_THEME']['google_zoom1'];?>,
                   'center': new google.maps.LatLng(latitude, longitude)
            });
            placeMarker(latitude, longitude, '#', '<div class="text-center">Change your location by clicking the link below. </div>', image_here, 'wanna dance');
         }
         } 

        function error_handler(error) {
            var locationError = '';
          jQuery('.MyLocationLi > a:nth-child(1)').click()
        }
    });
};
var mobileOnly = "<?php echo get_option( 'mobile_only_option' ); ?>";
if( mobileOnly === "yes") {
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) { user_geo(); }
} else { user_geo(); }
</script>
<?php
  }
}

?>