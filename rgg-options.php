<?php

if (RGG_IS_PRO) {
	require_once RGG_PLUGIN_DIR . '/pro/rgg_pro_options.php';
}

global $rgg_input_fields, $default_rgg_options;
$rgg_input_fields = array();

if (!defined('RGG_DEFAULT_TYPE')) { define('RGG_DEFAULT_TYPE', 'rgg'); };
if (!defined('RGG_DEFAULT_CLASS')) { define('RGG_DEFAULT_CLASS', ''); };
if (!defined('RGG_DEFAULT_REL')) { define('RGG_DEFAULT_REL', 'rgg'); };
if (!defined('RGG_DEFAULT_IDS')) { define('RGG_DEFAULT_IDS', ''); };
if (!defined('RGG_DEFAULT_MARGIN')) { define('RGG_DEFAULT_MARGIN', 2); };
if (!defined('RGG_DEFAULT_SCALE')) { define('RGG_DEFAULT_SCALE', 1.1); };
if (!defined('RGG_DEFAULT_MAXROWHEIGHT')) { define('RGG_DEFAULT_MAXROWHEIGHT', 200); };
if (!defined('RGG_DEFAULT_ROWSPAN')) { define('RGG_DEFAULT_ROWSPAN', 0); };
if (!defined('RGG_DEFAULT_INTIME')) { define('RGG_DEFAULT_INTIME', 100); };
if (!defined('RGG_DEFAULT_OUTTIME')) { define('RGG_DEFAULT_OUTTIME', 100); };
if (!defined('RGG_DEFAULT_CAPTIONS')) { define('RGG_DEFAULT_CAPTIONS', 'title'); };
if (!defined('RGG_DEFAULT_LINKED_IMAGE_SIZE')) { define('RGG_DEFAULT_LINKED_IMAGE_SIZE', 'large'); };
if (!defined('RGG_DEFAULT_ORDERBY')) { define('RGG_DEFAULT_ORDERBY', 'menu_order'); };
if (!defined('RGG_DEFAULT_LINK')) { define('RGG_DEFAULT_LINK', 'file'); };
if (!defined('RGG_DEFAULT_ID')) { define('RGG_DEFAULT_ID', ''); };
if (!defined('RGG_DEFAULT_SIZE')) { define('RGG_DEFAULT_SIZE', 'medium'); };
if (!defined('RGG_DEFAULT_INCLUDE')) { define('RGG_DEFAULT_INCLUDE', ''); };
if (!defined('RGG_DEFAULT_EXCLUDE')) { define('RGG_DEFAULT_EXCLUDE', ''); };
if (!defined('RGG_DEFAULT_ADMIN_LOCATION')) { define('RGG_DEFAULT_ADMIN_LOCATION', 'admin.php'); };
if (!defined('RGG_DEFAULT_LIGHTBOX')) { define('RGG_DEFAULT_LIGHTBOX', 'disabled'); };
if (!defined('RGG_DEFAULT_EFFECT')) { define('RGG_DEFAULT_EFFECT', 'bubble'); };

// set RGG-pro options to RGG-free values
if (!defined('RGG_DEFAULT_LICENSE_KEY')) { define('RGG_DEFAULT_LICENSE_KEY', ''); };
if (!defined('RGG_DEFAULT_LASTROWBEHAVIOR')) { define('RGG_DEFAULT_LASTROWBEHAVIOR', 'last_row_same_height'); };
if (!defined('RGG_DEFAULT_CAPTIONS_EFFECT')) { define('RGG_DEFAULT_CAPTIONS_EFFECT', 'none'); };
if (!defined('RGG_DEFAULT_CAPTIONS_INTIME')) { define('RGG_DEFAULT_CAPTIONS_INTIME', 0); };
if (!defined('RGG_DEFAULT_CAPTIONS_OUTTIME')) { define('RGG_DEFAULT_CAPTIONS_OUTTIME', 0); };


