# LPU eConnect Portal

A complete university portal with PHP, MySQL, HTML & CSS.

## Setup Instructions

### 1. Import Database
- Open phpMyAdmin → Import → Select `database.sql`
- Or run: `mysql -u root -p < database.sql`

### 2. Configure Database
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // your MySQL username
define('DB_PASS', '');          // your MySQL password
define('DB_NAME', 'lpu_econnect');
```

### 3. Place Files
- Copy the `lpu_econnect` folder to your XAMPP/WAMP `htdocs` directory
- Start Apache & MySQL in XAMPP

### 4. Access
- URL: `http://localhost/lpu_econnect`

## Demo Credentials

| Role    | ID       | Password    |
|---------|----------|-------------|
| Student | 11907832 | student123  |
| Student | 11907833 | student123  |
| Faculty | FAC001   | faculty123  |
| Faculty | FAC002   | faculty123  |

## Features

### Student Portal
- Dashboard with stats
- Attendance (course-wise with percentage)
- Results & Marks
- Class Timetable
- Fee Status
- Profile management

### Faculty Portal
- Dashboard
- Student List (searchable)
- Mark Attendance
- Add/Update Marks
- Post Notices
- Profile management

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5 + CSS3 (no frameworks)
- Google Fonts (Plus Jakarta Sans)
