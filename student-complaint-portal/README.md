# 🎓 Student Complaint Portal
### BCA Mini Project | PHP + MySQL + HTML/CSS/JS

A web-based complaint management system for college students to submit and track grievances anonymously.

---

## 📁 Folder Structure

```
student-complaint-portal/
│
├── index.html                  ← Homepage
├── login.html                  ← Student Login
├── dashboard.html              ← Student Dashboard (view complaints)
├── submit_complaint.html       ← Submit Complaint Form
├── check_status.html           ← Public complaint tracker (no login needed)
│
├── admin/
│   ├── login.html              ← Admin Login
│   └── dashboard.html          ← Admin Complaint Management Panel
│
├── css/
│   └── style.css               ← All styles
│
├── js/
│   └── script.js               ← All frontend logic
│
├── php/
│   ├── student_login.php       ← Student authentication
│   ├── admin_login.php         ← Admin authentication
│   ├── logout.php              ← Session destroy + redirect
│   ├── submit_complaint.php    ← Create new complaint
│   ├── get_complaints.php      ← Admin: fetch all complaints
│   ├── get_student_complaints.php ← Student: fetch own complaints
│   ├── get_departments.php     ← Load department list
│   ├── check_status.php        ← Public status lookup by code
│   └── update_status.php       ← Admin: update complaint status
│
├── config/
│   └── db.php                  ← Database connection
│
└── database.sql                ← Run this to create DB + tables
```

---

## ⚙️ Setup Instructions (Step by Step)

### Prerequisites
- XAMPP / WAMP / LAMP installed
- PHP 7.4+
- MySQL 5.7+
- A web browser

---

### Step 1: Copy the Project
Copy the `student-complaint-portal` folder to:
- **XAMPP**: `C:/xampp/htdocs/`
- **WAMP**: `C:/wamp64/www/`
- **Linux**: `/var/www/html/`

---

### Step 2: Setup the Database

1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **"New"** → Create a database named `complaint_portal`
3. Select the database → Click **"Import"**
4. Choose the `database.sql` file from the project folder
5. Click **"Go"**

---

### Step 3: Configure Database Connection

Open `config/db.php` and update:

```php
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASS', '');         // Your MySQL password (blank for XAMPP default)
```

---

### Step 4: Run the Project

Open your browser:
```
http://localhost/student-complaint-portal/
```

---

## 🔑 Demo Login Credentials

### Student Login
| Field | Value |
|-------|-------|
| Student ID | `BCA2024001` |
| Password | `student123` |

### Admin Login
| Field | Value |
|-------|-------|
| Email | `admin@college.edu` |
| Password | `admin123` |

> ⚠️ **Remove demo credentials before deployment!**

---

## 🛡️ Security Features

- Passwords stored using `password_hash()` (bcrypt)
- SQL injection prevention using `real_escape_string()`
- Session-based authentication
- Input validation on both frontend (JS) and backend (PHP)
- Admin routes protected by session checks

---

## 📦 Key Features

| Feature | Description |
|---------|-------------|
| Anonymous Submission | Students can hide identity while submitting |
| Unique Complaint Code | Auto-generated code like `CMP-2024-0001` |
| Status Tracking | Public tracker needs only complaint code |
| Admin Dashboard | View, filter, and update complaint statuses |
| Department Routing | Complaints forwarded to correct department |
| Input Validation | Both client-side (JS) and server-side (PHP) |

---

## 🗄️ Database Tables

| Table | Purpose |
|-------|---------|
| `students` | Student accounts |
| `admins` | Admin/staff accounts |
| `departments` | List of college departments |
| `complaints` | All submitted complaints |

---

## 🚀 Future Enhancements

- [ ] Email notifications when status changes
- [ ] File/image upload for proof/evidence
- [ ] Analytics dashboard with charts
- [ ] Department-wise admin accounts
- [ ] Mobile app version
- [ ] OTP-based login

---

## 👨‍💻 Developed By

- **Project**: Student Complaint Portal
- **Course**: BCA (Bachelor of Computer Applications)
- **Technology**: HTML, CSS, JavaScript, PHP, MySQL
- **Year**: 2024

---

> 📌 *This project is developed for academic purposes as a BCA Mini Project.*
