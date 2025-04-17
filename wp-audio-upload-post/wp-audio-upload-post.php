<?php
/*
Plugin Name: WP Audio Upload Post
Description: Upload or link MP3 files and auto-create posts with audio player, artist/album info, and optional featured image.
Version: 1.0
Author: Your Name
*/

// Register admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'Audio Uploader',
        'Audio Uploader',
        'manage_options',
        'audio-uploader',
        'wp_audio_upload_form'
    );
    add_submenu_page(
        'audio-uploader',
        'Audio Upload Settings',
        'Settings',
        'manage_options',
        'audio-uploader-settings',
        'wp_audio_upload_settings'
    );
});
add_action('wp_ajax_upload_audio_post', 'wp_audio_ajax_upload_handler');
function wp_audio_ajax_upload_handler() {
    require_once plugin_dir_path(__FILE__) . 'upload_action.php';
    wp_die();
}

// Load Bootstrap CSS and JS
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], null, true);
});



// Audio Upload Form
function wp_audio_upload_form() {
    ?>
    <div class="wrap container mt-4">
        <h2 class="mb-4">Upload Audio Post</h2>
        <form method="post" id="audio-upload-form" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Artist</label>
                <input type="text" name="artist" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Album</label>
                <input type="text" name="album" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">MP3 File</label>
                <input type="file" name="mp3_file" class="form-control" accept=".mp3">
            </div>
            <div class="col-md-6">
                <label class="form-label">Or MP3 Link</label>
                <input type="url" name="mp3_link" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control" accept="image/*">
            </div>
            
            <div class="col-md-6">
    <label class="form-label">Post Tags (comma-separated)</label>
    <input type="text" name="post_tags" class="form-control">
</div>

<div class="col-md-6">
    <label class="form-label">Select Category</label>
    <select name="post_category" class="form-select">
        <option value="">— None —</option>
        <?php
        $categories = get_categories(['hide_empty' => false]);
        foreach ($categories as $cat) {
            echo "<option value='{$cat->term_id}'>{$cat->name}</option>";
        }
        ?>
    </select>
</div>


            <div class="col-12">
                <button type="submit"  id="upload-btn" class="btn btn-primary">Create Post</button>
            </div>
        </form>
        <div id="upload-result" class="mt-3"></div>
    </div>
    
<script>
document.getElementById('audio-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const button = document.getElementById('upload-btn');
    const result = document.getElementById('upload-result');
    const formData = new FormData(form);
    formData.append('action', 'upload_audio_post'); // Required for WordPress AJAX
    
    button.disabled = true;
    button.textContent = 'Uploading...';

    fetch('<?php echo plugin_dir_url(__FILE__) . 'upload_action.php'; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        result.innerHTML = '<div class="alert alert-success">Post created successfully!</div>';
        form.reset();
    })
    .catch(error => {
        result.innerHTML = '<div class="alert alert-danger">Error: ' + error + '</div>';
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = 'Create Post';
    });
});
</script>


 <div class="wrap container mt-4">
        <h2 class="mb-4">Upload Audio Post</h2>
        <form id="audio-upload-form-bluk" enctype="multipart/form-data">
    <input type="file" class="form-control" name="mp3_files[]" id="mp3_files_bluk" multiple accept=".mp3" required>
    <button class="btn btn-primary" type="submit" id="upload-btn-bluk">Create Post</button>
</form>
<div id="upload-result-bluk"></div>


<script>
document.getElementById('audio-upload-form-bluk').addEventListener('submit', function(e) {
    e.preventDefault();

    const input = document.getElementById('mp3_files_bluk');
    const files = input.files;
    const button = document.getElementById('upload-btn-bluk');
    const result = document.getElementById('upload-result-bluk');
    
    if (!files.length) {
        result.innerHTML = '<div class="alert alert-warning">Please select MP3 files to upload.</div>';
        return;
    }

    button.disabled = true;
    button.textContent = 'Uploading...';
    result.innerHTML = '';

    const uploadURL = '<?php echo plugin_dir_url(__FILE__) . 'upload_action.php'; ?>';

    // Upload each file individually
    const uploadNext = async (index = 0) => {
        if (index >= files.length) {
            button.disabled = false;
            button.textContent = 'Create Post';
            result.innerHTML += '<div class="alert alert-info">All uploads complete.</div>';
            return;
        }

        const file = files[index];
        if (!file.name.toLowerCase().endsWith('.mp3')) {
            result.innerHTML += `<div class="alert alert-warning">${file.name} is not an MP3 file. Skipped.</div>`;
            return uploadNext(index + 1);
        }

        const formData = new FormData();
        formData.append('mp3_file', file);
        formData.append('title', file.name.replace(/\.[^/.]+$/, '').replace(/[_-]/g, ' ')); // Title from base name
        formData.append('action', 'upload_audio_post'); // For WordPress AJAX if needed

        try {
            const response = await fetch(uploadURL, {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            result.innerHTML += `<div class="alert alert-success">${file.name} uploaded successfully!</div>`;
        } catch (err) {
            result.innerHTML += `<div class="alert alert-danger">Error uploading ${file.name}: ${err}</div>`;
        }

        uploadNext(index + 1);
    };

    uploadNext();
});
</script>

        </div>

    <?php
}

// Settings page
function wp_audio_upload_settings() {
    if (isset($_POST['default_image_url'])) {
        update_option('wp_audio_default_image', esc_url_raw($_POST['default_image_url']));
        echo '<div class="alert alert-success mt-4">Default image updated.</div>';
    }

    $default_image = get_option('wp_audio_default_image', '');

    ?>
    <div class="wrap container mt-4">
        <h2 class="mb-4">Audio Upload Settings</h2>
        <form method="post" class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Default Featured Image URL</label>
                <input type="url" name="default_image_url" class="form-control" value="<?php echo esc_attr($default_image); ?>">
                <small class="text-muted">Used if no image is uploaded.</small>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Save Settings</button>
            </div>
        </form>
    </div>
    <?php
}
