-- LPU eConnect Database
-- Import this file in phpMyAdmin

CREATE DATABASE IF NOT EXISTS lpu_econnect;
USE lpu_econnect;

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reg_no VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100),
    semester INT DEFAULT 1,
    section VARCHAR(10),
    phone VARCHAR(15),
    photo VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Faculty Table
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    designation VARCHAR(100),
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    credits INT DEFAULT 3,
    faculty_id INT,
    semester INT,
    department VARCHAR(100),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Late') DEFAULT 'Present',
    marked_by INT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Marks / Results Table
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    exam_type ENUM('Minor1', 'Minor2', 'Major', 'Assignment', 'Practical') NOT NULL,
    marks_obtained DECIMAL(5,2) DEFAULT 0,
    total_marks DECIMAL(5,2) DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Notices Table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT,
    category ENUM('General', 'Exam', 'Holiday', 'Event', 'Assignment') DEFAULT 'General',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Timetable Table
CREATE TABLE IF NOT EXISTS timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    day ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(50),
    section VARCHAR(10),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Assignments Table
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    course_id INT NOT NULL,
    faculty_id INT,
    due_date DATE NOT NULL,
    max_marks INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Fee Table
CREATE TABLE IF NOT EXISTS fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    due_date DATE,
    paid_date DATE,
    status ENUM('Pending', 'Paid', 'Partial') DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Sample Data: Faculty
INSERT INTO faculty (faculty_id, name, email, password, department, designation, phone) VALUES
('ADMIN001', 'Super Admin', 'admin@lpu.in', MD5('admin123'), 'Administration', 'Super Admin', '9876543219'),
('FAC001', 'Dr. Rajesh Kumar', 'rajesh@lpu.in', MD5('faculty123'), 'Computer Science', 'Professor', '9876543210'),
('FAC002', 'Dr. Priya Sharma', 'priya@lpu.in', MD5('faculty123'), 'Electronics', 'Associate Professor', '9876543211'),
('FAC003', 'Prof. Amit Singh', 'amit@lpu.in', MD5('faculty123'), 'Mathematics', 'Assistant Professor', '9876543212');

-- Sample Data: Students
INSERT INTO students (reg_no, name, email, password, course, semester, section, phone) VALUES
('11907832', 'Rahul Verma', 'rahul@lpu.in', MD5('student123'), 'B.Tech CSE', 5, 'K20WC', '9123456780'),
('11907833', 'Ananya Gupta', 'ananya@lpu.in', MD5('student123'), 'B.Tech CSE', 5, 'K20WC', '9123456781'),
('11907834', 'Vikram Patel', 'vikram@lpu.in', MD5('student123'), 'B.Tech ECE', 3, 'K21WC', '9123456782');

-- Sample Data: Courses
INSERT INTO courses (course_code, course_name, credits, faculty_id, semester, department) VALUES
('CSE101', 'Data Structures & Algorithms', 4, 1, 3, 'Computer Science'),
('CSE201', 'Database Management Systems', 4, 1, 5, 'Computer Science'),
('CSE301', 'Operating Systems', 3, 2, 5, 'Computer Science'),
('MATH101', 'Engineering Mathematics', 4, 3, 1, 'Mathematics'),
('ECE101', 'Digital Electronics', 4, 2, 3, 'Electronics');

-- Sample Data: Notices
INSERT INTO notices (title, content, posted_by, category) VALUES
('Mid Semester Exams Schedule', 'Mid semester examinations will be conducted from 15th October. Please check the detailed timetable on the portal.', 1, 'Exam'),
('University Sports Meet 2024', 'Annual sports meet is scheduled for 20th November. All students are encouraged to participate.', 2, 'Event'),
('Assignment Submission Deadline', 'DSA Assignment-2 must be submitted before 10th October 11:59 PM via the portal.', 1, 'Assignment'),
('Holiday Notice - Diwali', 'University will remain closed from 1st Nov to 5th Nov on account of Diwali.', 3, 'Holiday');

-- Sample Data: Attendance
INSERT INTO attendance (student_id, course_id, date, status) VALUES
(1, 2, '2024-09-01', 'Present'),
(1, 2, '2024-09-03', 'Present'),
(1, 2, '2024-09-05', 'Absent'),
(1, 3, '2024-09-02', 'Present'),
(2, 2, '2024-09-01', 'Present'),
(2, 3, '2024-09-02', 'Late');

-- Sample Data: Marks
INSERT INTO marks (student_id, course_id, exam_type, marks_obtained, total_marks) VALUES
(1, 2, 'Minor1', 28, 35),
(1, 2, 'Minor2', 30, 35),
(1, 3, 'Minor1', 25, 35),
(2, 2, 'Minor1', 32, 35),
(2, 3, 'Minor1', 29, 35);

-- Sample Fees
INSERT INTO fees (student_id, semester, amount, paid_amount, due_date, status) VALUES
(1, 5, 75000.00, 75000.00, '2024-07-31', 'Paid'),
(2, 5, 75000.00, 50000.00, '2024-07-31', 'Partial'),
(3, 3, 72000.00, 0.00, '2024-07-31', 'Pending');
