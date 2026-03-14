# Simple Laravel Deployment Guide for Shared Hosting (cPanel)

A straightforward, no-bullshit guide to get Laravel running on shared hosting.

## Prerequisites

- Laravel project ready to deploy
- cPanel access with File Manager
- SSH access (recommended) or cPanel Terminal
- Database credentials from your host

---

## Step 1: Upload the Folder in ZIP

1. **On your local machine**, compress your entire Laravel project into a `.zip` file
   - Right-click your Laravel folder → Compress/Zip
   - Name it something like `laravel-app.zip`

2. **In cPanel File Manager**:
   - Navigate to your **home directory** (~/)
   - Upload the `laravel-app.zip` file

---

## Step 2: Unzip

1. **In cPanel File Manager**:
   - Right-click `laravel-app.zip`
   - Click **Extract**
   - It will create a folder (e.g., `laravel-app/`)

2. **Delete the .zip file** (optional, saves space)

---

## Step 3: Create New Folder on Root Called "maincode"

1. **In cPanel File Manager**, in your home directory:
   - Click **New Folder**
   - Name it: `maincode`

2. You now have:
   ```
   ~/
   ├── maincode/          (empty, we'll fill this)
   ├── public_html/       (existing)
   └── laravel-app/       (just extracted)
   ```

---

## Step 4: Copy Everything BUT Public Into "maincode"

1. **Open the extracted Laravel folder** (e.g., `laravel-app/`)

2. **Select all folders and files EXCEPT `public/`**:
   - app/
   - bootstrap/
   - config/
   - database/
   - routes/
   - storage/
   - vendor/
   - .env.example
   - artisan
   - composer.json
   - composer.lock
   - etc.
   - ✗ Do NOT copy `public/`

3. **Copy these** to `~/maincode/`

4. **Result**:
   ```
   ~/maincode/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── storage/
   ├── .env.example
   ├── artisan
   └── ...
   ```

---

## Step 5: Copy Everything Inside Public Into public_html

1. **Open** `laravel-app/public/`

2. **Select ALL files** inside:
   - .htaccess
   - index.php
   - css/
   - js/
   - etc.

3. **Copy to** `~/public_html/`

4. **Make sure `.htaccess` is copied** (it's hidden, enable "Show Hidden Files" in File Manager)

5. **Result**:
   ```
   ~/public_html/
   ├── .htaccess
   ├── index.php
   ├── css/
   ├── js/
   └── ...
   ```

---

## Step 6: Modify .env Based on cPanel Configuration

1. **In cPanel**, get your database info:
   - Go to **MySQL Databases**
   - Note your:
     - Database name
     - Database username
     - Database password
     - Host (usually `localhost`)

2. **In File Manager**, navigate to `~/maincode/`

3. **Rename** `.env.example` to `.env`

4. **Edit `.env`** and update:
   ```env
   APP_NAME=Laravel
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=                    # Leave blank, we'll generate this later
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

5. **Save**

---

## Step 7: Test with Artisan Migrate

1. **Open Terminal in cPanel** (or SSH):
   ```bash
   ssh your_cpanel_username@yourdomain.com
   ```

2. **Navigate to maincode**:
   ```bash
   cd ~/maincode
   ```

3. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

4. **Check for errors**:
   - If successful, you'll see output like: `Migration table created successfully`
   - If errors, check your `.env` database credentials

---

## Step 8: When It's OK, Generate New Key

1. **Still in terminal**, run:
   ```bash
   php artisan key:generate
   ```

2. **This updates `.env`** with `APP_KEY=base64:xxxxx`

3. **Verify** in `.env` that `APP_KEY` is now filled

---

## Step 9: Create Storage:link

1. **Still in terminal**, run:
   ```bash
   php artisan storage:link
   ```

2. **This creates a symlink**:
   ```
   maincode/public/storage → maincode/storage/app/public
   ```

---

## Step 10: Create Symlink from "maincode" Into "public_html"

This connects `public_html/index.php` to `maincode/` files.

1. **Still in terminal**, run:
   ```bash
   cd ~/public_html
   ln -s ../maincode/storage/app/public storage
   ```

2. **Verify it worked**:
   ```bash
   ls -la ~/public_html/
   ```
   
   You should see:
   ```
   storage -> ../maincode/storage/app/public
   ```

---

## Step 11: Enjoy It! 🎉

1. **Visit your domain**:
   ```
   https://yourdomain.com
   ```

2. **You should see your Laravel app**

3. **If you get an error**, check:
   - Error log: cPanel → Error Log
   - Laravel log: `~/maincode/storage/logs/laravel.log`
   - Permissions: `chmod -R 777 ~/maincode/storage ~/maincode/bootstrap/cache`

---

## Final Folder Structure

```
~/
├── maincode/                      ← Your Laravel app (backend)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── storage/
│   │   ├── app/
│   │   │   └── public/            ← Uploaded files stored here
│   │   └── logs/
│   ├── .env                       ← Database credentials
│   ├── artisan
│   ├── composer.json
│   └── ...
│
└── public_html/                   ← What users see
    ├── .htaccess                  ← URL rewriting
    ├── index.php                  ← Entry point
    ├── css/
    ├── js/
    └── storage → ../maincode/storage/app/public  ← Symlink to uploads
```

---

## Common Issues & Fixes

### 500 Error on Visit
- Check error log: cPanel → Error Log
- Check Laravel log: `~/maincode/storage/logs/laravel.log`
- Ensure `.htaccess` is in `public_html/`

### Permission Denied Errors
```bash
chmod -R 777 ~/maincode/storage
chmod -R 777 ~/maincode/bootstrap/cache
```

### Database Connection Error
- Verify `.env` credentials match cPanel MySQL settings
- Check database exists in cPanel → MySQL Databases

### Uploaded Files Not Displaying
- Verify symlink exists: `ls -la ~/public_html/storage`
- If not, run: `ln -s ../maincode/storage/app/public ~/public_html/storage`

### Artisan Commands Not Working
- Make sure you're in the `~/maincode` directory
- Use full path: `php ~/maincode/artisan migrate`

---

## Update Your Code Later

When you need to update code:

1. **Upload new zip** to home directory
2. **Extract it**
3. **Copy new files** to `~/maincode/` (overwrite old ones)
4. **Don't modify** the `public_html/` folder
5. **Done!** No need to recreate symlinks

---

## That's It!

You now have a working Laravel app on shared hosting. Enjoy! 🚀
