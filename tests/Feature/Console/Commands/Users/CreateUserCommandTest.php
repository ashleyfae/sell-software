<?php

namespace Tests\Feature\Console\Commands\Users;

use App\Console\Commands\Users\CreateUserCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CreateUserCommand::class)]
class CreateUserCommandTest extends TestCase
{
    /**
     * @see \App\Console\Commands\Users\CreateUserCommand::handle()
     */
    public function testCanHandle(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
