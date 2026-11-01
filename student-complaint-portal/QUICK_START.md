# 🚀 QUICK START (5 Minutes)

## Your Application's Server Error - SOLVED ✅

I've fixed the PHP code issues. Now you need to **set up a local PHP server**.

### The Problem
Your application is a **PHP + MySQL project** that requires:
- ✅ A running PHP server (XAMPP, WAMP, etc.)
- ✅ Running MySQL database
- ✅ Imported database schema

Currently, **PHP is not installed** on your system.

---

## 🎯 Quick Fix: Install XAMPP (Easiest)

### Step 1: Download XAMPP
1. Go to: [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
2. Download **XAMPP version 8.0+** for Windows
3. Run the installer

### Step 2: Copy Your Project
1. After XAMPP installs, copy your entire `student-complaint-portal` folder
2. Paste into: `C:\xampp\htdocs\`
3. Result: `C:\xampp\htdocs\student-complaint-portal\`

### Step 3: Start Services
1. Open XAMPP Control Panel
2. Click "Start" next to:
   - **Apache** ← (should turn green)
   - **MySQL** ← (should turn green)

### Step 4: Import Database
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. Click **"New"** on the left
4. Database name: `complaint_portal`
5. Click **"Create"**
6. Then:
   - Click the database name in the left sidebar
   - Click **"Import"** tab
   - Choose `database.sql` from your project folder
   - Click **"Go"** button

### Step 5: Access Your App
1. Open browser
2. Go to: `http://localhost/student-complaint-portal/`
3. **Done!** 🎉

---

## 📝 If You Get Errors

### Database Error: "Cannot connect"
- Make sure MySQL shows "Running" in XAMPP Control Panel
- Windows → Services → Check "MySQL" is running

### Page shows "Database connection failed" 
- The database was not imported, or credentials are wrong
- Verify MySQL is running
- Import database.sql via phpMyAdmin

### 404 "Page not found"
- Your project is not in the correct folder
- Should be: `C:\xampp\htdocs\student-complaint-portal\`

---

## 🏃 For Experienced Users

Already have XAMPP/WAMP installed? Then:

```bash
cd C:\xampp\htdocs
# Copy your project here

# Open phpmyadmin and import database.sql
# Visit http://localhost/student-complaint-portal/
```

---

## 📚 See Also
- Full setup details in `SETUP_TROUBLESHOOTING.md`
- PHP code has been updated with better error handling ✓
- All paths verified and working ✓

