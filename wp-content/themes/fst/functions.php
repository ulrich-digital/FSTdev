<?php
/*
*  Author: Matthias Ulrich
*  URL: https://ulrich.digital
*/

setlocale(LC_TIME, "de_DE.utf8");

/* ============================================ *\
    header.php aufraeumen
\* ============================================ */
remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'index_rel_link' ); // index link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action( 'wp_head', 'rest_output_link_wp_head');
remove_action( 'wp_head', 'wp_oembed_add_discovery_links');
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
/// emojis weg
add_action('init', 'remove_emoji');
function remove_emoji(){
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'remove_tinymce_emoji');
}

function remove_tinymce_emoji($plugins){if (!is_array($plugins)){return array();}return array_diff($plugins, array('wpemoji'));}

// jquery migrate weg
add_action( 'wp_default_scripts', 'cedaro_dequeue_jquery_migrate' );
function cedaro_dequeue_jquery_migrate( $scripts ) {
	if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {$jquery_dependencies = $scripts->registered['jquery']->deps;$scripts->registered['jquery']->deps = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );}
}



/* ============================================ *\

    CSS

\* ============================================ */

function theme_styles() {
    //wp_register_style( $handle, $src, $deps, $ver, $media );

    wp_register_style('reset', get_stylesheet_directory_uri() . '/reset.css', array(), '1.0', 'all');
    wp_enqueue_style('reset');

    wp_register_style('main',  get_stylesheet_directory_uri() . "/style.css?" . date("h:i:s"), array(), '1.0', 'all');
    wp_enqueue_style('main');

	wp_register_style('font_awesome', get_stylesheet_directory_uri() . '/css/all.css', array(), '1.0', 'all');
    wp_enqueue_style('font_awesome');

	wp_register_style('adobe_fonts', 'https://use.typekit.net/rch5lgv.css', array(), '1.0', 'all');
	wp_enqueue_style('adobe_fonts');
	
	/*
	wp_register_style('slick',  get_stylesheet_directory_uri() . '/js/slick.css', array(), '1.0', 'all');
	wp_enqueue_style('slick');
	wp_register_style('slick-lightbox', get_stylesheet_directory_uri() . '/js/slick-lightbox.css', array(), '1.0', 'all');
	wp_enqueue_style('slick-lightbox');

    wp_register_style('google_fonts', 'https://fonts.googleapis.com/css?family=Abel|Roboto:300', array(), '1.0', 'all');
    wp_enqueue_style('google_fonts');
    */
}
add_action('wp_enqueue_scripts', 'theme_styles');


function add_admin_styles() {
   wp_enqueue_style('admin-styles', get_template_directory_uri().'/style-admin.css');
}

add_action('admin_enqueue_scripts', 'add_admin_styles');
/* ============================================ *\

    Include eigene Javascripte

\* ============================================ */

function fuege_javascripts_ein() {
	$url_h0 = get_stylesheet_directory_uri().'/js/jquery-ui.min.js';
	$url_h2 = get_stylesheet_directory_uri().'/js/isotope.pkgd.min.js';
	$url_h3 = get_stylesheet_directory_uri().'/js/ulrich.js?v='. time() . '';

    //wp_enqueue_script( 'eigener_Name', pfad_zum_js, abhaengigkeit (zb jquery zuerst laden), versionsnummer, bool (true=erst im footer laden) );
	wp_enqueue_script( 'handler_name_0', $url_h0, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_2', $url_h2, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_3', $url_h3, array('jquery'), null, false );
}
add_action( 'wp_enqueue_scripts', 'fuege_javascripts_ein' );

add_action( 'comment_form_before', 'enqueue_comment_reply_script' );
function enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}



/* ============================================ *\

	Theme Support

\* ============================================ */


/*Title*/
add_filter( 'document_title_separator', 'document_title_separator' );
function document_title_separator( $sep ) {
    $sep = '|';
    return $sep;
}

add_filter( 'the_title', 'mytitle' );
function mytitle( $title ) {
    if ( $title == '' ) {
        return '...';
    } else {
        return $title;
    }
}

//entfernt die automatischen <p>-Auszeichnungen
//remove_filter ('the_content', 'wpautop');

