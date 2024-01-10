# SIMPLE-PARKING-API

## Setup Instructions

Clone the repository.
```bash
git clone https://github.com/pasanks/simple-parking-api.git
```

Switch to the project folder

```bash
cd simple-parking-api
```

Install composer dependencies.

```bash
composer install
```

Copy the example env file and make the required configuration changes in the .env file

```bash
cp .env.example .env
```

Generate a new application key
```bash
php artisan key:generate
```
Run the database migrations

**Set the database connection in .env before migrating

```bash
php artisan migrate
```

Start the local development server

```bash
php artisan serve
```
The server will typically start on http://localhost:8000.

You can run the test suit by running

```bash
php artisan test
```

## Assumption on Date and Time

For simplicity and uniformity, it is assumed that the start date and end date in the system always refer to the entire day. The time portion is set to start at 00:00:00 for the start date and end at 23:59:59 for the end date.


### Based on the requirements 
- I created a simple pricing logic which can be found in the BookingService.php
- For the API only dates are accepted (didn't consider time) - acceppted format YYYY-MM-DD (2024-01-02).

- I created some basic tests for the two main controller and service didn't spend much time on tests.
- Created a GitHub action workflow to run CodeSniffer to identify any code styling issues.
- User authentication has not been implemented in this version(planning to use laravel passport). The decision was made due to time constraints, with a focus on delivering the core functionality and logic of the system.

### Error Handling

Error handling in this project relies on Laravel's inbuilt error handler, customized to provide a proper response format. The error responses are designed to be informative and follow a consistent structure, enhancing the overall user experience(dev purpose only).
