<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php
    $uri = $_SERVER['REQUEST_URI'];
    $lastSegment = explode('/', trim($uri, '/'))[0];
    $lastSegmentWithoutParams = explode('?', $lastSegment)[0];

    // Fallback for root
    if (empty($lastSegmentWithoutParams)) {
      echo 'Home';
    } else {
      // Replace dashes with spaces, then capitalize
      $title = str_replace('-', ' ', $lastSegmentWithoutParams);
      echo ucwords($title);
    }
    ?>
  </title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Lato&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="icon" type="image/svg+xml" href="/assets/svgs/logo.svg">
</head>