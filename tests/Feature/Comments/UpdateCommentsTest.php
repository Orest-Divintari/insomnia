<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

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

        $this->patch(route('api.comments.update', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_comment_may_edit_the_comment()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $updatedComment = [
            'body' => 'updated body',
        ];

        $this->patch(
            route('api.comments.update', $comment),
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

        $this->patchJson(route('api.comments.update', $comment), $emptyComment)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_comment_the_body_must_be_of_type_string()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $nonStringComment = ['body' => array(15)];

        $this->patchJson(route('api.comments.update', $comment), $nonStringComment)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

}