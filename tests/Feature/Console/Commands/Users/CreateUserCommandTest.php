<?php

namespace Tests\Feature\Console\Commands\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Console\Commands\Users\CreateUserCommand
 */
class CreateUserCommandTest extends TestCase
{
    /**
     * @covers \App\Console\Commands\Users\CreateUserCommand::handle()
     */
    public function testCanHandle(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
