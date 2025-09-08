<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost/philsagapayroll/'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Manila',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

    
    //Company Information
    'CompanyName' => 'PHILSAGA Mining Corp.',
    'ShortCompanyName' => 'PWC',
    'CompanyEmail'=>'',
    'CompanyTelNo' => '',
    'CompanyMobileNo' => '',
    'CompanyAddress' => '',
    'CompanyShortAddress' => '',

    'DEFAULT_SYSTEM_SETTING' => '1',

    //Site is On Debug Mode
    'DebugMode' => '0',

    //Limit of Record Shown on the List
    'ListRowLimit' => '50',
      
      //CUT OFF TYPE
     'PERIOD_1ST_HALF' => '1ST HALF',
     'PERIOD_2ND_HALF' => '2ND HALF',
     'PERIOD_EVERY_CUTOFF' => 'EVERY CUTOFF',

     'PERIOD_1ST_HALF_ID' => '1',
     'PERIOD_2ND_HALF_ID' => '2',
     'PERIOD_EVERY_CUTOFF_ID' => '3',

     //RELEASING TYPE
     'RELEASE_1ST_HALF' => '1ST HALF',
     'RELEASE_2ND_HALF' => '2ND HALF',
     'RELEASE_EVERY_CUTOFF' => 'EVERY CUTOFF',
     'RELEASE_EVERY_MONTH' => 'EVERY MONTH',
     'RELEASE_EVERY_TWO_MONTHS' => 'EVERY TWO MONTHS',
     'RELEASE_ONE_TIME' => 'ONE TIME',

     'RELEASE_1ST_HALF_ID' => '1',
     'RELEASE_2ND_HALF_ID' => '2',
     'RELEASE_EVERY_CUTOFF_ID' => '3',
     'RELEASE_EVERY_MONTH_ID' => '4',
     'RELEASE_EVERY_TWO_MONTHS_ID' => '5',
     'RELEASE_ONE_TIME_ID' => '6',

    //TYPE OF STATUS
    'STATUS_ACTIVE' => 'Active',
    'STATUS_INACTIVE' => 'Inactive',
    'STATUS_BLOCKED' => 'Blocked',
    'STATUS_ALL' => 'All',
    
    //OTHER STATUS
    'STATUS_OPEN' => 'Open',
    'STATUS_CLOSE' => 'Close',
    
    'STATUS_POSTED' => 'Posted',
    'STATUS_APPROVED' => 'Approved',
    'STATUS_CANCELLED' => 'Cancelled',
    'STATUS_PENDING' => 'Pending',

    //Upload Image thumbnail
    'Thumbnail' => '300x300',
    'ThumbnailWidth' => '300',
    'ThumbnailHeight' => '300',

    //Payroll Type
    'PAYROLL_DAILY' => 'Daily',
    'PAYROLL_WEEKLY' => 'Weekly',
    'PAYROLL_SEMIMONTLY' => 'Semi-Montly',
    'PAYROLL_MONTHLY' => 'Monthly',

    //Payroll Generation Type
    'GENERATE_PAYROLL_BATCH' => 'Batch Payroll',
    'GENERATE_PAYROLL_FINAL' => 'Final Payroll',
    'GENERATE_PAYROLL_EMPLOYEE' => 'Employee Payroll',

    //13th Month Generation Type
    'GENERATE_13THMONTH_BATCH' => 'Batch',
    'GENERATE_13THMONTH_EMPLOYEE' => 'Employee',

    //Scheduled Jobs
    'BackUpKey' => 'G&(4#2%7*gQK3l!$}',
    'TextBlastKey' => '&9)@56DGWu1i<gqC3{P0}=',
    'EmailBlastKey' => '4%5r3@s33d*1Wg4LB{d0;%',

    //PLATFORM
    'PLATFORM_ADMIN' => 'Admin',
    'PLATFORM_WEBSITE' => 'Website',
    'PLATFORM_ANDROID' => 'Android',
    'PLATFORM_IOS' => 'iOS',
    
];
