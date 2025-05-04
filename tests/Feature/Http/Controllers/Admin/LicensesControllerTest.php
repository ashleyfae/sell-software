<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\LicensesController;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(LicensesController::class)]
class LicensesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('providerCanList')]
    public function testCanList(bool $userIsAdmin, bool $canView): void
    {
        /** @var User $user */
        $user = User::factory()
            ->when($userIsAdmin, fn(Factory $factory) => $factory->admin())
            ->create();

        $response = $this->actingAs($user)->get(route('admin.licenses.index'));

        if ($canView) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }

    public static function providerCanList() : \Generator
    {
        yield 'admin can list' => [
            'userIsAdmin' => true,
            'canView' => true,
        ];

        yield 'non-admin cannot list' => [
            'userIsAdmin' => false,
            'canView' => false,
        ];
    }
}
