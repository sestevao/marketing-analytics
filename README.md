# Marketing Analytics Dashboard

A unified marketing analytics dashboard built with **Laravel**, **Inertia.js**, **Vue 3**, and **Tailwind CSS**. This application allows users to connect various ad accounts (Google Ads, Facebook Ads, etc.) and view aggregated performance metrics and leads in a single interface.

## 🚀 Features

-   **Multi-Platform Integration**: Connect and manage ad accounts from Google, Facebook, LinkedIn, and more.
-   **Unified Dashboard**: View key metrics (Impressions, Clicks, Conversions, Spend) across all connected accounts.
-   **Leads Management**:
    -   Aggregated leads view from all platforms.
    -   Detailed lead information (Name, Contact, Campaign, Status).
    -   **Paginated** list of recent leads for easy navigation.
    -   Visual highlighting of leads belonging to the currently selected account.
-   **Interactive Charts**: Visualize performance trends over time using Chart.js.
-   **Mock Data Service**: Built-in service to generate realistic mock data for development and testing.

## 🛠️ Tech Stack

-   **Backend**: Laravel 11 (PHP)
-   **Frontend**: Vue.js 3, Inertia.js
-   **Styling**: Tailwind CSS, Heroicons
-   **Database**: SQLite (Default) / MySQL
-   **Charts**: Chart.js / vue-chartjs

## 📦 Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/marketing-analytics.git
    cd marketing-analytics
    ```

2.  **Install PHP dependencies**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Database Setup**
    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

6.  **Run the Application**
    Start the development servers:
    ```bash
    npm run dev
    ```
    and
    ```bash
    php artisan serve
    ```

## 🧪 Testing

To generate random test accounts and leads:
```bash
php artisan migrate:fresh --seed
```

## 🔐 Default Credentials (Seeder)

If you ran the seeder, you can log in with:
- **Email**: `admin@example.com`
- **Password**: `password`

## Screenshots

![Dashboard Cover](./dashboard%20-%20cover.png)

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
