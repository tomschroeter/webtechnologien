<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Dynamic page title based on controller-provided $title variable -->
  <title>
    <?php
    /**
     * Outputs the page title.
     * Falls back to "Art Gallery" if no custom $title is provided.
     *
     * @var string|null $title Optional variable set by the controller
     */
    if (isset($title) && !empty($title)) {
      echo htmlspecialchars($title);
    } else {
      echo "Art Gallery";
    }
    ?>
  </title>

  <!-- Link to Bootstrap 5.3.7 CSS for layout and components -->
  <link rel="stylesheet" href="/assets/bootstrap-5.3.7-dist/css/bootstrap.min.css">

  <!-- Custom site-wide styles -->
  <link rel="stylesheet" href="/assets/style.css">

  <!-- Favicon (SVG logo) shown in browser tab -->
  <link rel="icon" type="image/svg+xml" href="/assets/svgs/logo.svg">
</head>