<!DOCTYPE html>
<html lang="en">

<head>
    <title>Error</title>
    <meta charset="UTF-8">
</head>

<body class="container mt-5">
    <h2>Error</h2>
    <p>
        <?php
        $error = $_GET['error'] ?? '';

        switch ($error) {
            case 'tooShort':
                echo "Error 400: Please enter at least 3 characters in the search bar.";
                break;
            case 'invalidParam':
                echo "Error 400: The passed parameter is invalid.";
                break;
            case 'missingParam':
                echo "Error 400: A required parameter is missing.";
                break;
            case 'invalidID':
                $type = $_GET['type'] ?? '';
                if ($type === 'subject') {
                    echo "Error 404: No subject with the given ID was found.";
                } elseif ($type === 'artist') {
                    echo "Error 404: No artist with the given ID was found.";
                } elseif ($type === 'genre') {
                    echo "Error 404: No genre with the given ID was found.";
                } else {
                    echo "Error 404: No entry with the given ID was found.";
                }
                break;
            case 'notLoggedIn':
                echo "Error 401: You must be logged in to perform this action.";
                break;
            case 'unauthorized':
                echo "Error 403: You are not authorized to perform this action.";
                break;
            case 'missingReviewData':
                echo "Error 400: Required review data is missing.";
                break;
            case 'invalidReviewData':
                echo "Error 400: Invalid review data submitted.";
                break;
            case 'duplicateReview':
                echo "Error 409: You have already reviewed this artwork.";
                break;
            default:
                echo "An unknown error occurred.";
        }
        ?>
    </p>
</body>

</html>