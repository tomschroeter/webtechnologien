<?php
/**
 * Renders a star rating as HTML stars with full, half, and empty stars.
 *
 * @param float $rating The rating value (e.g., 3.5)
 * @return string HTML markup with styled stars
 */
function renderStars($rating): string
{
    // Get the number of full stars (integer part of rating)
    $fullStars = floor($rating);

    // Determine if there should be a half star (if fractional part >= 0.5)
    $halfStar = ($rating - $fullStars) >= 0.5;

    // Calculate the number of empty stars to fill to 5 stars total
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    $stars = '<span role="img" aria-label="Rating: ' . number_format($rating, 1) . ' out of 5 stars" ' .
        'style="font-size: 0.9rem; vertical-align: 1px; user-select: none;">';

    // Render full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<span style="color: gold;">★</span>';
    }

    // Render half star if needed
    if ($halfStar) {
        $stars .= '<span style="color: gold; position: relative; display: inline-block; width: 1em;">
            <span style="position: absolute; overflow: hidden; width: 0.5em;">★</span> 
            <span style="color: #e4e5e9;">★</span>
        </span>';
    }

    // Render empty stars in gray color to complete 5 stars total
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<span style="color: #e4e5e9;">★</span>';
    }

    $stars .= '</span>';

    return $stars;
}
?>