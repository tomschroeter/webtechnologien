# Web-Technologien App
Gruppe 2: Tom Schröter, Kian van der Meer, Tim Fuchs, Carlos Slaiwa, Arne Gutschik

This app is being created as part of our course "Web-Technologien" at the TH Wildau.
It is a PHP web app that is an art showcase site, including pages for artists, genres & subjects. Users can register/login and rate different paintings and favorite them as well as artists.

## Setup
Make sure you have XAMPP installed and have added the art DB that was provided.
Create an `.env` file and copy the structure of the `.env.template`.

1. Clone the GitHub Project into (some_folder)/XAMPP/xamppfiles/htdocs
2. Copy the images folder into the assets folder (webtechnologien/assets/images)
3. Run the Apache Web Server
4. Run the MySQL Database
5. Got to the browser on http://localhost/webtechnologien to see the main page

## Routing
This app uses filebased routing by default. Routes are therefore based on the folder structure, e.g. `app/index.php` -> http://localhost/app & `app/about/index.php` -> http://localhost/app/about.

If you want to link to another page you can do the following:
- `href="about"` -> http://localhost/app/about
- `href="/about"` -> http://localhost/about (avoid this)
- `href=""` -> http://localhost/app, returns to main page
- `href="about/another_page"` -> use this to go to more nested routes

### Dynamic Routing
> Make sure you have included `navbar.php` or `router.php` directly!
> For example usage see `navbar.php`

You can use:
- `href=<?php echo route("your_route", ["param" => some_id]) ?>`

When setting routes in `routes.php` make sure that:
- Routes make sense
- You give easy to remember route names
- Use optional params by adding `/:your_param` (: -> optional)
- Or use required params by adding `/{your_param}` ({} -> required)

# Unsere Termine
### Erster Pflicht-Besprechungstermin
📅 07.05.2025 </br>
🕞 15:25 Uhr
- [ ] done?

### Zweiter Pflicht-Besprechungstermin
📅 04.06.2025 </br>
🕔 17:05 Uhr
- [ ] done?

### Abgabe des Belegs/Source-Codes
📅 20.06.2025 </br>
🕚 23:00 Uhr
- [ ] done?

### Funktionsdemonstration
📅 25.06.2025 </br>
🕛 12:05 Uhr
- [ ] done?
