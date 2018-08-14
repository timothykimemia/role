### Laravel RolePermission Boilerplate

#### For developers who want to implement Role and Permission based methods on their application without using package vendors

This code structure is derived from [spatie/laravel-permission](https://github.com/spatie/laravel-permission) using Role based system and Permission control within the Laravel framework.

##### Steps

- Folder structure is of own choice.

- Create `Role.php`, `Permission.php`, Models and include `create_users_roles_table`, `create_users_permissions_table` and `create_roles_permissions_table` migrations to join relevant models.

- (Shortcut) Create `UsersRole.php`, `UsersPermission.php`, `RolesPermission.php` models then delete rather than ``php artisan make:migration``

```bash
    php artisan make:model Models\Role\Role -m
    php artisan make:model Models\Role\Permission -m
    php artisan make:model Models\Role\UsersRole -m
    php artisan make:model Models\Role\UsersPermission -m
    php artisan make:model Models\Role\RolesPermission -m

``` 
- If you want to separate your tables into different databases, check [DataSet Configuration](https://github.com/timothykimemia/dataset). Remember to add `Schema::connection('')` on your migrations.

- Add columns to the tables respectively.

- Create Seeder tables to populate tables with some existing data to use with your Role base system and its Permissions.

- This is because your application will start searching for existing definitions once you're done building your code.

```bash
    php artisan make:seeder Role\RolesTableSeeder
    php artisan make:seeder Role\PermisionsTableSeeder
```
- Add the table seeders on `DatabaseSeeder.php`.

- Migrate the table seeders to populate with your data definitions.

```bash
    php artisan db:seed
```
- Add the relations between the Models.

- Remember to add `protected $connection = 'role'` if you have separated the database tables. If not created [DataSet Configuration](https://github.com/timothykimemia/dataset), ignore this step.

- If working with [DataSet Configuration](https://github.com/timothykimemia/dataset), Remember to add where table exist on database with your relations.

```php
    public function roles()
    {
        return $this->belongsToMany(Role::class, '*_role.users_roles');
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, '*_role.users_permissions');
    }
```

- The code structure under `User.php` Model is what defines and sets restriction methods to your Role based application (check DocBlocs).

###### Middleware Restriction

- Create Middleware restriction that handles application requests.

```bash
    php artisan make:middleware Role\RolePermissionsMiddleware
```
- We can also use Laravel's predefined `can` Auth middleware.

```php
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if (!$request->user()->hasRole($role)):
            return back()->with('warning', "You don't have Access.");
        endif;

        if ($permission !== null && !$request->user()->can($permission)):
            return back()->with('warning', "You don't have Permission on this Action.");
        endif;

        return $next($request);
    }
```
- Set `'role'` on `Middleware\Kernel.php` under `protected $routeMiddleware = []`.

```php
    'role' => \App\Http\Middleware\Role\RolePermissionsMiddleware::class,
```
- Now you can Apply on your route middleware eg `'middleware' => ['auth', 'role:admin,delete']`.

###### Application Restriction using App Container (ServiceProvider)

- Create `RolePermissionServiceProvider.php` to map out all permissions and set Role based restrictions on your application. Ideal for middle or huge system project on restricting even read and write features

```php
    public function boot()
    {
        Permission::get()->map(function ($permission) {
            Gate::define($permission->name, function ($user) use ($permission) {
                return $user->hasPermissionTo($permission);
            });
        });
    }
```
- Add this provider on `config\app.php` under `'providers' = []`

```php
    App\Providers\Role\RolePermissionServiceProvider::class,
```
- Remember to comment if Permission table is null or database not populated (not found) because Laravel framework is trying to look for these Set or Defined Permissions (Remember Seeding our tables!).

- This code structure is Powerful to even restrict `php artisan` scripts. Will throw `Access Denied` exception if Permission table is null or not found.

###### Blade Templating

If you wish to use role restrictions on your views, this is what you do:

- Create a `BladeServiceProvider.php` to add Laravel `Blade::directive('')`. Use this provider to add other directives for your application project.

```bash
    php artisan make:provider View\BladeServiceProvider
```
- Define `Blade::directive('role')` and ensure to end your directive or will not work (`Blade::directive('endrole')`).

```php
    public function boot()
    {
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
    }
```
- Add this provider to `'providers' => []`

```php
    App\Providers\View\BladeServiceProvider::class,
```

- Now you can use `@role('admin')` && `@endrole` without defining on middleware (for security purposes, still define your middleware)

- This is replacement for `@if(auth()->user()->hasRole('admin'))` component. You can still use `@if` template.

###### Registration, Login and ResetPassword

- Remember to Add `attach()` on Auth Controllers so that every user starts with the lowest tier Role: `'user'`.

- Add `Role::where('name', 'user')->first()` to these controllers to first search for Role 'user'.

- Attach this role to registered, logged in or reset password user so that they may not bypass the Role based system. 

- Recommended to use `syncWithoutDetaching()`, this restricts multiple attaching the role, and also it won't break if user is assigned `'admin'` Role by attaching `'user'` Role again.

```php
    $role = Role::where('name', 'user')->first();
    $user->roles()->syncWithoutDetaching($role);
```

And you're done.

#### Contributing

Feel free to Contribute more If you working on such environments.

#### Security Vulnerabilities

If you discover a security vulnerability within Laravel and such conditions, please send an e-mail to Laravel team [taylor@laravel.com](mailto:taylor@laravel.com).

#### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).