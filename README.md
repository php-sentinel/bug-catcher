# Catch every bug in all your PHP applications in one place

<p align="center">
<img src="docs/logo/horizontal.svg" width="600"><br>
</p>
<img src="docs/bug_catcher_01.png" width="800" >
<img src="docs/stacktrace.png" width="800" >

> [!CAUTION]
> **Work in progress, use at your own risk**

## Installation

**Create blank symfony project**

```bash
composer create-project symfony/skeleton:"7.1.*" bug-catcher
```

**Add depenedencies**

```
//cmposer.json
...
    "repositories": [
        {
            "url": "https://github.com/tito10047/php-sparkline",
            "type": "vcs"
        }
    ]
```

```bash
composer require php-sentinel/bug-catcher:dev-main
composer require brendt/php-sparkline:dev-period2
````

**Enable bundle if not already enabled**
```php
//config/bundles.php
return [
    ...
    PhpSentinel\BugCatcher\BugCatcherBundle::class => ['all' => true],
    ...
];
```
## Configuration
**setup packages**

```yaml
#config/services.yaml
parameters:
    theme: default #can be : blue, green or red
```
```yaml
#config/packages/twig.yaml
twig:
    ...
    form_themes: [ '@EasyAdmin/symfony-form-themes/bootstrap_5_layout.html.twig' ]
    globals:
        theme: '%theme%'
        app_name: '%env(APP_NAME)%'
```
```yaml
#config/packages/twig_component.yaml
twig_component:
    ...
    defaults:
        ...
        PhpSentinel\BugCatcher\Twig\Components\: '@BugCatcher/components/'
```
```yaml
#config/packages/webpack_encore.yaml
webpack_encore:
    ...
    builds:
        bug_catcher: '%kernel.project_dir%/public/bundles/bugcatcher/'
framework:
    ...
    assets:
        packages:
            app:
                json_manifest_path: '%kernel.project_dir%/public/build/manifest.json'
            bug_catcher:
                json_manifest_path: '%kernel.project_dir%/public/bundles/bugcatcher/manifest.json'
```

**Security**

follow the instructions in the [Symfony docs](https://symfony.com/doc/current/security.html)
Modify these:
```yaml
#config/packages/security.yaml
security:
    ...
    providers:
        app_user_provider:
            entity:
                class: PhpSentinel\BugCatcher\Entity\User
                property: email
    firewalls:
        ...
        api:
            pattern: ^/api/
            stateless: true
        main:
            ...
            provider: app_user_provider
            form_login:
                login_path: bug_catcher.security.login
                check_path: bug_catcher.security.login
                enable_csrf: true
            logout:
                path: bug_catcher.security.logout
    access_control:
        - { path: ^/login$, role: PUBLIC_ACCESS }
        - { path: ^/api, roles: PUBLIC_ACCESS }
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/detail, roles: ROLE_DEVELOPER }
        - { path: ^/_components/LogList/clearAll, roles: ROLE_DEVELOPER }
        - { path: ^/_components/LogList/clearOne, roles: ROLE_DEVELOPER }
        - { path: ^/, roles: ROLE_CUSTOMER }
    role_hierarchy:
        ROLE_ADMIN: ROLE_DEVELOPER
        ROLE_DEVELOPER: ROLE_CUSTOMER
        ROLE_CUSTOMER: ROLE_USER
```
```yaml
#config/packages/doctrine.yaml
doctrine:
    orm:
        ...
        dql:
            string_functions:
                TYPE: PhpSentinel\BugCatcher\Extension\DQL\TypeFunction
```
```yaml
#config/packages/api_platform.yaml
api_platform:
    ...
    formats
        ...
        json: [ 'application/json' ]
```
**Routes**

```yaml
#config/routes/bug_catcher.yaml
_bug_catcher:
    resource: "@BugCatcherBundle/config/routes.php"
    prefix:   /
```

**Download icons**

```bash
php bin/console ux:icons:import pajamas:hamburger covid:virus-lab-research-magnifier-1 clarity:archive-line game-icons:magic-broom
```

## First Run
**Create database**

```dotenv
# .env.local
APP_ENV=dev
APP_NAME=BugCatcher
CLEAR_STACKTRACE_ON_FIXED=true
DATABASE_URL=mysql://user:password@localhost:3306/bug_catcher
```
    
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console app:create-user username password
```

**Start the built-in web server**

You can use Nginx or Apache, but the built-in web server works
great:

```
php bin/console server:run
```

Now check out the site at `http://localhost:8000`

**Setup cron for collection status codes**

```
# /etc/crontab
* * * * * www-data php /var/www/bug-catcher/bin/console app:ping-collector > /dev/null 2>&1
#optimize records by grouping them by 5 minutes older than 1 day
0 * * * * www-data php /var/www/bug-catcher/bin/console app:record-optimizer --past=1 --precision=5
#optimize records by grouping them by 60 minutes older than 7 days
0 0 * * * www-data php /var/www/bug-catcher/bin/console app:record-optimizer --past=7 --precision=60
```

## Enable Logging

**Setup your Symfony applications**

See package [php-sentinel/bug-catcher-reporter-bundle](https://github.com/php-sentinel/bug-catcher-reporter-bundle)

**Setup plain PHP applications**

See package [php-sentinel/bug-catcher-curl-reporter](https://github.com/php-sentinel/bug-catcher-curl-reporter)

Have fun!

## Modifications

See [docs/extending.md](docs/extending.md) for more information on how to extend the

## Have Ideas, Feedback or an Issue?

If you have suggestions or questions, please feel free to
open an issue on this repository.