add_action( 'after_setup_theme', 'ulrich_digital_setup' );

function ulrich_digital_setup(){
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    global $content_width;
    register_nav_menus(
	   //register_nav_menu( 'primary', __( 'Primary Menu', 'theme-slug' ) );
	   array(
		   'main-menu' => __( 'Main Menu', 'ulrich_digital_blank' ),
		   'main_menu_2' => __( 'Main Menu 2', 'ulrich_digital_blank' ),
		   'footer_menu_1' => __( 'Footer Menu 1', 'ulrich_digital_blank' ),
		   'footer_menu_2' => __( 'Footer Menu 2', 'ulrich_digital_blank' )
	    )
    );
}

add_action( 'widgets_init', 'ulrichdigital_blank_widgets_init' );
function ulrichdigital_blank_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar Widget Area', 'ulrich_digital_blank' ),
		'id' => 'primary-widget-area',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}

/* ACF Options Page*/
if( function_exists('acf_add_options_page') ) {
		acf_add_options_page();
}


/* =============================================================== *\ 

 	 ACF WYSIWYG - TOOLBAR anpassen

\* =============================================================== */ 
//https://www.advancedcustomfields.com/resources/customize-the-wysiwyg-toolbars/

add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars ) {

	/*
	echo '< pre >';
		print_r($toolbars);
	echo '< /pre >';
	//die;
	*/


  $toolbars['Very Simple' ] = array();
  $toolbars['Very Simple' ][1] = array('styleselect','hr' );

  // return $toolbars - IMPORTANT!
  return $toolbars;
}





/**
 * Registers an editor stylesheet for the theme.
 */
function add_editor_styles() {
    add_editor_style( 'style-admin.css' );
}
add_action( 'admin_init', 'add_editor_styles' );


/*
 * Callback function to filter the MCE settings
 */

// Callback function to insert 'styleselect' into the $buttons array
function my_mce_buttons_2($buttons) {
    array_unshift($buttons, 'styleselect');
    return $buttons;
}

// Register our callback to the appropriate filter
add_filter('mce_buttons_2', 'my_mce_buttons_2');

function my_mce_before_init_insert_formats($init_array) {
// Define the style_formats array
    $style_formats = array(
        // Each array child is a format with it's own settings
        array(
            'title' => 'Normaler Lauftext',
            'inline' => 'span',
            'classes' => 'programm_lauftext'
        ),
		array(
			'title' => 'Titel',
			'block' => 'h3',
			'classes' => 'programm_titel'
		),
		array(
			'title' => 'GROSSBUCHSTABEN',
			'inline' => 'span',
			'classes' => 'programm_uppercase'
		),
		array(
			'title' => 'Klein + Kursiv',
			'inline' => 'span',
			'classes' => 'programm_klein_italic'
		),
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode($style_formats);
    return $init_array;
}

// Attach callback to 'tiny_mce_before_init' 
add_filter('tiny_mce_before_init', 'my_mce_before_init_insert_formats');







/*===============================================================*\

	Eigene Bildgroesse

\*===============================================================*/

add_action('after_setup_theme', 'eigene_bildgroessen', 11);
function eigene_bildgroessen() {
	add_image_size('facebook_share', 1200, 630, true);
	add_image_size('startseite', 2400, 1600, true);
	add_image_size('angebot_header_bild', 2000, 2000, false);
	add_image_size('galerie_thumb', 700, 700, true);
	}

//Bildgroessen zur Auswahl hinzufuegen
//add_filter('image_size_names_choose', 'bildgroessen_auswaehlen');
function bildgroessen_auswaehlen($sizes) {
	$custom_sizes = array('facebook_share' => 'Facebook-Vorschaubild');
	return array_merge($sizes, $custom_sizes);
	}




/*===============================================================*\

    SVG erlauben

\*===============================================================*/

function add_svg_to_upload_mimes($upload_mimes)
	{
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
	}
add_filter('upload_mimes', 'add_svg_to_upload_mimes');




/*===============================================================*\

    Custom Admin-Logo

\*===============================================================*/

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/BG_logo_rgb.svg);
            padding-bottom: 60px;
            width:320px;
            background-repeat: no-repeat;
 background-size: 250px auto;

        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );




