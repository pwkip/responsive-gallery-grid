<?php

global $rgg_instance_fallback;
$rgg_instance_fallback = 10135; //give it this value for debugging purposes

function rgg_gallery_shortcode($output, $attr, $instance = false) {

    global $rgg_settings, $rgg_options, $rgg_instance_fallback;

    if ($instance === false) {
        $instance = $rgg_instance_fallback;
        $rgg_instance_fallback++;
    }

    $post = get_post();
    $post_id = $post->ID ?? '';

    // image_size is deprecated, but if it is set and size is empty => size = image_size.
    if (!empty($attr['image_size']) && empty($attr['size'])) {
        $attr['size'] = $attr['image_size'];
    }

    // create settings based on the attributes set in the shortcode.
    $settings_arr = shortcode_atts(array(
        'gallery_instance' => $instance,
        'type' => $rgg_options['type'],
        'class' => $rgg_options['class'],
        'rel' => $rgg_options['rel'],
        'ids' => $rgg_options['ids'],
        'fid' => null, // RML compatibility (https://bit.ly/3d9QSUb)
        'margin' => intval($rgg_options['margin']),
        'scale' => doubleval($rgg_options['scale']),
        'maxrowheight' => intval($rgg_options['maxrowheight']),
        'rowspan' => intval($rgg_options['rowspan']),
        'intime' => intval($rgg_options['intime']),
        'outtime' => intval($rgg_options['outtime']),
        'captions' => $rgg_options['captions'],
        'captions_effect' => $rgg_options['captions_effect'],
        'captions_intime' => intval($rgg_options['captions_intime']),
        'captions_outtime' => intval($rgg_options['captions_outtime']),
        'linked_image_size' => $rgg_options['linked_image_size'],
        'lightbox' => $rgg_options['lightbox'],

        //pro params:
        'lastrowbehavior' => $rgg_options['lastrowbehavior'],
        'effect' => $rgg_options['effect'],

        // default params  that can be inherited from gallery_shortcode
        'order'      => 'ASC',
        'orderby'    => $rgg_options['orderby'],
        'link'       => $rgg_options['link'],
        'id'         => $rgg_options['id'] == '' ? $post_id : $rgg_options['id'],
        'size'       => $rgg_options['size'], // default changed from thumbnail to medium, because that makes more sense
        'include'    => $rgg_options['include'],
        'exclude'    => $rgg_options['exclude'],
    ), $attr);

    // swipebox is no longer supported
    if ($settings_arr['lightbox'] === 'swipebox') {
        // Fall back to simplelightbox
        $settings_arr['lightbox'] = 'simplelightbox';
    }

    // Sanitize the settings
    $settings_arr = array_map( 'htmlentities' , $settings_arr);

    $type = $settings_arr['type'];
    $class = $settings_arr['class'];
    $rel = $settings_arr['rel'];
    $ids = $settings_arr['ids'];
    $margin = $settings_arr['margin'];
    $scale = $settings_arr['scale'];
    $maxrowheight = $settings_arr['maxrowheight'];
    $rowspan = $settings_arr['rowspan'];
    $intime = $settings_arr['intime'];
    $outtime = $settings_arr['outtime'];
    $captions = $settings_arr['captions'];
    $captions_effect = $settings_arr['captions_effect'];
    $captions_intime = $settings_arr['captions_intime'];
    $captions_outtime = $settings_arr['captions_outtime'];
    $linked_image_size = $settings_arr['linked_image_size'];
    $lightbox = $settings_arr['lightbox'];

    //pro
    $lastrowbehavior = $settings_arr['lastrowbehavior'];
    $effect = $settings_arr['effect'];

    // default params  that can be inherited from gallery_shortcode
    $order = $settings_arr['order'];
    $orderby = $settings_arr['orderby'];
    $link = $settings_arr['link'];
    $id = $settings_arr['id'];
    $size = $settings_arr['size'];
    $include = $settings_arr['include'];
    $exclude = $settings_arr['exclude'];

    if ($type == 'native') {
        // returning nothing will make gallery_shortcode take over
        return '';
    }

    /* code below is based on default gallery_shortcode (wp-includes/media.php) */
    /* BEGIN ------------------- */

    if (!empty($ids)) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($orderby)) $orderby = 'post__in';
        $include = $ids;
    }

    // Allow plugins/themes to override the default gallery template. // NOT NEEDED ofcourse
    // $output = apply_filters('post_gallery', '', $attr);
    // if ( $output != '' ) return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    $orderby = sanitize_sql_orderby($orderby);
    if (!$orderby) unset($orderby);

    $id = intval($id);
    if ('RAND' == $order)
        $orderby = 'none';

    // RML compatibility (https://bit.ly/3d9QSUb)
    if (function_exists('wp_rml_get_object_by_id') && $settings_arr['fid'] !== null && ($rml_folder = wp_rml_get_object_by_id($settings_arr['fid'])) !== null) {
        // We need WP_Query because get_posts suppress filters
        $_attachments = (new WP_Query(array(
            'include' => $include,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'rml_folder' => $rml_folder->getId(),
            'order' => $order,
            'orderby' => $orderby
        )))->get_posts();
        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($include)) {
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($exclude)) {
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments))
        return '';

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment)
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    /* ------------------------- END */



    $rgg_settings[$instance] = $settings_arr;

    ob_start();

    echo '<div class="rgg-container" data-rgg-id="' . $instance . '">';

    if ($lightbox == 'image-above') {
?>
        <div class="image-above-container">
            <?php
            foreach ($attachments as $mid => $attachment) {
                $src = wp_get_attachment_image_src($mid, $linked_image_size)[0];
            ?>
                <div class="slide">
                    <img src="<?php echo $src ?>">
                </div>
            <?php } ?>
        </div>
    <?php
    }
    ?>
    <div class="rgg-imagegrid captions-<?php echo $captions ?> captions-effect-<?php echo $captions_effect ?> <?php echo $class ?>" data-rgg-id="<?php echo $instance ?>">
        <?php
        foreach ($attachments as $mid => $attachment) {

            //$native_gallery_link_html = wp_get_attachment_link($mid, $size, true); //some lightbox plugins will filter the native gallery links. So we will copy some of the attributes to our own links.

            $href = '';
            if ($link == 'file') {
                $info = wp_get_attachment_image_src($mid, $linked_image_size);
                $href = $info[0];
            } else if ($link == 'post') {
                $href = get_the_permalink($mid);
            }
            $title = get_post_field('post_excerpt', $mid);
            $alt = get_post_meta($mid, '_wp_attachment_image_alt', true);
            $title_esc = htmlentities($title, ENT_COMPAT, 'UTF-8');
            $a_title = "title=\"$title_esc\"";

            $img_info = wp_get_attachment_image_src($mid, $size);

            $rgg_lightbox = 'rgg-' . $lightbox;

            $href_attr = $href ? 'href="'.$href.'"' : '';

            ob_start();
        ?>
            <a <?php echo $href_attr ?> data-rel="<?php echo $rel ?>" rel="<?php echo $rel ?>" <?php echo $a_title ?> class="<?php echo $rgg_lightbox ?> size-<?php echo $size ?> rgg-img" data-src="<?php echo $img_info[0] ?>" data-ratio="<?php echo $img_info[1] / $img_info[2] ?>" data-height="<?php echo $img_info[2] ?>" data-width="<?php echo $img_info[1] ?>" aria-label="<?php echo $alt ?>">
                <?php
                if (RGG_IS_PRO && ((substr($captions, 0, 7) == 'overlay' || $captions == 'custom') && !empty($title_esc))) {
                    echo '<span class="rgg-caption-container"><span class="rgg-caption"><span class="rgg-inner-caption">' . $title . '</span></span></span>';
                }
                ?>
            </a>
        <?php

            $gallery_attachment_link_html = ob_get_clean();

            echo apply_filters('wp_get_attachment_link', $gallery_attachment_link_html, $mid, $size, $href, false, false); // allow other plugins to add additional attributes

        }
        ?>
    </div>
<?php

    echo '</div>'; //close .rgg-container

    return do_shortcode(ob_get_clean());
}

add_filter('post_gallery', 'rgg_gallery_shortcode', 9, 3);
