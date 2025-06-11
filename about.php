<!DOCTYPE html>
<html lang="en">

<?php
  require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/navbar.php";
?>

<body class="container">
    <div class="mt-4 mb-5">
        <h1>Über uns</h1>
        <hr>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 card-title">Unser Manifest</h2>
                <p class="card-text">
                    Wir sind eine Gruppe von Studierenden, die ihre allererste Webanwendung erstellt. Diese Website ist ein hypothetisches Projekt, das im Rahmen einer Belegaufgabe entwickelt wurde, um die Grundlagen der Webentwicklung zu erlernen.
                </p>
                <p class="card-text">
                    Wir wollen gemeinsam Neues zu entdecken, experimentieren und Spaß bei der Entwicklung haben. Diese Seite ist ein Beweis für unseren Lernerfolg und die gute Teamarbeit.
                </p>
                <p class="card-text">
                    Bitte beachten Sie: Diese Website ist dient ausschließlich zu Ausbildungszwecken und gehört keinem echten Unternehmen.
                </p>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4 card-title">Impressum</h2>
                <p class="mb-1"><strong>Inhaber:</strong> Programmers Having Pizza (PHP) GmbH</p>
                <p class="mb-1"><strong>Firmensitz:</strong> Hochschulring 1, 15745 Wildau</p>
                <p class="mb-1"><strong>E-Mail:</strong> <a href="mailto:artists@php.dev">artists@php.dev</a></p>
                <p class="mb-1"><strong>Telefon:</strong> +49 123 4567890</p>
                <p class="mb-0"><strong>Management:</strong> Arne Gutschick, Tim Fuchs, Kian van der Meer, Carlos Slaiwa, Tom Schröter</p>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
