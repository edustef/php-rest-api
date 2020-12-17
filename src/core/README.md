# php-mvc-framework

This is a mini-framework inspired by modern php frameworks like Laravel.
It is NOT made for production. This was only created for learning purposes and it is probablly full of bugs and missing functionalities so please do not use it.

The following instructions are a note for myself on how to quickly use it.

## Requisites

### With docker

You can create a `docker-compose.yml` in root folder and call composer-up. You can change mount location for mysql however you want but the '/app' should be tied to '.' so it mounts the root folder. 

```yml
version: "3.8"

services:
  web:
    image: mattrayner/lamp:latest
    ports: 
      - 80:80
    volumes:
      - .:/app
      - /mnt/c/mysql:/var/lib/mysql
    
```

### OR

Make sure you have the following:
 - PHP >= 7.4
 - Composer 
 - `vlucas/phpdotenv`(recommended) or any package that allows you to read .env files

## Installation

Run this command in your project:

`composer require edustef/php-mvc-framework vlucas/phpdotenv`

## Configuration

### ENV Variables
Make sure to create an .env file and follow this example
```
DB_DSN=mysql:host=localhost;port=3306;dbname=mvc_framework
DB_USER=root
DB_PASSWORD=password
```

If you're using the `vlucas/phpdotenv` make sure to include this in the `public/index.php`

```php
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
```

### .httaccess

Make sure you have an .httaccess in your root folder and paste the following.
```
Options +FollowSymLinks -Indexes
RewriteEngine On

RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

RewriteCond %{REQUEST_URI} !public/
RewriteRule (.*) /public/$1 [L]

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule ^(.*)$ index.php [L,QSA
```

No idea how this works exactly but it is a modification that was provided from Laravel documentation but changed so it `index.php` is in public. 
This way you can also add css and javascript inside the public folder so that the links now are references from the public folder. 

In conclusion you can use: `href="css/\*.css` or `src=scripts/\*.js` where folder exists as: `public/css/` and `public/scripts`.

#### autoloader 

In composer.json you can specificy the autoloader like this. "app\\" is the namespace of the application and "./src" is the namespace root folder. So for this example my application lives in a src folder but you can also create it anywhere else.

```json
"autoload": {
  "psr-4": {
    "app\\": "./src"
  }
}
```

### public

Application runs from the `public/index.php`. In order to get started we can look at this basic starting template

```php
// include the autloader from the /vendor/autoload.php;
require_once __DIR__ . '/../vendor/autoload.php';

// Make sure to include all classes from their coresponding namespaces
use edustef\mvcFrame\Application;
use app\controllers\SiteController;
use app\models\User;

// The configuration for env variables using the vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// This is the config that will be passed to the Application constructor
// db array is required*
// 
// The userClass is optional, as not all application may require a user
// If you want to have a user however make sure to have a class model created which extends DatabaseModel. 
// In this case the User is that class model but you may name it however you want
$config = [
  'userClass' => User::class,
  'db' => [
    'dsn' => $_ENV['DB_DSN'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD']
  ]
];

// This application will only be created once
// However the Application registers itself as a static constant
// which can be used to access any functionalities from anywhere. 
// For example if in your Controller class you want to redirect you can call
// Application::$app->response->redirect('/');
$app = new Application(dirname(__DIR__) . '/src/', $config);

// This is how you can add routes. 
// The first argument is the path and the second is an array with 
//    1st element the className of the Controller. 
//    2nd element is the name of the method that will be called from the Controller.
$app->router->get('/', [SiteController::class, 'home']);

// If you have a simple page that doesn't need a controller just call the path and the name of the view.
$app->router->get('/contact', 'contact');

// Or you can even provide your own callback like this
$app->router->post('/contact', function(Request $request, Response $response) {
  echo '<h1>Hello World</h1>';
});
```
