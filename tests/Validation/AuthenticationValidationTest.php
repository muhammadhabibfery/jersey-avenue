<?php

namespace Tests\Validation;

trait AuthenticationValidationTest
{
    /** @test */
    public function the_credentials_does_not_match(): void
    {
        $this->withExceptionHandling();
        $credentials = ['username' => $this->data['username'], 'password' => $this->fakePassword];

        $res = $this->post(route('login'), $credentials);

        $res->assertInvalid(['username']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_the_authenticated_routes(): void
    {
        $this->withExceptionHandling();

        $res = $this->get(route('dashboard'));

        $res->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