$rgg_options = get_option(RGG_OPTIONS); // load any saved options from DB
if (!is_array($rgg_options)) $rgg_options = array();

$default_rgg_options = array(); //
$default_rgg_options['type'] = RGG_DEFAULT_TYPE;
$default_rgg_options['class'] = RGG_DEFAULT_CLASS;
$default_rgg_options['rel'] = RGG_DEFAULT_REL;
$default_rgg_options['ids'] = RGG_DEFAULT_IDS;
$default_rgg_options['margin'] = RGG_DEFAULT_MARGIN;
$default_rgg_options['scale'] = RGG_DEFAULT_SCALE;
$default_rgg_options['maxrowheight'] = RGG_DEFAULT_MAXROWHEIGHT;
$default_rgg_options['rowspan'] = RGG_DEFAULT_ROWSPAN;
$default_rgg_options['intime'] = RGG_DEFAULT_INTIME;
$default_rgg_options['outtime'] = RGG_DEFAULT_OUTTIME;
$default_rgg_options['captions'] = RGG_DEFAULT_CAPTIONS;
$default_rgg_options['linked_image_size'] = RGG_DEFAULT_LINKED_IMAGE_SIZE;
$default_rgg_options['orderby'] = RGG_DEFAULT_ORDERBY;
$default_rgg_options['link'] = RGG_DEFAULT_LINK;
$default_rgg_options['id'] = RGG_DEFAULT_ID;
$default_rgg_options['size'] = RGG_DEFAULT_SIZE;
$default_rgg_options['include'] = RGG_DEFAULT_INCLUDE;
$default_rgg_options['exclude'] = RGG_DEFAULT_EXCLUDE;
$default_rgg_options['admin_location'] = RGG_DEFAULT_ADMIN_LOCATION;
$default_rgg_options['lightbox'] = RGG_DEFAULT_LIGHTBOX;
$default_rgg_options['effect'] = RGG_DEFAULT_EFFECT;

$default_rgg_options['license_key'] = RGG_DEFAULT_LICENSE_KEY;
$default_rgg_options['lastrowbehavior'] = RGG_DEFAULT_LASTROWBEHAVIOR;
$default_rgg_options['captions_effect'] = RGG_DEFAULT_CAPTIONS_EFFECT;
$default_rgg_options['captions_intime'] = RGG_DEFAULT_CAPTIONS_INTIME;
$default_rgg_options['captions_outtime'] = RGG_DEFAULT_CAPTIONS_OUTTIME;

$rgg_options = wp_parse_args($rgg_options,$default_rgg_options); // replace any missing options with the default options.

add_action('admin_init', function() {
	global $default_rgg_options;
	if(isset($_POST['reset'])) {
		if (!wp_verify_nonce($_POST['nonce'], 'rgg_reset_options')) {
			wp_die('Security check');
		}
		update_option(RGG_OPTIONS, $default_rgg_options);
	}
}, 10, 0);

add_action( 'admin_enqueue_scripts', 'load_page_options_wp_admin_style' );
function load_page_options_wp_admin_style() {
	wp_register_style( 'page_options_wp_admin_css', RGG_PLUGIN_DIR_URL.'/css/admin-style.css', false, RGG_VERSION );
	wp_enqueue_style( 'page_options_wp_admin_css' );
}

add_action('admin_menu', 'rgg_admin_add_page');
function rgg_admin_add_page() {
	global $rgg_options;
	if ($rgg_options['admin_location'] != 'admin.php') {
		add_submenu_page( $rgg_options['admin_location'], 'Responsive Gallery Grid Settings', 'RGG Gallery', 'manage_options', RGG_PLUGIN, 'rgg_options_page', 200 );
	} else {
		add_menu_page( 'Responsive Gallery Grid Settings', 'RGG Gallery', 'manage_options', RGG_PLUGIN, 'rgg_options_page', 'dashicons-layout', 200 );
	}
}

