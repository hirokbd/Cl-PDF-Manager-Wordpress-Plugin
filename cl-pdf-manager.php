<?php
/*
  Plugin Name: CL PDF Manager
  Plugin URI: https://creepeslab.com/cl-pdf-manager
  description: Help you manage your Iamge & Tite link to PDF documents. Support short code to show special PDF documents.
  Version: 1.0
  Author: K.H. Hirok
  Author URI: http://creepeslab.com
  Text Domain: cl-pdf-manager
  Domain Path: /languages
  License: GPL2
 */
/* This calls CL PDF Manager() function when wordpress initializes. */
/* Note that the CL PDF Manager doesnt have brackets. 
 */

add_action('init', 'cl_pdf_manager');

function cl_pdf_manager() {
    $labels = array(
        'name' => 'PDF Manager',
        'singular_name' => 'PDF Managers',
        'menu_name' => 'PDF Managers',
        'name_admin_bar' => 'PDF Manager',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New PDF Manager',
        'new_item' => 'New PDF Manager',
        'edit_item' => 'Edit PDF Manager',
        'view_item' => 'View PDF Manager',
        'all_items' => 'All PDF Managers',
        'search_items' => 'Search PDF Managers',
        'parent_item_colon' => 'Parent PDF Managers:',
        'not_found' => 'No PDF Manager found.',
        'not_found_in_trash' => 'No PDF Manager found in Trash.'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'rewrite' => array('slug' => 'cl-pdf-manager'),
        'has_archive' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-media-text',
        'supports' => array('title', 'editor', 'author', 'thumbnail')
    );
    register_post_type('cl_pdf_manager', $args);
}

function add_custom_meta_boxes() {
    add_meta_box('wp_custom_attachment', 'Upload PDF', 'wp_custom_attachment', 'cl_pdf_manager', 'normal', 'high');
}

add_action('add_meta_boxes', 'add_custom_meta_boxes');

function wp_custom_attachment() {
    $pdffile = get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
    wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
    ?>
    <p class="description">
        Upload your PDF here.
    </p>
    <input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25"><br>
    <?php if ($pdffile) { ?>
        <a href="<?php echo $pdffile['url'] ?>" target="_NEW">Read PDF</a>
        <?php
    }
}

add_action('save_post', 'save_custom_meta_data');

function save_custom_meta_data($id) {
    if (!empty($_FILES['wp_custom_attachment']['name'])) {
        $supported_types = array('application/pdf');
        $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
        $uploaded_type = $arr_file_type['type'];

//        if(in_array($uploaded_type, $supported_types)) {
        $upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));
        if (isset($upload['error']) && $upload['error'] != 0) {
            wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
        } else {
            update_post_meta($id, 'wp_custom_attachment', $upload);
        }
    }
}

function update_edit_form() {
    echo ' enctype="multipart/form-data"';
}

add_action('post_edit_form_tag', 'update_edit_form');

function cl_pdf_manager_fun() {
    $query = new WP_Query(
            array('post_type' => 'cl_pdf_manager',
        'posts_per_page' => 12, 'post_status' => 'publish',
        'paged' => $paged, 'order' => 'ASC'
    ));
//    echo '<pre>';print_r($query); echo '</pre>';
    if ($query->have_posts()) :
        ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?> 
            <?php
            $pdffile = get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
            //print_r($pdffile);
            ?>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
            <style>
                @media all and (min-width:780px) {
                    .pdf-man {
                        width: 33.33%;
                        float: left!important;
                    }
                }
                @media all and (max-width:769px) {
                    .pdf-man {
                        width: 50%;
                        float: left!important;
                    }
                }
                @media all and (max-width:450px) {
                    .pdf-man {
                        width: 100%;
                    }
                }
            </style>

            <div class="col-sm-4 pdf-man grid-item">
                <div style="text-align: center;" class="pdf-img">
                    <a href="<?php
                    if ($pdffile) {
                        echo $pdffile['url'];
                    } else {
                        echo '#';
                    }
                    ?>" target='_NEW'> <?php
                           if (has_post_thumbnail()) {
                               the_post_thumbnail('full');
                           }
                           ?>
                    </a>
                </div>
                <h3 class="pdf-title" style="text-align: center;"> <a href="<?php
                    if ($pdffile) {
                        echo $pdffile['url'];
                    } else {
                        echo '#';
                    }
                    ?>"  target='_NEW'> <?php the_title(); ?></a></h3>
            </div>



            <?php
        endwhile;
        wp_reset_postdata();
        ?>
        <!-- show pagination here -->
    <?php else : ?>
        <!-- show 404 error here -->
    <?php endif; ?>


    <?php
}

add_shortcode('cl_pdf_manager_show', 'cl_pdf_manager_fun');

function cl_pdf_manager_all_fun() {
    $query = new WP_Query(
            array('post_type' => 'cl_pdf_manager',
        'posts_per_page' => 12, 'post_status' => 'publish',
        'paged' => $paged, 'order' => 'DESC'
    ));
//    echo '<pre>';print_r($query); echo '</pre>';
    if ($query->have_posts()) :
        ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?> 
            <?php
            $pdffile = get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
            //print_r($pdffile);
            ?>
            <div class="col-sm-4 pdf-man grid-item">
                <div style="text-align: center;" class="pdf-img">
                    <a href="<?php
                    if ($pdffile) {
                        echo $pdffile['url'];
                    } else {
                        echo '#';
                    }
                    ?>" target='_NEW'> <?php
                           if (has_post_thumbnail()) {
                               the_post_thumbnail('full');
                           }
                           ?>
                    </a>
                </div>
                <h3 class="pdf-title" style="text-align: center;"> <a href="<?php
                    if ($pdffile) {
                        echo $pdffile['url'];
                    } else {
                        echo '#';
                    }
                    ?>"  target='_NEW'> <?php the_title(); ?></a></h3>

                <?php the_author_posts_link(); ?> on <?php the_time('F jS, Y'); ?>  <?php the_category(', '); ?>
                <p  style="text-align: justify;"><? //php the_excerpt();       ?>
                    <?php
                    echo wp_trim_words(get_the_content(), 30);
                    ?>
                    <a href="<?php the_permalink(); ?>">Read More</a>
                </p>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
        <!-- show pagination here -->
    <?php else : ?>
        <!-- show 404 error here -->
    <?php endif; ?>


    <?php
}

add_shortcode('cl_pdf_manager_showall', 'cl_pdf_manager_all_fun');
