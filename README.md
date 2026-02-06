MP3 Store Platform

**Overview**
Real-world digital sales system built with PHP + MySQL. Admins upload full MP3s and 30-second previews, users can listen to previews, and downloads are protected by server-side access control with time-limited tokens.

**Setup**
1. Create the database and tables:
   - Import `database.sql`.
2. Update DB credentials in `includes/db.php`.
3. Create an admin user (use a PHP shell or temporary script):

```php
<?php
$hash = password_hash('ChangeMe123!', PASSWORD_BCRYPT);
echo $hash;
```

Then insert into MySQL:

```sql
INSERT INTO admins (username, password_hash) VALUES ('admin', 'PASTE_HASH_HERE');
```

**Run**
- Point your web server to the project root (so `/public/index.php` is accessible) and ensure `songs_private` and `previews` are not directly accessible.
- If you prefer a safer setup, point the web root to `public/` and move `admin/` into `public/admin/` (then update links accordingly).

**Key Routes**
- Storefront: `/public/index.php`
- Admin login: `/admin/login.php`
- Admin upload: `/admin/upload.php`

**Notes**
- Previews are served through `public/preview.php` and should be exactly 30 seconds long.
- Full MP3s are stored in `songs_private` and only served by `public/download.php`.
