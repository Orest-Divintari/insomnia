<?php

namespace Tests\Traits;

use App\Models\Category;
use App\Models\GroupCategory;
use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\User;

trait SearchableTest
{

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'elastic']);
    }

    /**
     * Make a search request with the given parameters
     *
     * @param array $parameters
     * @return array
     */
    public function searchJson($parameters, $numberOfItems)
    {
        $counter = 0;

        do {
            usleep(200000);
            $results = $this->getJson(
                route('search.index', $parameters)
            );
            $counter++;

        } while ($this->failsValidation($results, $numberOfItems) || $counter <= 10);

        return $results->json()['data'];
    }

    public function searchNoResults($parameters, $expectedResult)
    {
        $counter = 0;

        do {
            $counter++;
            usleep(200000);
            $response = $this->getJson(
                route('search.index', $parameters)
            );

        } while ($response->getContent() !== $expectedResult || $counter < 20);

        return $response;
    }
    /**
     * Determine whether the results contain the given thread
     *
     * @param array $results
     * @param Thread $thread
     * @return boolean
     */
    public function assertContainsThread($results, $thread)
    {
        $results = collect($results);

        $this->assertTrue(
            $results->contains(function ($result) use ($thread) {
                $categoryKeyExists = array_key_exists('category', $result) ? true : false;

                return $result['id'] == $thread->id
                && $result['poster']['id'] == $thread->poster->id
                && $categoryKeyExists
                && $result['category']['id'] == $thread->category->id;
            }));
    }

    /**
     * Determine whether the results contain the given threadReply
     *
     * @param array $results
     * @param Reply $threadReply
     * @return boolean
     */
    public function assertContainsThreadReply($results, $threadReply)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($threadReply) {
                return $result['id'] == $threadReply->id
                && $result['poster']['id'] == $threadReply->poster->id
                && $result['repliable']['id'] == $threadReply->repliable->id
                && $result['repliable']['poster']['id'] == $threadReply->repliable->poster->id
                && $result['repliable']['category']['id'] == $threadReply->repliable->category->id;
            }));
    }

    /**
     * Determine whether the results contain the given profilePost
     *
     * @param array $results
     * @param ProfilePost $profilePost
     * @return bool
     */
    public function assertContainsProfilePost($results, $profilePost)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($profilePost) {
                $profileOwnerKeyExists = array_key_exists('profile_owner_id', $result) ? true : false;

                return $result['id'] == $profilePost->id
                && $profileOwnerKeyExists
                && $result['profile_owner_id'] == $profilePost->profileOwner->id
                && $result['poster']['id'] == $profilePost->poster->id;
            }));
    }

    /**
     * Validate the results of the request
     *
     * @param mixed $results
     * @return boolean
     */
    public function failsValidation($results, $numberOfItems)
    {
        if (!is_object(json_decode($results->getContent()))) {
            return true;
        } elseif (is_array($results->json()['data']) && count($results->json()['data']) != $numberOfItems) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the results contain the given comment
     *
     * @param array $results
     * @param Reply $comment
     * @return bool
     */
    public function assertContainsComment($results, $comment)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($comment) {
                $repliableKeyExists = array_key_exists('repliable', $result) ? true : false;

                return $result['id'] == $comment->id
                && $result['poster']['id'] == $comment->poster->id
                && $repliableKeyExists
                && $result['repliable']['id'] == $comment->repliable->id
                && $result['repliable']['poster']['id'] == $comment->repliable->poster->id
                && $result['repliable']['profile_owner_id'] == $comment->repliable->profileOwner->id;
            }));
    }

    protected function emptyIndices()
    {
        GroupCategory::all()->each->delete();
        ProfilePost::all()->each->delete();
        User::all()->each->delete();
        Tag::all()->each->delete();
    }

    public function sentence()
    {
        return 'Last night in Ny, a big brown fox got an iphone from an Applestore';
    }

    public function searchTerm()
    {
        return 'big brown fox';
    }

    public function noResultsMessage()
    {
        return 'No results found.';
    }
}