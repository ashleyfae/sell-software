<?php

namespace Tests\Feature\Http\Controllers\Customers\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Customers\Account\ContactInformationController
 */
class ContactInformationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Http\Controllers\Customers\Account\ContactInformationController::show()
     */
    public function testGuestRedirected(): void
    {
        $response = $this->get(route('customer.account.contact.show'));

        $response->assertRedirectToRoute('login');
    }

    /**
     * @covers \App\Http\Controllers\Customers\Account\ContactInformationController::show()
     */
    public function testCanShow(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('customer.account.contact.show'));

        $response->assertOk();
        $response->assertViewIs('customers.account.contact');
        $response->assertSee('Jane Doe');
        $response->assertSee('janedoe@example.com');
    }

    /**
     * @covers \App\Http\Controllers\Customers\Account\ContactInformationController::update()
     */
    public function testCanUpdate(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
        ]);

        $response = $this->actingAs($user)->post(route('customer.account.contact.update'), [
            'name' => 'The New Jane Doe',
            'email' => 'newjanedoe@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', 'Contact information updated successfully.');

        $user = $user->refresh();

        $this->assertSame('The New Jane Doe', $user->name);
        $this->assertSame('newjanedoe@example.com', $user->email);
    }

    /**
     * @covers \App\Http\Controllers\Customers\Account\ContactInformationController::update()
     */
    public function testCanUpdateWithoutRequiredFieldsTriggersErrors(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
        ]);

        $response = $this->actingAs($user)->post(route('customer.account.contact.update'));

        $response->assertSessionHasErrors(['name', 'email']);
    }
}
