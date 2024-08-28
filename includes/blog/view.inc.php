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

function displayTags($tags)
{
    $tags_array = explode(",", $tags);
    $result = "";
    foreach ($tags_array as $tag) {
        $result .= " <a href='blogs.php?tag=" . $tag . "'>
                        <strong class='inline-block text-gray-500 px-2 py-1 text-xs cursor-pointer'>#" . $tag . "</strong>
                    </a>";
    }
    return $result;
}

function displayBlog($blog)
{
    $title = strlen($blog["title"]) <= 50 ? $blog["title"] : substr($blog["title"], 0, 50) . " ...";
    $blog_id = $blog["blog_id"];
    $thumbnail = displayThumbnail($blog["thumbnail"]);
    $created_at = $blog["created_at"];
    $tags = $blog["tags"];
    $author_id = $blog["author_id"];
    $author_name = $blog["author_name"];

    $result = "
        <div  class='p-4 space-y-4 group rounded-md bg-white text-gray-800 shadow-md h-[20rem] flex flex-col justify-between'>
             <div class='space-y-2'>
                <div class='h-40 overflow-hidden'>
                    <a href='blog.php?blog_id=" . $title . " >
                        <img src='" . $thumbnail . "' alt='blog image' class='object-cover w-full h-full group-hover:scale-105 transition-transform duration-300'>
                    </a>
                </div>
                <div>
                    <h3 class='text-lg leading-tight font-semibold'>
                        <a href='blog.php?blog_id=" . $blog_id . ">
                            " . $title . "
                        </a>
                    </h3>
                </div>
            </div>
             <div class='mt-3 flex flex-col'>
                <!-- Display blog tags -->
                <div class='flex items-center flex-wrap'>
                    " . displayTags($tags) . "
                </div>
                <div class='flex items-center justify-between'>
                    <p class='text-xs'>
                        Author -
                        <strong>
                            <a href='user.php?user_id=" . $author_id . "' class='w-full text-gray-800 font-semibold'> " . $author_name . "</a>
                        </strong>
                    </p>
                    <p>
                        <small class='font-semibold'>" . blogDate($created_at) . "</small>
                    </p>
                </div>
            </div>
        </div>
    ";
    return $result;
}

function displayLoadMoreBtn($blog_id)
{
    $result = "
            <button id='btn-more' data-last_blog_id='" . $blog_id . "' class='bg-gray-800 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md w-52'>Load More</button>
    ";
    return $result;
}
