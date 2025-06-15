<?php
function renderStars($rating): string
{
    $stars = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    if ($halfStar) {
        $stars .= '⯪';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }

    return $stars;
}
?>