/*===============================================================*\

    Backend anpassen

\*===============================================================*/

/* Menueelemente aus dem WordPress-Dashboard entfernen */
function remove_menus () {
	global $menu;
	$restricted = array(__('Beiträge'), __('Kommentare'));
 	//$restricted = array(__('Kommentare'));
	end ($menu);
	while (prev($menu)){
     $value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
		}
 }
add_action('admin_menu', 'remove_menus');

/* Menueelemente aus dem Menue-Bar oben entfernen */
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('new-content');
	/*
	my-account – link to your account (avatars disabled)
	my-account-with-avatar – link to your account (avatars enabled)
	my-blogs – the "My Sites" menu if the user has more than one site
	get-shortlink – provides a Shortlink to that page
	edit – link to the Edit/Write-Post page
	new-content – link to the "Add New" dropdown list
	comments – link to the "Comments" dropdown
	appearance – link to the "Appearance" dropdown
	updates – the "Updates" dropdown
	*/
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

/*Benutzerdefinierte Reihenfolge des Backend-Menu*/
function wpse_custom_menu_order( $menu_ord ) {
 if ( !$menu_ord ) return true;
 return array(
     'index.php', // Dashboard
     'link-manager.php', // Links
     'edit.php?post_type=page', // Pages
     'users.php', // Users
     'upload.php', // Media
     'separator1', // First separator
     'themes.php', // Appearance
     'plugins.php', // Plugins
     'tools.php', // Tools
     'options-general.php', // Settings
     'separator2', // Second separator
     'separator-last', // Last separator
 );
}
//add_filter( 'custom_menu_order', '__return_true' );
//add_filter( 'menu_order', 'wpse_custom_menu_order', 10, 1 );

function backend_entwickelt_mit_herz( $text ) {
	return ('<span style="color:black;">Entwickelt mit </span><span style="color: red;font-size:20px;vertical-align:-3px">&hearts;</span><span style="color:black;"</span><span> von <a href="https://ulrich.digital" target="_blank">ulrich.digital</a></span>' );
}
add_filter( 'admin_footer_text', 'backend_entwickelt_mit_herz' );






/* =============================================================== *\ 

 	 ZIP-Archiv 

\* =============================================================== */ 
  
/*
ACF beim save_post einhaken
https://www.advancedcustomfields.com/resources/acf-save_post/
*/


/*
gets the current post-type in the WordPress Admin
*/
function get_current_post_type() {
	global $post, $typenow, $current_screen;
	
	//we have a post so we can just get the post type from that
	if ( $post && $post->post_type ) {
		return $post->post_type;
	}
	
	//check the global $typenow - set in admin.php
	elseif ( $typenow ) { 
		return $typenow; 
	}
	
	//check the global $current_screen object - set in sceen.php
	elseif ( $current_screen && $current_screen->post_type ) {
		return $current_screen->post_type;
	}
	
	//check the post_type querystring
	elseif ( isset( $_REQUEST['post_type'] ) ) {
		return sanitize_key( $_REQUEST['post_type'] );
	}
	
	//lastly check if post ID is in query string
	elseif ( isset( $_REQUEST['post'] ) ) {
		return get_post_type( $_REQUEST['post'] );
	}
		//we do not know the post type!
	return null;
}

get_current_post_type();


/*
gets the current post-id in the WordPress Admin
*/
function filter_query( $query ) {
    if(in_the_loop()) :
		$post_id = get_the_ID();
    else: 
		$post_id = get_queried_object_id();
	endif;
    if($post_id) {
        if(empty($query['post__not_in'])) $query['post__not_in'] = array(); // that way if someone else already has stuff in $query['post__not_in'], we won't override it but append to it...
        $query['post__not_in'][] = $post_id;
    }
    return $query;
}
add_filter('wpc_query', 'filter_query', 1 );



/*
ACF HOOK > priorität 20 = nach dem Speichern
*/

//my_acf_save_post(216); //> beim Debugging die funktion ohne speicher button aufrufen

add_action('acf/save_post', 'my_acf_save_post', 20);

