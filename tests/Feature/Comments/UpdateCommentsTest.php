<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $errorMessage;

    public function setUp(): void
    {
        parent::setUp();
        $this->errorMessage = 'Please enter a valid message.';
    }

    /** @test */
    public function unathorized_users_cannot_edit_a_comment()
    {
        $comment = CommentFactory::create();
        $poster = $this->signIn();

        $response = $this->patch(route('ajax.comments.update', $comment));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_comment_may_edit_the_comment()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $updatedComment = ['body' => $this->faker->sentence];

        $this->patch(
            route('ajax.comments.update', $comment),
            $updatedComment
        );

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $comment->body,
        ]);
        $this->assertDatabaseHas('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $updatedComment['body'],
        ]);
    }

    /** @test */
    public function when_updating_a_comment_a_body_is_required()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $emptyComment = ['body' => ''];

        $response = $this->patchJson(
            route('ajax.comments.update', $comment),
            $emptyComment
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_comment_the_body_must_be_of_type_string()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $nonStringComment = ['body' => array(15)];

        $response = $this->patchJson(
            route('ajax.comments.update', $comment),
            $nonStringComment
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

}