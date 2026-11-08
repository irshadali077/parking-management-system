# ParkEase - Setup Guide

## XAMPP Installation & Setup

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/
2. Run the installer and follow the setup wizard.
3. Install to the default location: `C:\xampp\`
4. Make sure **Apache** and **MySQL** are selected during installation.

### Step 2: Start XAMPP Services
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache** (web server).
3. Click **Start** next to **MySQL** (database server).
4. Both should turn green, meaning they are running.

### Step 3: Copy the Project
1. Copy the entire `parking-management-system` folder.
2. Paste it inside `C:\xampp\htdocs\`.
3. The path should look like: `C:\xampp\htdocs\parking-management-system\`

### Step 4: Import the Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** on the left sidebar to create a new database.
3. Enter the database name: `parking_db`
4. Click **"Create"**.
5. Click on the **"Import"** tab at the top.
6. Click **"Choose File"** and select the file:
   `parking-management-system/database/parking_db.sql`
7. Click **"Go"** at the bottom.
8. You should see a success message. The 4 tables will be created.

### Step 5: Run the Project
1. Open your browser.
2. Go to: `http://localhost/parking-management-system/`
3. You will be redirected to the login page.

---

## Default Login Credentials

### Admin Login
- **URL**: `http://localhost/parking-management-system/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

### User Login
- Create a new account from the signup page.
- **URL**: `http://localhost/parking-management-system/user/signup.php`

---

## Folder Structure Explained

```
parking-management-system/
├── index.php              → Entry point (redirects to login)
├── config/
│   └── db.php             → Database connection settings
├── database/
│   └── parking_db.sql     → SQL file to import into phpMyAdmin
├── includes/
│   ├── header.php         → Common navigation bar + HTML head
│   ├── footer.php         → Footer + chatbot + JS includes
│   └── auth_check.php     → Checks if user is logged in
├── assets/
│   ├── css/style.css      → All styles for the project
│   └── js/
│       ├── main.js        → General UI interactions
│       └── chatbot.js     → Chatbot logic (offline)
├── user/
│   ├── signup.php         → User registration
│   ├── login.php          → User login
│   ├── forgot_password.php→ Password reset (security question)
│   ├── logout.php         → Log out
│   ├── dashboard.php      → User dashboard
│   ├── book_slot.php      → View & book parking slots
│   ├── cancel_booking.php → Cancel active booking
│   ├── history.php        → Booking history
│   └── profile.php        → Edit profile & password
├── admin/
│   ├── login.php          → Admin login
│   ├── logout.php         → Admin logout
│   ├── dashboard.php      → Admin statistics
│   ├── users.php          → View all users
│   ├── bookings.php       → View all bookings
│   ├── slots.php          → Add/edit/delete slots
│   └── includes/
│       ├── admin_header.php → Admin navigation
│       └── admin_auth.php   → Admin login check
├── chatbot/
│   └── chatbot.php        → Chatbot HTML widget
└── docs/
    ├── SETUP_GUIDE.md     → This file
    └── VIVA_QA.md         → Viva questions & answers
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Connection failed" error | Make sure MySQL is running in XAMPP |
| Page not found (404) | Check the folder is in `C:\xampp\htdocs\` |
| "Access denied" database error | Default username is `root` with empty password |
| Styles not loading | Make sure folder structure is correct |
| Can't login as admin | Import the SQL file first, password is `admin123` |

---

## Technologies Used

| Technology | Purpose |
|-----------|---------|
| HTML5 | Page structure and forms |
| CSS3 | Styling, layout, responsive design |
| JavaScript | Client-side interactivity, chatbot |
| PHP | Server-side logic, authentication |
| MySQL | Data storage and management |
| XAMPP | Local development server |
