<?php
	if(isset($_POST['settings_form']) && $_POST['settings_form'] != '') {

		if(!get_option('eopa_google_map_api_key')) {
			add_option('eopa_google_map_api_key', $_POST['api_key']);
		} else {
			update_option('eopa_google_map_api_key', $_POST['api_key']);
		}

		if(!get_option('eopa_default_lat')) {
			add_option('eopa_default_lat', $_POST['default_lat']);
		} else {
			update_option('eopa_default_lat', $_POST['default_lat']);
		}

		if(!get_option('eopa_default_long')) {
			add_option('eopa_default_long', $_POST['default_long']);
		} else {
			update_option('eopa_default_long', $_POST['default_long']);
		}

		if(!get_option('eopa_default_zoom')) {
			add_option('eopa_default_zoom', $_POST['default_zoom']);
		} else {
			update_option('eopa_default_zoom', $_POST['default_zoom']);
		}
	}

	$api_key = get_option('eopa_google_map_api_key') != '' ? get_option('eopa_google_map_api_key') : '';

	$default_lat = get_option('eopa_default_lat') != '' ? get_option('eopa_default_lat') : '';
	$default_long = get_option('eopa_default_long') != '' ? get_option('eopa_default_long') : '';
	$default_zoom = get_option('eopa_default_zoom') != '' ? get_option('eopa_default_zoom') : '';
?>

	
	<form action="" method="post" id="settings_form">
		<h3>Settings</h3>
		<div class="form_field">
			<label>Google Map API Key</label>
			<input type="text" name="api_key" value="<?php echo $api_key;?>">
		</div>

		<div class="form_field">
			<label>Default Location</label>
			<input type="text" class="lat-field" name="default_lat" value="<?php echo $default_lat;?>"> - 
			<input type="text" class="long-field" name="default_long" value="<?php echo $default_long;?>">
		</div>

		<div class="form_field">
			<label>Default Map Zoom</label>
			<input type="text" class="zoom-field" name="default_zoom" value="<?php echo $default_zoom;?>" placeholder="Latitude"/>
		</div>

		<div class="form_field">
			<input type="submit" class="button-primary" name="settings_form" value="Submit" placeholder="Longitude" />
		</div>
	</form>