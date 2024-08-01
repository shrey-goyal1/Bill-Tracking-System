# Bill Tracking System

## Overview
The Bill Tracking System is a comprehensive web application developed to streamline the management and approval process of bills within an organization. This system is designed to provide a secure and user-friendly interface for various stakeholders, including Department Heads, Unit Heads, Accounts Heads, and Superusers. It offers features like bill submission, approval, rejection, and detailed tracking of bill statuses.

## Features
- **Multi-Role Dashboards**: Tailored dashboards for different user roles, offering functionalities specific to each role's requirements.
- **Bill Submission and Approval**: A streamlined process for submitting, approving, and rejecting bills, including the ability to add comments for transparency.
- **Bill Resubmission**: Secure functionality for clients to confirm, edit, and resubmit bills.
- **Automated Email Notifications**: Integration with PHPMailer for sending notifications regarding bill status changes.
- **Data Security**: Robust security measures to ensure the confidentiality and integrity of sensitive financial data.
- **User Management**: Admin panel for managing user accounts and permissions.

## Technologies Used
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL (handled via XAMPP Server)
- Email Integration: PHPMailer

## Requirements
- XAMPP Server (for local development)
- Web browser (Chrome, Firefox, etc.)
- Basic knowledge of PHP and MySQL

## Setup
1. Setup Database:
   - Import the provided SQL file into your MySQL database.
   - Configure the database connection in the `config.php` file.

2. Configure PHPMailer:
   - Update the email configuration settings in `mail_config.php` to match your email server credentials.

3. Run the Application:
   - Start XAMPP and navigate to `http://localhost/bill-tracking-system` in your browser.

## Usage
- **Login**: Use the provided credentials to log in as a Department Head, Unit Head, Accounts Head, or Superuser.
- **Dashboard Navigation**: Access your dashboard to view, approve, or reject bills.
- **Bill Submission**: Submit new bills via the submission form. Track the status of your submitted bills through the dashboard.
- **Notifications**: Receive email notifications for any updates on the bills.

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request with your changes.

## License
This project is licensed under the MIT License.

## Contact
For any questions or further information, please contact **Shrey Goyal** at **shreygoyal717@gmail.com**.

