<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php
    $requestUri = trim($_SERVER['REQUEST_URI'], '/');
    $baseDir = 'webtechnologien';
    if (strpos($requestUri, $baseDir) === 0) {
      $requestUri = substr($requestUri, strlen($baseDir));
    }
    $segments = explode('/', $requestUri);
    // Work on page names
    $page = ucfirst($segments[1]) ?? "Home";
    echo $page;
    ?>
  </title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/style.css">
</head>