<?php

namespace app\Console;

use app\PostParser;

include_once 'app/Console/initial/autoload.php';

// Type "php app/Console/PostParserCommand.php" in console/terminal in the root of project
$postParser = new PostParser();
$posts = $postParser->handle(false);

var_dump(
    json_encode($postParser->averagePostLengthMonthly($posts)),
    json_encode($postParser->longestPostMonthly($posts)),
    json_encode($postParser->totalPostsWeekly($posts)),
    json_encode($postParser->averagePostsNumberUserMonth($posts)),
    json_encode($postParser->averagePostsNumberMonthUser($posts))
);
