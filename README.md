# WP Audio Upload Post

A simple WordPress plugin that allows you to upload or link to MP3 files and automatically create posts with an embedded audio player, metadata (artist, album), and optional featured image.

---

## 🎧 Features

- Upload `.mp3` files directly or provide an MP3 URL.
- Auto-create WordPress posts with:
  - Title
  - Artist and album info
  - Embedded HTML5 audio player
  - Featured image (optional)
- Bulk MP3 upload supported
- Assign categories and tags to each post
- Default image fallback if none is uploaded
- Clean Bootstrap 5 styled admin interface

---

## 📦 Installation

1. Download the plugin or clone the repo into your `/wp-content/plugins/` directory:
   ```
   git clone https://github.com/durgeshdva/wp-audio-upload-post.git
   ```
2. Go to your WordPress dashboard.
3. Navigate to **Plugins > Installed Plugins**.
4. Activate **WP Audio Upload Post**.

---

## ⚙️ Usage

1. After activation, a new menu item **Audio Uploader** will appear in the admin sidebar.
2. Click **Audio Uploader** to access the upload form.
3. Fill in the details:
   - Title (required)
   - Artist, Album (optional)
   - Upload an MP3 or paste an MP3 URL
   - Optional featured image
   - Tags and category
4. Click **Create Post** to generate a new WordPress post with your audio.
5. To upload multiple MP3 files at once, use the **bulk uploader** section.

---

## 🔧 Settings

- Navigate to **Audio Uploader > Settings**
- Set a **Default Featured Image URL** – used when no image is uploaded during post creation.

---

## 📂 Folder Structure

```
wp-audio-upload-post/
├── wp-audio-upload-post.php  # Main plugin file (this one)
├── upload_action.php         # Handles file saving and post creation
```

> Make sure you also include `upload_action.php` in your plugin directory for full functionality.

---

## 🚀 To-Do / Ideas

- Shortcode support for front-end embedding
- Playlist generation for uploaded tracks
- Custom post type for better separation from blog posts

---

## 🧑‍💻 Author

**Durgesh Vishwakarma**  
GitHub: [@durgeshdva](https://github.com/durgeshdva)  
Website: [instargam.com](https://instagramc.om/durgesh.3121)
