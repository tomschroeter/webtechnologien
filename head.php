<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php
    $uri = $_SERVER['REQUEST_URI'];
    $segments = explode('/', trim($uri, '/'));
    $lastSegment = $segments[0] ?? '';

    // Fallback for root
    if (empty($lastSegment)) {
        echo 'Home';
    } else {
        // Capitalize and handle dash-separated segments if needed
        echo ucfirst($lastSegment);
    }
    ?>
  </title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="icon" type="image/svg+xml" href="assets/svgs/logo.svg">
</head>