function rgg_options_page() {
	global $rgg_options, $rgg_input_fields;
	
	// Include in admin_enqueue_scripts action hook
	wp_enqueue_media();

	if (isset($_POST['reset'])) {
	    echo '<div id="message" class="updated fade"><p><strong>Settings restored to defaults</strong></p></div>';
	} else if (isset($_REQUEST['settings-updated'])) {
	    echo '<div id="message" class="updated fade"><p><strong>Settings updated</strong></p></div>';
	}

	rgg_setup_input_fields();

?>

<div class="wrap rgg-admin-wrap">
    <h2>Responsive Gallery Grid Settings</h2>
    <form action="options.php" method="post">
    <?php settings_fields(RGG_OPTIONS); ?>

    <?php

    $admin_form = rgg_get_admin_form_structure();

    $admin_form = apply_filters('rgg_filter_admin_form',$admin_form);

    rgg_print_admin_form($admin_form);

    ?>
    </form>

    <h3>Restore Default Settings</h3>
    <form method="post" id="reset-form" action="">
        <p class="submit">
            <input name="reset" class="button button-secondary" type="submit" value="Restore defaults" >
			<input type=hidden name="nonce" value="<?php echo wp_create_nonce('rgg_reset_options') ?>" />
            <input type="hidden" name="action" value="reset" />
        </p>
    </form>
    <script>
        (function($){
            $('#reset-form').submit(function() {
                return confirm('Are you sure you want to reset the plugin settings to the default values? All changes you have previously made will be lost.');
            });
            $()
        }(jQuery))
    </script>
</div>
 
<?php
}

function rgg_get_admin_form_structure($skip_submit = false) {

	$submit_button = '';
    if (!$skip_submit) {
        ob_start();
        submit_button();
        $submit_button = ob_get_clean();
    }

	$admin_form = array(
		'rggpromo' => array(
			'before' => '<h3>Support RGG</h3><p><span class="dashicons dashicons-heart" style="color:#b5322b;"></span> <a href="https://responsive-gallery-grid.bdwm.be/responsive-gallery-grid-pro/" target="_blank">Buy RGG Pro for only &euro; 5.90</a> to unlock captions, additional effects, special row options and premium support<br><span class="dashicons dashicons-star-filled" style="color:#ffb900;"></span> or <a href="https://login.wordpress.org/?redirect_to=https%3A%2F%2Fwordpress.org%2Fsupport%2Fplugin%2Fresponsive-gallery-grid%2Freviews%2F" target="_blank">Leave a review</a> if you like RGG.</p>',
			'fields'=>array(),
		),

		'general-settings' => array(
			'before' => '<div class="title"><h3>General Settings</h3></div>',
			'fields' => array('admin_location'),
			'after' => $submit_button,

		),

		'gallery-settings' => array(
			'before' => '<div class="title"><h3>Gallery Settings</h3><p>Default settings for each gallery. You can always overwrite these default options by modifying the <a href="https://responsive-gallery-grid.bdwm.be/shortcode-parameters" target="_blank">shortcode parameters</a>.</p></div>',
			'fields' => array(
				'type',
				'class',
				'rel',
				'linked_image_size',
				'lightbox',
			),
			'after' => $submit_button,

		),

		'display-settings' => array(
			'before' => '<div class="title"><h3>Display settings</h3><p>The gallery images are automatically scaled to fit the container, but you can somewhat control the look with these settings.</p></div>',
			'fields' => array(
				'margin',
				'maxrowheight',
                'rowspan'
			),
			'after' => $submit_button,

		),

		'animation-settings' => array(
			'before' => '<div class="title"><h3>Animation settings</h3><p>Tweak the mouse-over animation effects and times</p></div>',
			'fields' => array(
				'scale',
				'intime',
				'outtime',
				'effect',
			),
			'after' => $submit_button,

		),

		'captions' => array(
			'before' => '<div class="title"><h3>Captions</h3></div>',
			'fields' => array('captions'),
			'after' => $submit_button,

		),

		'native-settings' => array(
			'before' => '<div class="title"><h3>Native settings</h3><p>These relate to parameters that will work with the native gallery as well.</p></div>',
			'fields' => array(
				'orderby',
				'link',
				'size',
				'ids',
			),
			'after' => $submit_button,
		),
		'advanced-native-settings' => array(
			'before' => '<div class="title"><h3>Advanced native settings</h3><p>They are kind of useless in my opinion.</p></div>',
			'fields' => array(
				'include',
				'exclude',
				'id'
			),
			'after' => $submit_button,

		),
	);

	return $admin_form;
}