function my_acf_save_post( $post_id ) {


	if (get_current_post_type() == "presse"){
		
		
		//$my_folder = "../downloads/" . $sprach_kuerzel . "/";
		$my_folder = "../downloads/";
		
		if (!is_dir($my_folder)) {
			mkdir($my_folder, 0747);
		}
		$filename ="../downloads/fathom_string_trio.zip";
		//$sprach_kuerzel = pll_get_post_language($post_id);
		//$filename ="../downloads/" . $sprach_kuerzel . "/peter_werlen.zip";
		$serverpfad = getcwd();
		$serverpfad_gekuerzt = str_replace ("/wp-admin", "", $serverpfad);
		$server_filename = $serverpfad_gekuerzt . "/downloads/" . $sprach_kuerzel . "/peter_werlen.zip";
		//altes ZIP-Archiv löschen, wenn vorhanden 
		if (file_exists ( $filename )){ 
			unlink($server_filename); 
		} 

		// neues ZIP-Archiv erstellen
		$zip = new ZipArchive();
		
		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) { 
				exit("cannot open <$filename>\n"); 
			} 

			// Dateien holen und dem ZIP-Archive hinzufügen
			if( have_rows('dateien_container', $post_id) ):		
				while ( have_rows('dateien_container', $post_id) ) : the_row();
				
			        $dateien = get_sub_field('datei', $post_id);
			        $ganzerPfad = $dateien['url'];
			        $dateiName = $dateien['filename'];
			        //teilurl: alles was vor dem wp-content ist abschneiden
			        $rest_pre = strpos($ganzerPfad, "wp-content", $offset = 0); 
			        $rest = substr($ganzerPfad, $rest_pre);
					$rest = "../" . $rest;
					$zip->addFile($rest, $dateiName);
									
				endwhile;
			endif;	

		$zip->close();
	
	} else { //get_current_post_type
   		return;
	}
}






/*===============================================================*\

	Contact Form 7

\*===============================================================*/


/*===============================================================*\

	Custom Post Types

\*===============================================================*/

add_action('init','ab_register_post_type_agenda');
function ab_register_post_type_agenda(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Agenda',
    'menu_name' => 'Projekte',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Agenda',
    'public' => true,
	'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-megaphone',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'agenda',
        'with_front' => true
   		),
	);
register_post_type('Agenda', $args);
}


add_action('init','ab_register_post_type_projekt');
function ab_register_post_type_projekt(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Projekt',
    'menu_name' => 'Projekte neu',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Projekte',
    'public' => true,
	'show_in_nav_menus' => true,
    'show_in_menu' => true,
	'show_in_rest' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-megaphone',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'projekt',
        'with_front' => true
   		),
	);
register_post_type('projekt', $args);
}


add_action('init','ab_register_post_type_konzert_archiv');
function ab_register_post_type_konzert_archiv(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Agenda-Archiv',
    'menu_name' => 'Agenda-Archiv',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$konzert_archiv_args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Konzert-Archiv',
    'public' => true,
    'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-megaphone',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'konzert_archiv',
        'with_front' => true
   		),
	);
register_post_type('Konzert-Archiv', $konzert_archiv_args);
}


add_action('init','ab_register_post_type_portrait');
function ab_register_post_type_portrait(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Portrait',
    'singular_name' => 'Portrait',
    'menu_name' => 'Portrait',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$portrait_args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Portrait',
    'public' => true,
    'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-groups',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'portrait',
        'with_front' => true
   		),
	);
register_post_type('portrait', $portrait_args);
}

add_action('init','ab_register_post_type_repertoire');
function ab_register_post_type_repertoire(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Repertoire',
    'menu_name' => 'Repertoire',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$repertoire_args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Repertoire',
    'public' => true,
    'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-playlist-audio',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'repertoire',
        'with_front' => true
   		),
	);
register_post_type('repertoire', $repertoire_args);
}

add_action('init','ab_register_post_type_presse');
function ab_register_post_type_presse(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Presse',
    'menu_name' => 'Presse',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb'
	);
$presse_args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Presse',
    'public' => true,
    'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => false,
    'query_var' => true,
	'menu_icon' => 'dashicons-media-document',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'presse',
        'with_front' => true
   		),
	);
register_post_type('presse', $presse_args);
}
