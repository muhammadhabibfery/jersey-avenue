<?php

namespace Tests\Validation;

trait EmailVerificationValidationTest
{
    /** @test */
    public function the_verify_email_page_cannot_be_rendered_when_user_unauthenticated(): void
    {
        $this->withExceptionHandling();

        $res = $this->get(route('verification.notice'));

        $res->assertRedirect(route('login'));
    }

    /** @test */
    public function the_hash_should_be_valid(): void
    {
        $this->withExceptionHandling();

        $res = $this->get(route('verification.verify', $this->verifyData($this->userNotVerified->id, 'abc@gmail.com')));

        $res->assertForbidden();
        $this->assertFalse($this->userNotVerified->fresh()->hasVerifiedEmail());
    }

    /** @test */
    public function unverified_user_cannot_access_the_verified_routes(): void
    {
        $this->withExceptionHandling();
        $this->actingAs($this->userNotVerified);

        $res = $this->get(route('cart'));

        $res->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function verified_user_cannot_resend_verify_link(): void
    {
        $this->withExceptionHandling();
        $this->actingAs($this->userVerified);

        $res = $this->post(route('verification.send'));

        $res->assertRedirect(route('home'))
            ->assertSessionMissing('status');
    }

    /** @test */
    public function verified_user_cannot_verify_twice(): void
    {
        $user = $this->userVerified;
        $this->withExceptionHandling();
        $this->actingAs($user);

        $res = $this->get($this->getVerificationUrl($user->id, $user->email));

        $res->assertRedirect(route('home'))
            ->assertSessionMissing('status');
    }
}
