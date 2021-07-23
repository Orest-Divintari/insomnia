<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_upload_an_avatar()
    {
        $user = create(User::class);

        $response = $this->json('patch', route('ajax.user-avatar.update', $user));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_can_upload_an_image_as_avatar()
    {
        $user = $this->signIn();
        Storage::fake('public');

        $this->json('patch', route('ajax.user-avatar.update', $user), [
            'avatar' => $image = UploadedFile::fake()->image('avatar.jpg'),
        ]);

        Storage::disk('public')
            ->assertExists("images/avatars/users/{$user->name}/{$image->hashName()}");

        $this->assertEquals(
            asset("/images/avatars/users/{$user->name}/{$image->hashName()}"),
            $user->fresh()->avatar_path);
        $this->assertFalse($user->default_avatar);
    }

    /** @test */
    public function a_user_can_use_gravatar_as_avatar()
    {
        $user = $this->signIn();
        $gravatarMail = 'orestisdivintari@gmail.com';

        $user = $this->json('patch', route('ajax.user-avatar.update', $user), [
            'gravatar_email' => $gravatarMail,
        ])->json();

        $this->assertStringContainsString('gravatar', $user['avatar_path']);
        $this->assertStringContainsString('gravatar', $user['gravatar_path']);
        $this->assertFalse($user['default_avatar']);
        $this->assertEquals($user['gravatar_email'], $gravatarMail);
    }

    /** @test */
    public function the_gravatar_value_must_be_an_email()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route('ajax.user-avatar.update', $user), [
            'gravatar_email' => 'asdasdasd',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['gravatar_email' => ['Gravatars require valid email addresses.']]);
    }

    /** @test */
    public function a_gravatar_value_is_required_when_it_is_present()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route('ajax.user-avatar.update', $user), [
            'gravatar_email' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['gravatar_email' => ['Gravatars require valid email addresses.']]);
    }

    /** @test */
    public function a_gravatar_image_should_exist_for_the_given_email()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route('ajax.user-avatar.update', $user), [
            'gravatar_email' => 'gravatarDoesNotExist@gmail.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['gravatar_email' => ['Gravatars require valid email addresses.']]);
    }

    /** @test */
    public function the_avatar_must_be_an_image()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route(
            'ajax.user-avatar.update', $user),
            ['avatar' => 'invalid avatar']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function the_avatar_must_be_a_valid_type_of_image()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route(
            'ajax.user-avatar.update', $user),
            ['avatar' => UploadedFile::fake()->image('avatar.pdf')]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function the_avatar_is_required()
    {
        $user = $this->signIn();

        $response = $this->json('patch', route(
            'ajax.user-avatar.update', $user),
            ['avatar' => '']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function users_can_delete_an_uploaded_avatar()
    {
        $user = $this->signIn();
        Storage::fake('public');
        $this->json('patch', route('ajax.user-avatar.update', $user), [
            'avatar' => $image = UploadedFile::fake()->image('avatar.jpg'),
        ]);
        Storage::disk('public')
            ->assertExists("images/avatars/users/{$user->name}/{$image->hashName()}");
        $this->assertEquals(
            asset("/images/avatars/users/{$user->name}/{$image->hashName()}"),
            $user->fresh()->avatar_path);
        $this->assertFalse($user->default_avatar);

        $this->json('delete', route('ajax.user-avatar.destroy', $user));

        Storage::disk('public')
            ->missing("images/avatars/users/{$user->name}/{$image->hashName()}");
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'id' => $user->id,
            'avatar_path' => null,
            'default_avatar' => true,
        ]);

    }

    /** @test */
    public function it_deletes_the_previous_avatar_when_new_avatar_is_persisted()
    {
        $user = $this->signIn();
        Storage::fake('public');
        $this->json('patch', route('ajax.user-avatar.update', $user), [
            'avatar' => $firstImage = UploadedFile::fake()->image('avatar.jpg'),
        ]);
        Storage::disk('public')
            ->assertExists("images/avatars/users/{$user->name}/{$firstImage->hashName()}");
        $this->assertEquals(
            asset("/images/avatars/users/{$user->name}/{$firstImage->hashName()}"),
            $user->fresh()->avatar_path);
        $this->assertFalse($user->default_avatar);

        $this->json('patch', route('ajax.user-avatar.update', $user), [
            'avatar' => $secondImage = UploadedFile::fake()->image('avatar.jpg'),
        ]);

        Storage::disk('public')
            ->missing("images/avatars/users/{$user->name}/{$firstImage->hashName()}");
        Storage::disk('public')
            ->assertExists("images/avatars/users/{$user->name}/{$secondImage->hashName()}");
    }

}
