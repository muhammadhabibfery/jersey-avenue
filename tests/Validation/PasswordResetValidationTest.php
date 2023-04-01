<?php

namespace Tests\Validation;

trait PasswordResetValidationTest
{
    /** @test */
    public function the_email_field_must_have_an_existing_user(): void
    {
        $this->withExceptionHandling();

        $res = $this->post(route('password.email'), ['email' => 'paul@beatles.com']);

        $res->assertInvalid('email')
            ->assertSessionMissing('status');
    }

    /** @test */
    public function the_password_fields_should_be_follow_the_password_rules(): void
    {
        $this->withExceptionHandling();

        $res = $this->post(route('password.store'), $this->resetPasswordData($this->user->email, 'abc', 'abcde', $this->getToken()));

        $res->assertInvalid('password')
            ->assertSessionMissing('status');
    }

    /** @test */
    public function the_token_field_should_be_valid(): void
    {
        $this->withExceptionHandling();

        $res = $this->post(route('password.store'), $this->resetPasswordData($this->user->email, 'abc@121212', 'abc@121212', 'token12345'));

        $res->assertInvalid('email')
            ->assertSessionMissing('status');
    }
}
