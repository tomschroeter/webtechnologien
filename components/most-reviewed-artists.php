<div class="card-deck">
  <?php
  $query = "
    SELECT ar.FirstName AS artist_first, ar.LastName AS artist_last, COUNT(r.ReviewId) AS total_reviews
    FROM reviews r
    JOIN artworks a ON a.ArtworkId = r.ArtWorkId
    JOIN artists ar ON a.ArtistId = ar.ArtistId
    GROUP BY ar.FirstName
    ORDER BY total_reviews DESC
    LIMIT 3
    ";

  $result = $conn->query($query);

  $rowIndex = 1;
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $artist = $row["artist_first"] . " " . $row["artist_last"];
      $total_reviews = $row["total_reviews"];

      // Display each artist in a card without an image
  
      /* 
      Display example:
        $rowIndex. $artist -> 1. Pablo Picasso
        $total_reviews Reviews -> 11 Reviews 
      */
      echo "
      <div class=\"card\">
        <div class=\"card-body\">
          <h4 class=\"card-title\">$rowIndex. $artist</h4>
          <div class=\"w-100\">
            <p class=\"card-text\">
              $total_reviews Reviews
            </p>
          </div>
        </div>
      </div>
      ";
      $rowIndex++;
    }
  } else {
    echo "<div>No results found</div>";
  }
  ?>
</div>