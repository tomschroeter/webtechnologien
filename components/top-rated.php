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
        $fileName = $row["imgFile"] . ".jpg";

        // Note: Wrong filenames: 12030; Balcony by Edouard Manet, 01290; The Dream by Pablo Picasso
        // And many more
        // echo $fileName;
        // TODO: work on rating position
        echo "
          <div class=\"card\">
            <img class=\"card-img-top\" src=\"$worksDir/$fileName\" alt=\"\">
            <div class=\"card-body\">
              <h4 class=\"card-title\">$artworkName</h4>
              <div class=\"d-flex w-100\">
              <p class=\"card-text\">
                $artworkArtist
                <span class=\"ml-auto text-danger font-weight-bold\">$rating/10</span>
              </p>
              </div>
            </div>
          </div>
          ";
      }
    } else {
      echo "No results found.";
    }
    ?>
  </div>
</div>