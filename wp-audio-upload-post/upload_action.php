<?php
require_once('../../../wp-load.php'); // Adjust if needed
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

function textTitle($name){
    $title = str_replace('-',' ',$name);
    $title = str_replace('_',' ',$title);
    $title = str_replace('--',' ',$title);
    $title = str_replace('  ',' ',$title);
    return $title;
}
function fileslug($name){
    $name = str_replace(' ','-',$name);
    $str = preg_replace('~[^a-zA-Z0-9]+~', '', $name);
    return $str;
}

// Sanitize inputs
$tags   = sanitize_text_field($_POST['post_tags'] ?? '');
$cat_id = intval($_POST['post_category'] ?? 0);
$title  = sanitize_text_field($_POST['title']);
$artist = sanitize_text_field($_POST['artist']);
$album  = sanitize_text_field($_POST['album']);
$mp3    = '';

// Create song_upload dir if not exists
$upload_dir = wp_upload_dir();
$song_dir   = $upload_dir['basedir'] . '/song_upload/';
$song_url   = $upload_dir['baseurl'] . '/song_upload/';
wp_mkdir_p($song_dir);

// Handle MP3 Upload
if (!empty($_FILES['mp3_file']['name'])) {
    $file = $_FILES['mp3_file'];
    $filename = sanitize_file_name($file['name']);
    $target = $song_dir . textTitle($filename);

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $mp3 = $song_url . textTitle($filename);
    } else {
        wp_die('MP3 upload failed.');
    }
} elseif (!empty($_POST['mp3_link'])) {
    $remote_url = esc_url_raw($_POST['mp3_link']);
    $filename = basename(parse_url($remote_url, PHP_URL_PATH));
    $local_filename = textTitle(sanitize_file_name($filename));
    $target = $song_dir . $local_filename;

    // Download and save the file
    $mp3_data = wp_remote_get($remote_url);
    if (is_wp_error($mp3_data)) {
        wp_die('MP3 download failed.');
    }

    $body = wp_remote_retrieve_body($mp3_data);
    if (!$body) {
        wp_die('MP3 file is empty or not accessible.');
    }

    file_put_contents($target, $body);
    $mp3 = $song_url . $local_filename;
} else {
    wp_die('MP3 required.');
}

// Handle image upload or use default
$image_id = '';
if (!empty($_FILES['featured_image']['name'])) {
    $img = media_handle_upload('featured_image', 0);
    if (!is_wp_error($img)) {
        $image_id = $img;
    }
} else {
    $default = get_option('wp_audio_default_image');
    if ($default) {
        $image_id = attachment_url_to_postid($default);
    }
}

// Auto-detect local MP3 file size (in MB)
$filesize = 'Unknown';

if (!empty($mp3)) {
    $upload_dir = wp_upload_dir();
    $local_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $mp3);

    if (file_exists($local_path)) {
        $bytes = filesize($local_path);
        $filesize = '' . round($bytes / (1024 * 1024), 2) . 'MB';
    }
}

if($artist){
  $artist = "   <tr><td>Artist</td><td><a href='/?s={$artist}'>" . esc_html($artist) . "</a></td></tr>";
}
if($album){
   $album = "<tr><td>Album</td><td><a href='/?s={$album}'>" . esc_html($album) . "</a></td></tr>";
}
// Build post content
$content = "<p>Download $title</p>
<table class='table'>
  <tr><td>File Size</td><td>{$filesize}</td></tr>
  <tr><td>File</td><td>[post_views_dv]</td></tr>
 {$artist}
  {$album}
</table>
<center>
  <audio class='audio' controls='' controlslist='nodownload' preload='none'>
    <source rel='nofollow' src='{$mp3}' type='audio/mpeg'>
  </audio>
</center>
<p><strong>Download:</strong> <a href='{$mp3}'>320kbps MP3</a></p>
<div class='tag'>
  <strong>Description: </strong>$title 320kbps, $title Free Download, $title 192kbps, $title High Quality Remix Dj Mp3 Song, $title 128kbps, Dj Mp3 Song Download, $title
</div>";

// Create Post
$post_id = wp_insert_post([
    'post_title'   => wp_strip_all_tags($title),
    'post_content' => $content,
    'post_status'  => 'publish',
    'post_type'    => 'post',
    'post_category' => $cat_id ? [$cat_id] : [],
    'tags_input'    => $tags,
    'post_slug'     => fileslug($title),
]);


if ($image_id && !is_wp_error($image_id)) {
    set_post_thumbnail($post_id, $image_id);
}
echo 'success';
