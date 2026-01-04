# Linavelt Project

## Overview
Linavelt is a Laravel-based project designed to provide a robust and scalable web application framework. It integrates modern tools such as Tailwind CSS and Node.js to enhance development efficiency and user experience.

## Features
- **Laravel Framework**: Backend powered by Laravel for rapid development.
- **Tailwind CSS**: Utility-first CSS framework for styling.
- **Node.js Integration**: For managing frontend dependencies and build processes.
- **GitHub Workflows**: Automated CI/CD pipelines for linting, testing, and security scans.
- **Fedora 43 Compatibility**: Ensures compatibility with Fedora 43 systems.

## Prerequisites
- PHP 8.4 or higher
- Node.js 22 or higher
- Composer
- npm
- A Fedora 43 system (or compatible Linux distribution)

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/nixbys/linavelt.git
   cd linavelt
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Set up environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run database migrations:
   ```bash
   php artisan migrate
   ```

## Usage
- Start the Laravel development server:
  ```bash
  php artisan serve
  ```
- Start the Vite development server:
  ```bash
  npm run dev
  ```

## Testing
Run the test suite:
```bash
php artisan test
```

## GitHub Workflows
This project includes the following workflows:
- **Linting**: Ensures code quality.
- **Testing**: Runs the test suite.
- **Security Scans**: Performs security analysis using CodeQL.

## Contributing
1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add your message here"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/your-feature-name
   ```
5. Open a pull request.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Contact
For questions or support, please contact the project maintainers.
