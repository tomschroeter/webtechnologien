# Web-Technologien App

Gruppe 2: Tom Schröter, Kian van der Meer, Tim Fuchs, Carlos Slaiwa, Arne Gutschick

This application is developed as part of the "Web-Technologien" course at TH Wildau. It is a PHP-based web platform designed to showcase art as part of an art gallery, featuring dedicated pages for artworks, artists, genres, and subjects. Registered users can review various paintings, and add their favorite artworks and artists to personalized lists.

You can find detailed documentation of our progress, software architecture, and more in our [Documentation](Dokumentation%20Art%20Gallery%20-%20Gruppe%202.pdf).

## Setup

Make sure you have XAMPP installed and have added the art DB that was provided.
Create an `.env` file and copy the structure of the `.env.template`.

1. Clone the GitHub Project into (some_folder)/XAMPP/xamppfiles/htdocs
2. Rename the folder from 'webtechnologien' to 'src'
3. Open the 'httpd.conf' config file in XAMPP and change the document root and directory to '\<YOUR PATH\>/htdocs/src'
3. Copy the images folder into the assets folder (src/assets/images)
4. Run the Apache Web Server
5. Run the MySQL Database
6. Got to the browser on http://localhost to see the main page