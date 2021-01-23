# snowtricks

EN - Projet n°6 created for OpenClassrooms and Backend path.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/eabfb573ef4a4261903e8dd8b9b26061)](https://app.codacy.com/manual/riwalenn/snowtricks?utm_source=github.com&utm_medium=referral&utm_content=riwalenn/snowtricks&utm_campaign=Badge_Grade_Dashboard)

## Built With

* [PHP 7.4.9]
* [Symfony 5.1]
* [Bootstrap 4.5.2]
* [Squadfree](https://bootstrapmade.com/demo/Squadfree/) - template

## Download and Installation
You need a web development environment like WampServer (for Windows), MAMP (for Mac) or LAMP (for Linux).

- Clone the project code : "git clone https://github.com/riwalenn/snowtricks.git"
- Go to the console and write "composer install" where you want to have the project
- Open the .env file and change the database connection values on line 32 like "DATABASE_URL=mysql://root:@127.0.0.1:3306/oc_projets_n6?serverVersion=5.7.19" for me.
- Return to the console and write "php bin/console doctrine:database:create"
- "php bin/console doctrine:migrations:migrate"
- To have some initial dataset : "php bin/console doctrine:fixtures:load"
- Run the application with "php -S localhost:8000 -t public"
- If you want an admin account go to your database then write this sql request : "INSERT INTO `user` (`id`, `username`, `email`, `password`, `image`, `token`, `created_at`, `is_active`, `roles`) VALUES (99, 'Admin', 'admin@gmail.com', '$2y$13$eEY38DakHa/VwoHNx/xlHu.PlViXkvnGEH0lLXtc2QmxFDhBBC6li', '5', NULL, NULL, 1, '[\"ROLE_ADMIN\"]')"
- Then go to the login page (Connexion) :
    - admin@gmail.com (as email)
    - testtest (as password)

## Preview
**[![Squad-free](https://bootstrapmade.com/wp-content/themefiles/Squadfree/800.png)](https://bootstrapmade.com/demo/Squadfree/)**

## Status
[BootstrapMade license](https://bootstrapmade.com/license/)

## Copyright and License
Copyright Squadfree. All Rights Reserved - Designed by [BootstrapMade](https://bootstrapmade.com)

## Author
* **Riwalenn Bas** - *Blog* - [Riwalenn Bas](https://www.riwalennbas.com)
* **Riwalenn Bas** - *Repositories* - [Github](https://github.com/riwalenn?tab=repositories)
