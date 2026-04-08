## Project Setup Guide

To run this project successfully, please follow the steps below.

### Step 1 — Install XAMPP
Install XAMPP and make sure Apache and MySQL services are available.

### Step 2 — Start the Server
Open the XAMPP Control Panel and start:
- Apache
- MySQL

### Step 3 — Place the Project in `htdocs`
Copy the project folder into the XAMPP `htdocs` directory.

**Project path example:**
`C:\xampp\htdocs\mbsl_employee_directory`

### Step 4 — Create the Database
Open phpMyAdmin using:
`http://localhost/phpmyadmin/`

Create a new database called:
`mbsl_employee_directory`

### Step 5 — Import the Database File
Select the created database, click **Import**, choose the provided `database.sql` file, and click **Go**.

### Step 6 — Configure Database Connection
Open `app/config/db.php` and verify the database connection settings.

Default configuration:
- Host: `localhost`
- Username: `root`
- Password: *(empty in XAMPP default setup)*
- Database: `mbsl_employee_directory`

### Step 7 — Verify Upload and Asset Folders
Ensure the following folders exist:
- `uploads/users/`
- `uploads/employees/`
- `assets/images/`

### Step 8 — Add Default Image
Make sure the default image file is available at:
`assets/images/default-user.jpg`

### Step 9 — Set Up PDF Export Library
Copy the FPDF library into:
`app/helpers/fpdf/`

Required files:
- `fpdf.php`
- `font/`

### Step 10 — Run the Project
Open the following URL in your browser:
`http://localhost/mbsl_employee_directory/public/login.php`

### Step 11 — Login and Use the System
Use the provided admin or staff login credentials to access the project features.

### Step 12 — Main Modules
The system includes:
- Login / Register
- Dashboard
- Users Management
- Employees Management
- My Profile
- CSV / PDF Export
- Dark Mode

### Step 13 — Troubleshooting
If the system does not run correctly, check:
- Apache and MySQL are running
- Database has been imported successfully
- Database connection file is correct
- Project folder is inside `htdocs`
- FPDF library is in the correct location


##//////////////////////////////////////////////////////API LIST////////////////////////////////////////////////////////////////##


## 🔗 API Endpoints

### 🔐 Authentication
| Method | Endpoint | Description |
|--------|--------|------------|
| POST | /api/auth/login.php | User login |
| POST | /api/auth/register.php | User registration |
| POST | /api/auth/logout.php | User logout |

---

### 👤 Users Management
| Method | Endpoint | Description |
|--------|--------|------------|
| GET | /api/users/index.php | Get all users |
| POST | /api/users/index.php | Add new user |
| POST | /api/users/index.php?action=update | Update user |
| POST | /api/users/index.php?action=delete | Delete user |
| GET | /api/users/index.php?action=view&id=1 | View user details |

---

### 🧑‍💼 Employees Management
| Method | Endpoint | Description |
|--------|--------|------------|
| GET | /api/employees/index.php | Get all employees |
| POST | /api/employees/index.php | Add employee |
| POST | /api/employees/index.php?action=update | Update employee |
| POST | /api/employees/index.php?action=delete | Delete employee |
| GET | /api/employees/index.php?action=view&id=1 | View employee |

---

### 📊 Dashboard
| Method | Endpoint | Description |
|--------|--------|------------|
| GET | /api/dashboard/stats.php | Get dashboard statistics |

---

### 👤 Profile
| Method | Endpoint | Description |
|--------|--------|------------|
| GET | /api/profile/index.php | Get profile details |
| POST | /api/profile/index.php | Update profile |

---

### 📤 Export
| Method | Endpoint | Description |
|--------|--------|------------|
| GET | /api/employees/export.php?type=csv | Export employees CSV |
| GET | /api/employees/export.php?type=pdf | Export employees PDF |



Images screenshorts ?