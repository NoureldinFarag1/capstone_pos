<p align="center"><img src="/Users/noureldinfarag/capstone_pos/public/images/logo.png" width="400" alt="Project Logo"></p>

<p align="center">
<a href="https://github.com/yourusername/yourproject/actions"><img src="https://github.com/yourusername/yourproject/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/yourusername/yourproject"><img src="https://img.shields.io/packagist/dt/yourusername/yourproject" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/yourusername/yourproject"><img src="https://img.shields.io/packagist/v/yourusername/yourproject" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/yourusername/yourproject"><img src="https://img.shields.io/packagist/l/yourusername/yourproject" alt="License"></a>
</p>

## About the Project

The project is a point-of-sale (POS) system designed specifically for brand clothing stores. It aims to streamline the sales process, manage inventory, and provide detailed sales reports. The application includes features such as:

- Efficient sales processing and receipt generation
- Inventory management with real-time updates
- Detailed sales and inventory reports
- User management and role-based access control

## Screenshots

Here are some screenshots of the project:

![Screenshot 1](/Users/noureldinfarag/capstone_pos/public/images/Screenshot 2025-01-17 at 4.36.51 PM.png)
![Screenshot 2](/Users/noureldinfarag/capstone_pos/public/images/Screenshot 2025-01-17 at 4.37.05 PM.png)
![Screenshot 3](/Users/noureldinfarag/capstone_pos/public/images/Screenshot 2025-01-17 at 4.38.13 PM.png)
![Screenshot 4](/Users/noureldinfarag/capstone_pos/public/images/Screenshot 2025-01-17 at 4.39.07 PM.png)
![Screenshot 5](/Users/noureldinfarag/capstone_pos/public/images/Screenshot 2025-01-17 at 4.39.25 PM.png)


## Installation

To install the project locally, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/NoureldinFarag1/capstone_pos.git
    ```
2. Navigate to the project directory:
    ```bash
    cd capstone_poss
    ```
3. Install the dependencies:
    ```bash
    composer install
    npm install
    ```
4. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```bash
    cp .env.example .env
    ```
5. Generate the application key:
    ```bash
    php artisan key:generate
    ```
6. Run the database migrations:
    ```bash
    php artisan migrate
    ```

## Usage

To start the development server, run:
```bash
php artisan serve
```

Then, open your browser and visit `http://localhost:8000` to see the application in action.

## Contributing

Thank you for considering contributing to this project! Please read the [contribution guide](CONTRIBUTING.md) for details on the process for submitting pull requests.

## Code of Conduct

In order to ensure that the community is welcoming to all, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within the project, please send an e-mail to [your email](mailto:youremail@example.com). All security vulnerabilities will be promptly addressed.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
