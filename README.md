
# Laravel E-commerce Backend

This is a Laravel-based e-commerce backend project, featuring user roles such as Admin, Seller, and Customer. It also includes product and order management, product reviews, and category management.

## Features

- **User Roles**: Admin, Seller, Customer
- **Product Management**: CRUD operations for products
- **Category Management**: Multiple categories per product
- **Product Reviews**: Customers can review products
- **Order Management**: Place and track orders
- **API Documentation**: Detailed API documentation available

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL or MariaDB
- Laravel >= 11.x

### Installation

1. Clone the repository:

   \`\`\`bash
   git clone https://github.com/juwelrana090/laravel-ecommerce-backend.git
   \`\`\`

2. Navigate to the project directory:

   \`\`\`bash
   cd laravel-ecommerce-backend
   \`\`\`

3. Install dependencies:

   \`\`\`bash
   composer install
   \`\`\`

4. Set up the `.env` file:

   \`\`\`bash
   cp .env.example .env
   \`\`\`

   Update the database and other configurations in the `.env` file.

5. Generate the application key:

   \`\`\`bash
   php artisan key:generate
   \`\`\`

6. Run migrations:

   \`\`\`bash
   php artisan migrate
   \`\`\`

7. Seed the database:

   \`\`\`bash
   php artisan db:seed
   \`\`\`

8. Run the project:

   \`\`\`bash
   php artisan serve
   \`\`\`

   The project will run at [http://localhost:8000](http://localhost:8000).

### Admin, Seller, and Customer Credentials

You can log in with the following demo credentials:

- **Admin User**:
  - Email: `admin@gmail.com`
  - Password: `12345678Aa`
  
- **Seller User**:
  - Email: `seller@gmail.com`
  - Password: `12345678Aa`
  
- **Customer User**:
  - Email: `customer@gmail.com`
  - Password: `12345678Aa`

## API Documentation

You can find the complete API documentation here: [API Documentation](https://ecommerce-backend.codingzonebd.com/api/documentation)

### API Demo URL

You can try the API demo at: [API Demo URL](https://ecommerce-backend.codingzonebd.com/api/v1)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
