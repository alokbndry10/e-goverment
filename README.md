# Nagarik App Clone - E-Government Portal

A simple NID and Citizenship verification system built with PHP, HTML, CSS, and JavaScript for XAMPP.

## Features

### User Portal
- User registration and login
- Submit National ID (NID) documents for verification
- Submit Citizenship (Nagarikta) documents for verification
- View document status (Pending/Verified/Rejected)
- View verified documents

### Admin Portal
- Admin login
- View all submitted documents
- Filter documents by status and type
- Verify or reject documents with remarks
- Dashboard with statistics

## Installation

### Prerequisites
- XAMPP (Apache + MySQL)
- Web browser

### Setup Steps

1. **Copy the project folder** to your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\e goverment
   ```

2. **Start XAMPP** and ensure Apache and MySQL are running.

3. **Create the database**:
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named `nagarik_app`
   - Import the `database.sql` file or run the SQL commands manually

4. **Access the application**:
   - User Portal: http://localhost/e%20goverment/login.html
   - Admin Portal: http://localhost/e%20goverment/admin/

## Default Admin Credentials
- Username: `admin`
- Password: `admin123`

## Project Structure

```
e goverment/
├── admin/
│   ├── index.php        # Admin login
│   ├── dashboard.php    # Admin dashboard
│   └── logout.php       # Admin logout
├── auth/
│   ├── login.php        # User login handler
│   ├── register.php     # User registration handler
│   ├── logout.php       # User logout
│   └── upload_document.php # Document upload handler
├── css/
│   └── style.css        # Main stylesheet
├── js/
│   └── main.js          # JavaScript functions
├── uploads/             # Uploaded documents (auto-created)
├── config.php           # Database configuration
├── database.sql         # Database schema
├── login.html           # User login page
├── register.php         # User registration page
├── dashboard.php        # User dashboard
├── nid.php              # National ID upload page
├── citizenship.php      # Citizenship upload page
└── README.md            # This file
```

## Usage Flow

### For Users:
1. Register with mobile number and password
2. Login to access the dashboard
3. Click on "National ID" or "Citizenship" service
4. Upload document images and submit
5. Wait for admin verification
6. Once verified, view your documents

### For Admin:
1. Login to admin portal
2. View pending documents
3. Click on images to view full size
4. Verify or reject documents
5. Rejected documents allow users to resubmit

## Theme Colors
- Primary Blue: #1e3c72
- Secondary Blue: #2a5298
- Red: #dc143c
- Background: Linear gradient from blue to red
- Card Background: #d4e4fa

## Notes
- Make sure the `uploads` folder has write permissions
- The admin password is hashed using PHP's `password_hash()` function
- Sessions are used for authentication
