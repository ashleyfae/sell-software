<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateAccessTokenForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-access-token {user : User ID number or email} {token_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a Sanctum access token for the provided user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = $this->getUser($this->argument('user'));

        $this->line(json_encode($user->toArray(), JSON_PRETTY_PRINT));

        if (! $this->confirm('Is this the correct user?')) {
            return;
        }

        $token = $user->createToken(
            name: $this->argument('token_name')
        );

        $this->line(sprintf(
            'Token created: %s',
            $token->plainTextToken
        ));
    }

    protected function getUser($userIdOrEmail) : User
    {
        if (is_numeric($userIdOrEmail)) {
            return User::findOrFail($userIdOrEmail);
        } else {
            return User::query()
                ->where('email', $userIdOrEmail)
                ->firstOrFail();
        }
    }
}
