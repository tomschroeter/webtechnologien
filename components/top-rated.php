<div>
  <div class="card-columns">
    <?php
    $worksDir = 'assets/images/works/medium';

    $query = "
    SELECT a.Title AS artwork_name, ar.FirstName AS artist_first, ar.LastName AS artist_last, AVG(r.Rating) AS avg_rating, a.ImageFileName AS imgFile
    FROM reviews r
    JOIN artworks a ON r.ArtWorkId = a.ArtWorkId
    JOIN artists ar ON a.ArtistId = ar.ArtistId
    GROUP BY a.ArtWorkId, a.Title, ar.FirstName
    ORDER BY avg_rating DESC
    LIMIT 3
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $artworkName = $row["artwork_name"];
        $artworkArtist = $row["artist_first"] . " " . $row["artist_last"];
        $rating = round($row["avg_rating"], 0);

        // Get the correct filename and check if it exists
        // If file does not exist, use a placeholder image (001010.jpg for example)
        $fileName = $row["imgFile"] . ".jpg";
        $imgPath = "$worksDir/$fileName";
        $placeholderImg = "$worksDir/001010.jpg";

        $imgToUse = file_exists($imgPath) ? $imgPath : $placeholderImg;

        // Note: Wrong filenames: 12030; Balcony by Edouard Manet, 01290; The Dream by Pablo Picasso
        // And many more
        // echo $fileName;
        echo "
          <div class=\"card\">
            <img class=\"card-img-top\" src=\"$imgToUse\" alt=\"\">
            <div class=\"card-body\">
              <h4 class=\"card-title\">$artworkName</h4>
              <div class=\"w-100\">
              <p class=\"card-text d-flex\">
                $artworkArtist
                <span class=\"ml-auto font-weight-bold\">$rating/10</span>
              </p>
              </div>
            </div>
          </div>
          ";
      }
    } else {
      echo "
        <div>No results found</div>
      ";
    }
    ?>
  </div>
</div>