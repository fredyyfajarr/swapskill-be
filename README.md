```text
  _________                             __   .__.__  .__           _____________________
 /   _____/_  _  _______  ______  _____|  | _|__|  | |  |          \______   \_   _____/
 \_____  \\ \/ \/ /\__  \ \____ \/  ___/  |/ /  |  | |  |    ______ |    |  _/|    __)_ 
 /        \\     /  / __ \|  |_> >___ \|    <|  |  |_|  |__ /_____/ |    |   \|        \
/_______  / \/\_/  (____  /   __/____  >__|_ \__|____/____/         |______  /_______  /
        \/              \/|__|       \/     \/                             \/        \/ 
```

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
</div>

## 📑 Table of Contents
- [About The Project](#about-the-project)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Usage](#usage)
- [Contributing](#contributing)
- [License / Copyright](#license--copyright)

## 🚀 About The Project

**SwapSkill Backend (API)** serves as the robust infrastructure powering the SwapSkill platform—a dedicated community space designed to facilitate peer-to-peer skill exchanges. The core architectural vision leverages Laravel 13 to provide high-performance, secure, and maintainable RESTful APIs that interface perfectly with the frontend clients. 

This backend handles the heavy lifting: from secure user authentication, precise database modeling, and scalable real-time processing to serving admin panels dynamically using Filament. It is meticulously crafted with modern PHP practices, ensuring rapid data retrieval and reliable transaction integrity for every skill swap interaction.

## ✨ Key Features
- **Secure Authentication**: Leveraging Laravel Sanctum & Breeze for rock-solid stateless token authentication.
- **Admin Dashboard**: Fully featured administration panel powered by Filament 5.5 for effortless resource management.
- **High-Performance**: Utilizes Laravel Octane for significantly faster request handling and bootstrapping.
- **Comprehensive API**: RESTful endpoints specifically tailored to handle user profiles, skill listings, matching algorithms, and real-time messaging.
- **Scalable Architecture**: Built-in support for job queues and testing via PHPUnit and Pest to ensure enterprise-grade stability.

## 🛠 Tech Stack
- **Framework:** Laravel ^13.0
- **Admin Panel:** Filament ^5.5
- **Authentication:** Laravel Sanctum & Breeze
- **Performance/Server:** Laravel Octane
- **Language:** PHP ^8.3
- **Testing:** PHPUnit, Faker

## 📂 Project Structure
```text
swapskill-be/
├── app/                  # Core MVC logic, Models, Controllers, and Service layers
├── bootstrap/            # Application bootstrapping and configurations
├── config/               # Global framework and package configurations
├── database/             # Migrations, factories, and seeders
├── routes/               # API and web endpoint definitions
├── tests/                # Automated feature and unit testing suites
└── composer.json         # Package dependencies and PSR-4 autoloading
```

## 🏁 Getting Started

### Prerequisites
- **PHP**: v8.3 or higher
- **Composer**: Dependency manager for PHP
- **MySQL / PostgreSQL / SQLite**: Database environment

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/fredyyfajarr/swapskill-be.git
   ```
2. Navigate into the backend directory:
   ```bash
   cd swapskill-be
   ```
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Setup environment configurations:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Run database migrations:
   ```bash
   php artisan migrate
   ```

## 💻 Usage

To run the application locally, you can use Laravel's built-in artisan server:

```bash
php artisan serve
```

For high-performance local environments utilizing Octane (if Swoole or RoadRunner is installed):
```bash
php artisan octane:start
```

The API endpoints will be accessible at `http://localhost:8000/api`.

## 🤝 Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License / Copyright

Copyright &copy; 2026 Fredy Fajar Adi Putra. All Rights Reserved.
