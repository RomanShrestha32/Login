**Description : **
# Login-Register System with Two-Factor Authentication (2FA)

This project is a PHP-based web application that provides user registration, login, logout, and password reset functionalities. It includes a two-factor authentication (2FA) system to enhance security using OTP (One-Time Password). The application is styled using HTML, CSS (custom styles), and Bootstrap, while PHP handles the back-end logic.

## Project Structure

### Front-End
The front-end consists of the user interface (UI) elements such as forms, buttons, and input fields that allow users to interact with the application. The pages are styled using CSS and Bootstrap for a responsive design and consistent layout.

1. **HTML and CSS**
   - Pages like `index.php`, `login.php`, `registration.php`, `forgot-password.php`, and `reset-password.php` use standard HTML forms for user input.
   - **Custom styles** are defined in `styles.css` and applied across the project.
   - Bootstrap components are used to style elements and ensure responsive design.

2. **Bootstrap**
   - Bootstrap is used for styling forms, buttons, and layout grids.
   - Predefined Bootstrap classes make the UI mobile-friendly and maintain a professional appearance without custom CSS coding for common components.

### Back-End
The back-end of this project handles the server-side logic, including user authentication, session management, and database interactions. It is built using PHP and MySQL.

1. **PHP**
   - PHP scripts are used to validate user inputs and interact with the database.
   - Logic for registration, login, logout, and 2FA verification is handled in various PHP files (e.g., `login.php`, `registration.php`, `verify_2fa.php`).

2. **MySQL Database**
   - The database is used to store user details (name, email, password) and OTP codes.
   - Users' passwords are encrypted before being stored in the database.
   - The OTP (used for 2FA) is temporarily stored in the database and compared during the verification step.

### Project Files and Directories

- **`vendor/`**: This directory contains third-party libraries installed via Composer (e.g., PHP Mailer for sending OTP emails).
- **`Background.jpg`, `Backgroundd.jpg`, `Backgrounddd.jpg`**: Background images used in the design of login and registration pages.
- **`composer.json`**: Contains metadata about the project and PHP packages required for this application.
- **`composer.lock`**: Records the exact versions of the installed PHP packages.
- **`database.php`**: This file manages the connection to the MySQL database, ensuring the application interacts with the database correctly.
- **`forgot-password.php`**: Handles user requests to reset their password and sends them a password reset link via email.
- **`index.php`**: Acts as the landing page, allowing users to navigate to either the login or registration page.
- **`login_register.php`**: This file contains the logic for user login and registration, including input validation and database interaction.
- **`login.php`**: Displays the login form and validates user credentials. If two-factor authentication is enabled, it prompts the user to enter the OTP.
- **`logout.php`**: Logs the user out by destroying the session and redirecting them to the login page.
- **`mailer.php`**: Responsible for sending emails using PHP Mailer, such as OTPs and password reset links. The sender is named 'login-register'.
- **`otp.php`**: Presents a form for entering the OTP sent via email, completing the 2FA process.
- **`registration.php`**: Allows users to create a new account. The form includes fields for full name, email, and password.
- **`reset-password.php`**: A form for resetting a forgotten password, with password matching validation and submission to update the user's password.
- **`styles.css`**: Contains custom styles applied across the application, such as background colors and form styles.
- **`verify_2fa.php`**: Verifies the OTP entered by the user during login. If correct, it authenticates the user.

## Features

### 1. **User Registration**
   Users can register with their name, email, and password. The registration form checks for valid email formatting and ensures the password and confirm password fields match.

### 2. **Login with 2FA**
   After logging in, users are required to input a One-Time Password (OTP) sent to their registered email address to complete the login process. This adds an extra layer of security.

### 3. **Password Reset**
   If users forget their password, they can request a reset link via the "Forgot Password" option. They will receive an email with a link to reset their password.

### 4. **Logout**
   The application supports secure session management, allowing users to log out at any time. Upon logging out, the session is destroyed, and users are redirected to the login page.

### 5. **Two-Factor Authentication (2FA)**
   This security measure sends an OTP to the user’s email address, which must be entered to complete login, ensuring unauthorized access is prevented even if the password is compromised.

## How to Run the Project

1. **Install XAMPP:**
   Ensure that you have XAMPP or another local development environment that supports PHP and MySQL.

2. **Clone the Project:**
   Place the project folder in the `htdocs` directory of your XAMPP installation. For example:
   ```
   C:\xampp\htdocs\login-register
   ```

3. **Install Dependencies:**
   Run the following command in the project directory to install PHP dependencies using Composer:
   ```
   composer install
   ```

4. **Database Setup:**
   - Create a MySQL database named `login-register`.
   - Import the provided SQL file (if applicable) or manually create tables with the following fields:
     - **id** (INT, Primary Key)
     - **full_name** (VARCHAR)
     - **email** (VARCHAR)
     - **password** (VARCHAR, encrypted)
     - **otp** (INT, stores temporary OTP for 2FA)

5. **Configure Database:**
   Update the `database.php` file with your MySQL connection details:
   ```php
   $conn = new mysqli('localhost', 'root', '', 'login-register');
   ```

6. **Run the Application:**
   Start the Apache server from XAMPP and visit `http://localhost/login-register` in your browser.

## Future Implementations

### 1. **User Roles and Permissions**
   Implement different user roles such as admin and regular users. This would allow certain parts of the system to be restricted to authorized users only (e.g., an admin dashboard).

### 2. **Profile Management**
   Add a profile management system where users can update their details such as their name, email, or password from within the application.

### 3. **Enhanced Security**
   - Implement stronger password policies (e.g., password complexity requirements, password expiration).
   - Use CAPTCHA to prevent automated form submissions (e.g., for login or registration).
   - Store hashed passwords using more advanced algorithms like `bcrypt` instead of `md5` or `sha256`.

### 4. **Email Verification**
   Before enabling access to the platform, require users to verify their email by sending them a unique verification link after registration.

### 5. **Multi-Language Support**
   Allow users to choose different languages for the UI to cater to a more global audience.

### 6. **OAuth Integration**
   Integrate social media login options (e.g., Google, Facebook) for user convenience and broader accessibility.

### 7. **Advanced Analytics Dashboard**
   Provide an admin dashboard that displays user activity, registration statistics, login attempts, etc., using charts and graphs.

## License

This project is open-source and can be modified and used under the terms of the MIT License.
```

### Explanation of Additional Sections

- **Front-End and Back-End**: Describes the technologies and frameworks used for the UI and server-side logic.
- **Future Implementations**: Suggests features that could enhance the application in the future, such as user roles, profile management, and improved security measures.

-Roman Shrestha#   L o g i n  
 