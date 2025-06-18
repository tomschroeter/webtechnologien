<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php
    // Use title from controller data
    if (isset($title) && !empty($title)) {
      echo htmlspecialchars($title);
    } else {
      echo "Art Gallery";
    }
    ?>
  </title>
  <link rel="stylesheet" href="/assets/bootstrap-4.1.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="icon" type="image/svg+xml" href="/assets/svgs/logo.svg">
</head>