function rgg_print_admin_form($form) {
    global $rgg_input_fields;
    foreach ($form as $sections) {
	    if (isset($sections['before'])) {
		    echo $sections['before'];
	    }
	    echo '<div class="fieldset">';
        foreach ($sections['fields'] as $fieldname) {
            rgg_print_input_field($fieldname,$rgg_input_fields[$fieldname]);
        }
        if (isset($sections['after'])) {
            echo $sections['after'];
        }
        echo '</div>';
    }
}

function rgg_image_field($slug, $args) {
	
	global $rgg_options;
	
	$defaults = array(
		'title'=>'Image',
		'description' => '',
		'choose_text' => 'Choose an image',
		'update_text' => 'Use image',
		'default' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	$label; $description; $choose_text; $update_text; $default;
	
	if (!key_exists($slug, $rgg_options)) {
		$rgg_options[$slug] = $default;
	}
	
?>
    <span class="label"><?php echo $label; ?></span>
<?php
	if ($description) {
?>
    <p><?php echo $description; ?></p>
<?php
	}
?>
	<p>
		<div class="image-container" id="default-thumbnail-preview_<?php echo $slug ?>">
<?php
	if ($rgg_options[$slug] != '') {
		$img_info = wp_get_attachment_image_src($rgg_options[$slug], 'full');
		$img_src = $img_info[0];
?>
			<img src="<?php echo $img_src ?>" height="100">
<?php
	}
?>
		</div>
		<a class="choose-from-library-link" href="#"
			data-field="<?php echo RGG_OPTIONS.'_'.$slug ?>"
			data-image_container="default-thumbnail-preview_<?php echo $slug ?>"
		    data-choose="<?php echo $choose_text; ?>"
		    data-update="<?php echo $update_text; ?>"><?php _e( 'Choose image' ); ?>
		</a>
		<input type="hidden" value="<?php echo $rgg_options[$slug] ?>" id="<?php echo RGG_OPTIONS.'_'.$slug ?>" name="<?php echo RGG_OPTIONS.'['.$slug.']' ?>">
	</p>
<?php
	
}

function rgg_print_input_field($slug, $args) {
	global $rgg_options;

	if (isset($args['pro_only']) && $args['pro_only'] && !RGG_IS_PRO) {
	    // hide pro options if not RGG_IS_PRO
	    return;
    }

    if ($args['input_type'] == 'select') {
	    return rgg_input_select($slug,$args);
    }
	
	$defaults = array(
		'label'=>'',
		'description' => '',
		'default' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );

	$label = $args['label'];
	$description = $args['description'];
	$default = $args['default'];
	
	if (!key_exists($slug, $rgg_options)) {
		$rgg_options[$slug] = $default;
	}
	
?>	
	<p>
		<span class="label"><?php echo $label ?></span>
		<span class="field"><input type="text" data-default-value="<?php echo htmlspecialchars($default) ?>" value="<?php echo htmlspecialchars($rgg_options[$slug]) ?>" id="<?php echo RGG_OPTIONS.'_'.$slug ?>" name="<?php echo RGG_OPTIONS.'['.$slug.']' ?>"></span>
		<span class="description"><?php echo $description ?><?php if (!empty($default)) echo ' (Default: '.$default.')' ?></span>
	</p>
<?php

}

function rgg_input_select($slug, $args) {
	global $rgg_options;
	
	$defaults = array(
		'label'=>'',
		'desription' => '',
		'options' => array(), // array($name => $value)
		'default' => '',
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	
	$label; $description; $options; $default;
	
	if (!key_exists($slug, $rgg_options)) {
		$rgg_options[$slug] = $default;
	}
	
	// $first_element = array('-1' => '-- Select --');
	// $options = array_merge($first_element, $options);
	
?>	
	<p>
		<span class="label"><?php echo $label ?></span>
		<span class="field">
			<select id="<?php echo RGG_OPTIONS.'_'.$slug ?>" data-default-value="<?php echo $default ?>" name="<?php echo RGG_OPTIONS.'['.$slug.']' ?>">
<?php
	foreach($options as $value => $text) {
?>
				<option value="<?php echo $value ?>" <?php echo $rgg_options[$slug]==$value?'selected':'' ?>><?php echo $text ?></option>
<?php 
	}
?>
			</select>			
		</span>
		<span class="description"><?php echo $description ?><?php if (!empty($default)) echo ' (Default: '.$options[$default].')' ?></span>
	</p>
<?php

}

function rgg_checkbox($slug, $args) {
	global $rgg_options;
	
	$defaults = array(
		'label'=>'',
		'desription' => '',
		'default' => '',
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	
	$label; $description; $default;
	
?>	
	<p>
		<span class="label"><?php echo $label ?></span>
		<span class="field">
			
			<input type="checkbox" data-default-value="<?php echo $default ?>" name="<?php echo RGG_OPTIONS.'['.$slug.']' ?>" value="1" <?php checked('1', $rgg_options[$slug]) ?>>
		</span>
		<span class="description"><?php echo $description ?><?php if (!empty($default)) echo ' (Default: '.$default.')' ?></span>
	</p>
<?php
}

function rgg_setup_input_fields() {

    global $rgg_input_fields;

	$image_sizes = array();
	$registered_image_sizes = get_intermediate_image_sizes();

	foreach ($registered_image_sizes as $image_size) {
		$image_sizes[$image_size] = $image_size;
	}

	$image_sizes['full'] = 'full';

	$rgg_input_fields['admin_location'] = array(
		'input_type' => 'select',
		'label' => 'Admin Menu Location',
		'description' => 'Make your WordPress admin interface less bloated by adding the RGG settings page to a submenu',
		'options' => array(
			'admin.php'=> 'New top level menu',
			'index.php' => 'Dashboard', 'upload.php' => 'Media', 'themes.php'=>'Appearance', 'plugins.php'=>'Plugins', 'tools.php'=>'Tools', 'options-general.php'=>'Settings'),
		'default' => RGG_DEFAULT_ADMIN_LOCATION
	);

	$rgg_input_fields['lightbox'] = array(
		'input_type' => 'select',
		'label' => 'Lightbox',
		'description' => 'Choose your lightbox configuration',
		'options' => array(
			'disabled' => 'Disabled / use third party lightbox',
            'simplelightbox' => 'SimpleLightbox',
            'image-above' => 'Active image above gallery',
        ),
		'default' => RGG_DEFAULT_LIGHTBOX
	);

	$rgg_input_fields['type'] = array(
		'input_type' => 'select',
		'label' => 'Type',
		'description' => 'The gallery type. Note: if you choose the native gallery all the other options will be ignored!',
		'options' => array('rgg'=> 'Responsive Gallery Grid', 'native' => 'Native Gallery'),
		'default' => RGG_DEFAULT_TYPE
	);

	$rgg_input_fields['class'] = array(
		'input_type' => 'text',
		'label' => 'Class',
		'description' => 'Additional CSS class(es) that will be appended to the CSS class of the gallery container. If you wish to add multiple classes, seperate them with a space.',
		'default' => RGG_DEFAULT_CLASS
	);

	$rgg_input_fields['rel'] = array(
		'input_type' => 'text',
		'label' => 'Rel',
		'description' => 'The rel attribute of the image links. Used by lots of lightbox scripts to group images.',
		'default' => RGG_DEFAULT_REL
	);

	$rgg_input_fields['margin'] = array(
		'input_type' => 'text',
		'label' => 'Margin',
		'description' => 'A positive integer value indicating the number of pixels you want to appear between the images in the Responsive Gallery.',
		'default' => RGG_DEFAULT_MARGIN
	);

	$rgg_input_fields['scale'] = array(
		'input_type' => 'text',
		'label' => 'Scale',
		'description' => 'A positive decimal value indicating the factor by which the image-size is multiplied on mouse over.',
		'default' => RGG_DEFAULT_SCALE
	);

	$rgg_input_fields['maxrowheight'] = array(
		'input_type' => 'text',
		'label' => 'Max Row Height',
		'description' => 'A positive integer value indicating the maximum height, in pixels, of each row in the Responsive Gallery Grid.',
		'default' => RGG_DEFAULT_MAXROWHEIGHT
	);
	$rgg_input_fields['rowspan'] = array(
		'input_type' => 'text',
		'label' => 'Span some images over 2 rows',
		'description' => 'Make some images larger',
		'default' => RGG_DEFAULT_ROWSPAN
	);

	$rgg_input_fields['intime'] = array(
		'input_type' => 'text',
		'label' => 'Animation In time',
		'description' => 'A positive integer value indicating the time, in milliseconds, it will take for the mouse over animation to complete.',
		'default' => RGG_DEFAULT_INTIME
	);

	$rgg_input_fields['outtime'] = array(
		'input_type' => 'text',
		'label' => 'Animation Out Time',
		'description' => ' 	A positive integer value indicating the time, in milliseconds, it will take for the mouse out animation to complete.',
		'default' => RGG_DEFAULT_OUTTIME
	);

	$rgg_input_fields['orderby'] = array(
		'input_type' => 'select',
		'label' => 'Gallery Order',
		'description' => 'Choose which default order you would like to use for the gallery images',
		'options' => array('menu_order' => 'Use the order you chose manually', 'ID' => 'Order by media ID', 'title' => 'Order by title', 'date' => 'Order by the date and time the image was uploaded', 'modified' => 'Order by the date and time the image was last modified', 'rand' => 'Random order'),
		'default' => RGG_DEFAULT_ORDERBY
	);

	$rgg_input_fields['link'] = array(
		'input_type' => 'select',
		'label' => 'Link To',
		'description' => 'Choose where the gallery images should link to',
		'options' => array('post' => 'Attachment Page', 'file' => 'Media File', 'none' => 'None'),
		'default' => RGG_DEFAULT_LINK
	);

	$rgg_input_fields['effect'] = array(
		'input_type' => 'select',
		'label' => 'Effect',
		'description' => 'Choose the mouse-over effect. <a href="https://responsive-gallery-grid.bdwm.be/responsive-gallery-grid-pro/" target="_blank">Buy RGG Pro</a> for more effects.',
		'options' => array(
			'bubble' => 'Bubble',
			'none' => 'None',
		),
		'default' => RGG_DEFAULT_EFFECT
	);

	$rgg_input_fields['size'] = array(
		'input_type' => 'select',
		'label' => 'Image Size',
		'description' => 'The size of the images to load as the tiles of the grid.',
		'options' => $image_sizes,
		'default' => RGG_DEFAULT_SIZE,
	);

	$rgg_input_fields['linked_image_size'] = array(
		'input_type' => 'select',
		'label' => 'Linked image size',
		'description' => 'The size of the images where the tiles link to.',
		'options' => $image_sizes,
		'default' => RGG_DEFAULT_LINKED_IMAGE_SIZE
	);

	$rgg_input_fields['captions'] = array(
		'input_type' => 'select',
		'label' => 'Captions',
		'description' => 'Choose how you want the captions to be displayed. <a href="https://responsive-gallery-grid.bdwm.be/responsive-gallery-grid-pro/" target="_blank">Buy RGG Pro</a> for more options.',
		'options' => array(
			'title' => 'As title attribute',
			'off' => 'Don\'t show captions',
		),
		'default' => RGG_DEFAULT_CAPTIONS
	);

	$rgg_input_fields['ids'] = array(
		'input_type' => 'text',
		'label' => 'IDs',
		'description' => 'A comma seperated list of media IDs.<br>This is generated by WordPress if you create a Gallery with Add Media. If this parameter is omitted, the gallery will show all images that are attached to the current post.',
		'default' => RGG_DEFAULT_IDS
	);

	$rgg_input_fields['include'] = array(
		'input_type' => 'text',
		'label' => 'Include',
		'description' => 'A comma seperated list of media IDs of additional images to include in the gallery.',
		'default' => RGG_DEFAULT_INCLUDE
	);

	$rgg_input_fields['exclude'] = array(
		'input_type' => 'text',
		'label' => 'Exclude',
		'description' => 'A comma seperated list of media IDs of images to exclude from the gallery.',
		'default' => RGG_DEFAULT_EXCLUDE
	);

	$rgg_input_fields['id'] = array(
		'input_type' => 'text',
		'label' => 'Post ID',
		'description' => 'A valid Post ID. This will only be used if the ids parameter is omitted.<br>Fills the gallery with all images attached to the post with the provided ID.<br>Default value is the ID of the post in which the gallery is inserted.',
		'default' => RGG_DEFAULT_ID
	);

	$rgg_input_fields =  apply_filters('rgg_filter_input_fields', $rgg_input_fields);
}

function rgg_get_thumbnail_src ($post_id=0, $size="thumbnail") {
	global $rgg_options;
	if ($post_id == 0) {
		global $post;
	} else {
		$post = get_post($post_id);
	}
	
	if ( has_post_thumbnail($post->ID)) {
	   $img_info = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ));
	} else if (key_exists('default_thumbmail', $rgg_options) && $rgg_options['default_thumbmail'] != '') {
		$img_info = wp_get_attachment_image_src($rgg_options['default_thumbmail']);
	} else {
		return '';
	}
	
	return $img_info;
}

function rgg_the_post_thumbnail ($post_id=0, $size="thumbnail") {
	$info = rgg_get_thumbnail_src($post_id,$size);
	if ($info == '') {
		echo '';
	} else {
?>
	<img width="<?php echo $info[1] ?>" height="<?php echo $info[2] ?>" src="<?php echo $info[0] ?>" class="attachment-<?php $size ?> wp-post-image" alt="">
<?php
	}
}

add_action('admin_init', 'rgg_admin_init');
function rgg_admin_init(){
	if (!current_user_can('manage_options')) {
		wp_die('You do not have sufficient permissions to access this page.');
	}
	register_setting( RGG_OPTIONS, RGG_OPTIONS, 'rgg_options_sanitize' );
	add_settings_section('rgg_main', 'Main Settings', 'rgg_section_text', RGG_PLUGIN);
	add_settings_field('rgg_text_string', 'Plugin Text Input', 'rgg_setting_string', RGG_PLUGIN, 'rgg_main');
}

function rgg_section_text() {
echo '<p>Main description of this section here.</p>';
}

function rgg_setting_string() {
	
}

function rgg_options_sanitize($input) {

	// Basic sanitization for all input fields
	$input = array_map('sanitize_text_field', $input);

	// Extra sanitization for attributes to prevent XSS
	$input['class'] = sanitize_key($input['class']);
	$input['rel'] = sanitize_key($input['rel']);

	return $input;
}