<?php

declare(strict_types=1);

namespace app;

use app\Cache\Cache;
use app\Client\ApiClient;
use app\Models\Posts;
use Exception;

class PostParser
{
    /**
     * @param bool $checkNewest
     *
     * @return Posts
     * @throws Exception
     */
    public function handle(bool $checkNewest = true): Posts
    {
        $cachedClient = new Cache(new ApiClient(), 3300); // 55 minutes is ok to hold token
        $cachedClient->obtainToken();

        $cachedClient->setTtl(18000); // cache for 5 hours until check newest or force
        $cachedClient->setForce($this->forceLoadNeeded($cachedClient, $checkNewest));

        return $cachedClient->getPosts();
    }

    /**
     * Check if force loading from API needed or use standard caching mechanism
     * Also can turn off checking data from API by $checkNewest
     *
     * @param Cache $cachedClient
     * @param bool $checkNewest
     *
     * @return bool
     */
    public function forceLoadNeeded(Cache $cachedClient, bool $checkNewest = true): bool
    {
        $forceLoad = false;

        if ($checkNewest) {
            $cachedPosts = $cachedClient->getCache($cachedClient->configureCacheKeyByMethod('getPosts', []));

            if ($cachedPosts instanceof Posts) {

                // take the newest post from cache and from api
                $newestCachedPost = $cachedPosts->getIterator()->offsetGet(0);
                $newestPost = $cachedClient->getRealObject()->getPosts(1)->getIterator()->offsetGet(0);

                // we need to load new api data, when the newest post has bigger timestamp
                if ($newestPost->createdTimestamp > $newestCachedPost->createdTimestamp) {
                    $forceLoad = true;
                }
            }
        }

        return $forceLoad;
    }

    /**
     * Average character length of posts per month
     *
     * @param Posts $posts
     *
     * @return array
     * @throws Exception
     */
    public function averagePostLengthMonthly(Posts $posts): array
    {
        $result = [];

        foreach ($posts->groupBy('month') as $month => $perMonth) {

            $result[] = (object) [
                'month'   => $month,
                'average' => (new Posts(...$perMonth))->getAverage(),
            ];
        }

        return $result;
    }

    /**
     * Longest post by character length per month
     *
     * @param Posts $posts
     *
     * @return array
     */
    public function longestPostMonthly(Posts $posts): array
    {
        $result = [];

        foreach ($posts->groupBy('month') as $month => $perMonth) {

            $result[] = (object) [
                'month'           => $month,
                'longest_post_id' => (new Posts(...$perMonth))->getLongest()->id,
            ];
        }

        return $result;
    }

    /**
     * Total posts split by week number
     *
     * @param Posts $posts
     *
     * @return array
     * @throws Exception
     */
    public function totalPostsWeekly(Posts $posts): array
    {
        $result = [];

        foreach ($posts->groupBy('week') as $week => $perWeek) {

            $result[] = (object) [
                'week'  => $week,
                'total' => (new Posts(...$perWeek))->getCount(),
            ];
        }

        return $result;
    }

    /**
     * Average number of posts per user per month
     * Per USER -> Per MONTH
     *
     * @param Posts $posts
     *
     * @return array
     * @throws Exception
     */
    public function averagePostsNumberUserMonth(Posts $posts): array
    {
        $result = [];

        foreach ($posts->groupBy('fromName') as $user => $perUser) {

            $userTotalPostCount = $userWorkMonthCount = 0;

            foreach ((new Posts(...$perUser))->groupBy('month') as $month => $perMonth) {
                $userTotalPostCount += (new Posts(...$perMonth))->getCount();
                $userWorkMonthCount++;
            }

            $result[] = (object) [
                'user'      => $user,
                'per_month' => $userWorkMonthCount ? round($userTotalPostCount / $userWorkMonthCount, 3) : 0,
            ];
        }

        return $result;
    }

    /**
     * Average number of posts per month per user
     * Per MONTH -> Per USER
     *
     * @param Posts $posts
     *
     * @return array
     * @throws Exception
     */
    public function averagePostsNumberMonthUser(Posts $posts): array
    {
        $result = [];

        foreach ($posts->groupBy('month') as $month => $perMonth) {

            $monthTotalPostCount = $monthActiveUserCount = 0;

            foreach ((new Posts(...$perMonth))->groupBy('fromName') as $user => $perUser) {
                $monthTotalPostCount += (new Posts(...$perUser))->getCount();
                $monthActiveUserCount++;
            }

            $result[] = (object) [
                'month'    => $month,
                'per_user' => $monthActiveUserCount ? round($monthTotalPostCount / $monthActiveUserCount, 3) : 0,
            ];
        }

        return $result;
    }
}
