<?php

function displayThumbnail($thumbnail)
{
    $demo_thumbnail = "assets/dummy.jpg";
    if ($thumbnail) {
        return "uploads/blogs/" . $thumbnail;
    } else {
        return $demo_thumbnail;
    }
}

function blogDate($date)
{
    $dateTime = new DateTime($date);
    $formattedDate = $dateTime->format("F d Y");
    return $formattedDate;
}
