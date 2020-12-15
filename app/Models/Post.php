<?php

namespace app\Models;

class Post
{
    public string $id;
    public string $message;
    public string $fromName;
    public string $month;
    public int $week;
    public ?int $createdTimestamp;

    public function __construct(string $id, string $message, string $fromName, string $month, int $week, int $createdTimestamp = null)
    {
        $this->id = $id;
        $this->message = $message;
        $this->fromName = $fromName;
        $this->month = $month;
        $this->week = $week;
        $this->createdTimestamp = $createdTimestamp;
    }
}
