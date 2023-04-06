<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public User $userCustomer;

    public array $data = [
        'username' => 'johnlennon',
        'email' => 'johnlennon@beatles.com',
        'phone' => '12344567890'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->userCustomer = $this->authenticatedUser(['role' => User::$roles[2]]);
    }

    /** @test */
    public function customer_profile_page_can_be_rendered(): void
    {
        $res = $this->get(route('profile.edit'));

        $res->assertOk()
            ->assertSee(trans('Profile'));
    }

    /** @test */
    public function customer_profile_page_contains_livewire_component(): void
    {
        $res = $this->get(route('profile.edit'));

        $res->assertSeeLivewire(Profile::class);
    }

    /** @test */
    public function customer_profile_page_can_retrieve_data(): void
    {
        Livewire::test(Profile::class)
            ->assertFormSet($this->userCustomer->only(['name', 'username', 'email', 'address', 'phone']));
    }

    /** @test */
    public function customer_profile_page_validation_should_be_dispatched(): void
    {
        $this->withExceptionHandling();
        $user = $this->createUser($this->data);
        $newData = $user->only(['username', 'email', 'phone']);

        Livewire::test(Profile::class)
            ->fillForm($newData)
            ->call('save')
            ->assertHasFormErrors(['email' => 'unique', 'phone' => 'unique']);
    }

    /** @test */
    public function customer_profile_page_can_update_profile(): void
    {
        $data = $this->data;

        Livewire::test(Profile::class)
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas(User::class, Arr::only($data, ['username', 'email', 'phone']));
    }

    /** @test */
    public function customer_profile_page_can_upload_avatar(): void
    {
        Storage::fake(User::$directory);
        $image = UploadedFile::fake()->image('beatles.png');
        $image = ['avatar' => $image];
        $data = array_merge($this->data, $image);

        $res = Livewire::test(Profile::class)
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('home'));

        $image = $res->json()->payload['serverMemo']['data']['avatar'][0];
        $this->assertDatabaseHas(User::class, ['avatar' => $image]);
        $this->deleteFile($image);
    }

    /** @test */
    public function customer_profile_page_can_change_password(): void
    {
        $newPassword = 'abc@121212';
        $passwordData = [
            'current_password' => 'abc@123123',
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword
        ];
        $data = array_merge($this->data, $passwordData);

        Livewire::test(Profile::class)
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('home'));
        $this->assertTrue(Hash::check($newPassword, $this->userCustomer->fresh()->getAuthPassword()));
    }
}
