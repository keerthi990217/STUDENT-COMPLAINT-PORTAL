# 🔧 Server Setup & Troubleshooting Guide

## CRITICAL: System Requirements

Your application requires a **local PHP server with MySQL database**. It cannot run directly by opening HTML files in your browser.

### What You Need:
- **PHP 7.4+** with MySQLi extension
- **MySQL 5.7+** database server
- **Local development server** (XAMPP, WAMP, LAMP, or similar)

---

## ⚠️ Common Server Errors & Solutions

### ❌ Error: "Cannot find PHP" or "php command not recognized"
**Cause**: PHP is not installed or not in your system PATH
**Solution**: Install XAMPP or WAMP (see below)

### ❌ Error: "Database connection failed"
**Cause**: MySQL is not running or database doesn't exist
**Solution**: 
1. Start MySQL service
2. Import database.sql into your MySQL server
3. Verify credentials in config/db.php

### ❌ Error: "Cannot find database configuration file"
**Cause**: File path is incorrect or project not in web root
**Solution**: 
1. Ensure project is in XAMPP/WAMP web root
2. Check file structure matches README.md

---

## 🚀 Quick Start Guide

### Option 1: Using XAMPP (Windows - Recommended)

#### Step 1: Install XAMPP
1. Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Run installer and select:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin

#### Step 2: Copy Project
1. Copy `student-complaint-portal` folder to `C:/xampp/htdocs/`
2. Result: `C:/xampp/htdocs/student-complaint-portal/`

#### Step 3: Start Services
1. Open XAMPP Control Panel
2. Start "Apache" and "MySQL"
3. Wait for both to show "Running" status

#### Step 4: Setup Database
1. Open browser → `http://localhost/phpmyadmin`
2. Login with default credentials:
   - Username: `root`
   - Password: (leave blank)
3. Click **"New"** and create database: `complaint_portal`
4. Go to **"Import"** tab
5. Select `database.sql` from the project folder
6. Click **"Go"**

#### Step 5: Configure DB Connection (if needed)
1. Open `config/db.php`
2. Update MySQL credentials if different from defaults:
   ```php
   define('DB_USER', 'root');     // Your MySQL username
   define('DB_PASS', '');         // Your MySQL password
   ```

#### Step 6: Access Application
1. Open browser → `http://localhost/student-complaint-portal/`
2. You should see the homepage

---

### Option 2: Using WAMP (Windows Alternative)

1. Download from [http://www.wampserver.com/](http://www.wampserver.com/)
2. Install and copy project to `C:/wamp64/www/`
3. Start WampServer
4. Access: `http://localhost/student-complaint-portal/`
5. Setup database same as XAMPP

---

### Option 3: Using Built-in PHP Server (For Quick Testing)

⚠️ **Note**: Works for testing but not recommended for production

```bash
cd c:\Users\Kiran Kumar\Downloads\student-complaint-portal
php -S localhost:8000
```

Then open: `http://localhost:8000/`

**⚠️ Limitation**: Built-in server has no MySQL support. Use XAMPP/WAMP for full functionality.

---

## 🗄️ Database Import Instructions

### Via phpMyAdmin (Easiest)
1. Open `http://localhost/phpmyadmin`
2. Create new database: `complaint_portal`
3. Select database
4. Click **"Import"** tab
5. Choose `database.sql`
6. Click **"Go"**

### Via MySQL Command Line (Advanced)
```bash
mysql -u root -p < database.sql
```

Or if no password:
```bash
mysql -u root < database.sql
```

---

## 🐛 Debugging Tips

### Check Error Logs
**XAMPP**:
- Apache errors: `C:/xampp/apache/logs/error.log`
- PHP errors: `C:/xampp/apache/logs/php_error.log`

**WAMP**:
- Check WAMP menu → Logs

### Enable PHP Error Display
Edit `php.ini` in XAMPP/WAMP:
```ini
display_errors = On
error_reporting = E_ALL
```

### Test Database Connection
Create a test file `test_db.php` in your project:
```php
<?php
$conn = new mysqli('localhost', 'root', '', 'complaint_portal');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
echo 'Connected successfully!';
$conn->close();
?>
```

Then visit: `http://localhost/student-complaint-portal/test_db.php`

---

## 📋 Default Demo Credentials

### Student Login
- **Student ID**: (Set up via Registration first)
- **Password**: (Your registered password)

### Admin Login
- Demo admin account needs to be created in database
- Insert via phpMyAdmin:
  ```sql
  INSERT INTO admins (name, email, password) 
  VALUES ('Admin', 'admin@college.com', '$2y$10$YourHashedPasswordHere');
  ```

---

## ✅ Checklist Before Testing

- [ ] XAMPP/WAMP installed and running
- [ ] Apache server started ✓
- [ ] MySQL server started ✓
- [ ] Project copied to `htdocs` (or `www`)
- [ ] Database `complaint_portal` created
- [ ] `database.sql` imported successfully
- [ ] `config/db.php` has correct credentials
- [ ] Can access `http://localhost/student-complaint-portal/`

---

## 📞 Still Having Issues?

1. Check browser console (F12 → Console tab) for errors
2. Check network tab to see failed requests
3. Verify both Apache and MySQL are running
4. Check error logs (see above)
5. Ensure port 80 (Apache) and 3306 (MySQL) are not blocked
6. Try restarting XAMPP/WAMP services

---

## 🔒 Security Notes

- ✅ Code has been updated with better error handling
- ⚠️ Don't use default credentials in production
- ⚠️ Set strong MySQL passwords
- ⚠️ Use HTTPS in production
- Consider using prepared statements for additional SQL injection protection

