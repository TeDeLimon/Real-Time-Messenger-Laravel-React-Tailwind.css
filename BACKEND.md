# Create a Laravel Proyect

Execute the next command:
```bash
composer create-project laravel/laravel 'proyect-name'
```

#### After that is moment to connect the database, we must modify the .env file:

Discomment this variables and modify to setup your configuration.
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_react_messenger
DB_USERNAME=root
DB_PASSWORD=root

#### To create a database that doesn't exists use the next command:
```bash
php artisan migrate
```

#### To start the server use the next command:
```bash
php artisan serve
```

#### For authentication we are can use Lav Breeze witch is a starter package and profile management
```bash
composer create-project laravel/breeze --dev
```

#### Now to install the breeze component:
```bash
php artisan breeze:install
```

#### Then select the React with Inertia
```bash
react
```
#### As optionales features select:
```bash
dark
```

#### Which testing framework we will prefer:
```bash
pest (0)
```

#### Now, we are gonna install a couple of new dependencies
- https://headlessui.com/
- https://heroicons.com
- https://daisyui.com/
- https://www.npmjs.com/package/emoji-picker-react
- https://github.com/remarkjs/react-markdown
- https://github.com/ramsey/uuid

```bash
npm install @headlessui/react
npm install @heroicons/react
npm install daisyui
npm install emoji-picker-react
npm install react-markdown
npm install uui
```

#### Now it's turn to install broadcasting what will install laravel reverb.
When a client connects to a socket server it will recieve realtime updates regarding certain things.
```bash
php artisan:install broadcasting
```
#### We are gonna use broadcasting so enter yes and after that yes to install Node dependencies 
```bash
yes
```

#### Let's start create the database (-m indicates that migration file will be installed as well)
```bash
php artisan make:model `name_of_table` -m
```

#### Migration's files are in folder database/migrations
Here we can add more fields and add extra logic to our app

#### Next step is work on the Models, create the relationships, etc.

#### It's time to generate the factories: one will be Group Factory and Message Factory
```bash
php artisan make:factory `nameFactory`
```
#### Once the factories are prepared and the DatabaseSeeder is configured, it's time to execute it
```bash
php artisan migrate:fresh --seed
```

