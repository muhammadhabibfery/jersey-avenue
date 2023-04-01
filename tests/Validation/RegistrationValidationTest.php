<?php

namespace Tests\Validation;

trait RegistrationValidationTest
{
    /** @test */
    public function all_fields_are_required(): void
    {
        $this->withExceptionHandling();

        $res = $this->post(route('register'), []);

        $res->assertInvalid(['name', 'username', 'email', 'password']);
    }

    /** @test */
    public function username_and_email_fields_should_be_unique(): void
    {
        $this->withExceptionHandling();
        $user = $this->createUser()->only(['name', 'username', 'email']);

        $res = $this->post(route('register'), $user);

        $res->assertInvalid(['username', 'email']);
    }

    /** @test */
    public function password_field_should_be_follow_the_password_rules(): void
    {
        $this->withExceptionHandling();
        $data = ['password' => 'abc', 'password_confirmation' => 'abc'];

        $res = $this->post(route('register'), $data);

        $res->assertInvalid(['password']);
    }
}
