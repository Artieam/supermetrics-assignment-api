<?php

declare(strict_types=1);

namespace app\Http;

use app\Models\Posts;
use app\PostParser;

class PostController
{
    private PostParser $postParser;

    public function __construct()
    {
        $this->postParser = new PostParser();
    }

    /**
     * GET all posts
     *
     * @return array
     * @throws \Exception
     */
    public function index(): array
    {
        return $this->getPosts()->getIterator()->getArrayCopy();
    }

    /**
     * GET average character length of posts per month
     *
     * @return array
     * @throws \Exception
     */
    public function avrPostLenMonth(): array
    {
        return $this->postParser->averagePostLengthMonthly($this->getPosts());
    }

    /**
     * GET Longest post by character length per month
     *
     * @return array
     * @throws \Exception
     */
    public function longestPostMonth(): array
    {
        return $this->postParser->longestPostMonthly($this->getPosts());
    }

    /**
     * GET total posts split by week number
     *
     * @return array
     * @throws \Exception
     */
    public function totalPostWeekly(): array
    {
        return $this->postParser->totalPostsWeekly($this->getPosts());
    }

    /**
     * Average number of posts per user per month
     *
     * @return array
     * @throws \Exception
     */
    public function avrPostNumUserMonth(): array
    {
        return $this->postParser->averagePostsNumberUserMonth($this->getPosts());
    }

    /**
     * Average number of posts per month per user
     *
     * @return array
     * @throws \Exception
     */
    public function avrPostNumMonthUser(): array
    {
        return $this->postParser->averagePostsNumberMonthUser($this->getPosts());
    }

    /**
     * @return Posts
     * @throws \Exception
     */
    private function getPosts(): Posts
    {
        return $this->postParser->handle(true);
    }
}
