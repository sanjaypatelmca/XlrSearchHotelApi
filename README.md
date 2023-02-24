note: this package run only in laravel

    How To Install 

        composer require xlr8rms/hotelsearchapi

            require packages
                guzzlehttp/guzzle: 7.5      
                laravel/framework: 8.75

    Usage
        
        1. in your folder config/app.php add this serviceProvider  Xlr8rms\Hotelsearchapi\HotelServiceProvider::class,
        2. run command php artisan serve
        3. http://127.0.0.1:8000/searchhotellist