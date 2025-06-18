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
  <link rel="stylesheet" href="/assets/bootstrap-4.1.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="icon" type="image/svg+xml" href="/assets/svgs/logo.svg">
</head>