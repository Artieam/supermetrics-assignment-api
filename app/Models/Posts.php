<?php

declare(strict_types=1);

namespace app\Models;

class Posts implements \IteratorAggregate
{
    /**
     * @var Post[]
     */
    private array $posts;

    public function __construct(Post ...$posts)
    {
        $this->posts = $posts;
    }

    /**
     * Calculate post by character length
     *
     * @return Post
     */
    public function getLongest(): Post
    {
        return array_reduce($this->posts, function (?Post $carry, Post $post) {
            return (strlen($carry->message ?? '') > strlen($post->message)) ? $carry : $post;
        });
    }

    /**
     * Average character length of posts
     *
     * @return float
     * @throws \Exception
     */
    public function getAverage(): float
    {
        $total = 0;

        if (empty($this->posts)) {
            return $total;
        }

        $total = array_reduce($this->posts, function (?int $carry, Post $post) {
            $carry += strlen($post->message);
            return $carry;
        });

        return round($total / $this->getCount(), 3);
    }

    /**
     * Count posts in collection
     *
     * @return int
     * @throws \Exception
     */
    public function getCount(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * Group posts by key
     *
     * @param string $property
     *
     * @return array
     */
    public function groupBy(string $property): array
    {
        $result = [];

        if (property_exists(Post::class, $property)) {
            foreach($this->posts as $post) {
                $result[$post->$property][] = $post;
            }
        }

        return $result;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->posts);
    }

    public function push(Post $post): Posts
    {
        $this->posts[] = $post;

        return $this;
    }
}
