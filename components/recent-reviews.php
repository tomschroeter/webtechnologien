<div class="card-deck">
  <?php
  $query = "
    SELECT *, a.Title as title, a.ArtWorkId AS artwork_id, c.FirstName AS reviewer_first_name, c.LastName AS reviewer_last_name
    FROM reviews r
    JOIN artworks a ON a.ArtWorkId = r.ArtWorkId
    JOIN customers c ON c.CustomerID = r.CustomerId
    ORDER BY r.ReviewDate DESC
    LIMIT 3
";

  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $formattedDate = date("F j, Y", strtotime($row["ReviewDate"]));
      $artworkRoute = route("subjects", ["id" => $row["artwork_id"]]);

      echo "
      <div class=\"card\" onclick=\" window.location.href='$artworkRoute'; \" style=\"cursor: pointer;\">
        <div class=\"card-body\">
          <h4 class=\"card-title\">$row[title]</h4>
          <h6 class=\"card-subtitle mb-2 text-muted\">
            $formattedDate by $row[reviewer_last_name], $row[reviewer_first_name]
          </h6>
          <div class=\"w-100\">
            <p class=\"card-text\">
              Rated this artwork: $row[Rating]/10
            </p>
          </div>
        </div>
      </div>
      ";
    }
  } else {
    echo "No recent reviews found.";
  }
  ?>
</div>