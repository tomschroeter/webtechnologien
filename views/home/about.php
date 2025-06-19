<div class="mt-4 mb-5">
    <h1>About Us</h1>
    <br>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 card-title">Our Manifesto</h2>
            <p class="card-text">
                We are a group of students creating their very first web application. This website is a hypothetical
                project developed as part of an assignment to learn the fundamentals of web development.
            </p>
            <p class="card-text">
                We want to discover new things together, experiment, and have fun during development. This site is
                proof of our learning success and good teamwork.
            </p>
            <p class="card-text">
                Please note: This website is for educational purposes only and does not belong to any real company.
            </p>
        </div>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 card-title">Imprint</h2>
            <p class="mb-1"><strong>Owner:</strong> Programmers Having Pizza (PHP) GmbH</p>
            <p class="mb-1"><strong>Company Address:</strong> Hochschulring 1, 15745 Wildau</p>
            <p class="mb-1"><strong>Email:</strong> <a href="mailto:artists@php.dev">artists@php.dev</a></p>
            <p class="mb-1"><strong>Phone:</strong> +49 123 4567890</p>
            <p class="mb-0"><strong>Management:</strong> Arne Gutschick, Tim Fuchs, Kian van der Meer, Carlos
                Slaiwa, Tom Schröter</p>
        </div>
    </div>
    <div class="card-body">
        <?php
        require_once dirname(dirname(__DIR__)) . "/components/contributor-card.php";
        require_once dirname(dirname(__DIR__)) . "/components/contributor-list.php";
        ?>
        <div class="d-flex flex-wrap justify-content-center">
            <?php
            foreach ($contributors as $contributor):
                $name = $contributor['name'];
                $githubUsername = $contributor['githubUsername'];
                $githubUrl = $contributor['githubUrl'];
                $profilePicture = $contributor['profilePicture'];
                $tickets = $contributor['tickets'];
                renderContributorCard($name, $githubUsername, $githubUrl, $profilePicture, $tickets);
            endforeach;
            ?>
        </div>
    </div>
</div>