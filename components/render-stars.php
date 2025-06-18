<?php
function renderStars($rating): string
{
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    // Not using unicode half star (⯪), as half star isn't rendered on MacOs Chrome
    $stars = '<span style="font-size: 0.9rem; vertical-align: 1px; user-select: none;">';
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<span style="color: gold;">★</span>';
    }

    // Half star
    if ($halfStar) {
        $stars .= '<span style="color: gold; position: relative; display: inline-block; width: 1em;">
            <span style="position: absolute; overflow: hidden; width: 0.5em;">★</span>
            <span style="color: #e4e5e9">★</span>
        </span>';
    }

    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<span style="color: #e4e5e9;">★</span>';
    }

    $stars .= '</span>';
    return $stars;
}
?>