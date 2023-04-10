<?php

namespace App\Console\Commands\Users;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create
                            {name : Name of the user}
                            {email : New user\'s email address}
                            {--password= : Optional password}
                            {--admin : Whether the user should be an administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    public function __construct(protected CreateNewUser $userCreator)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $password = $this->option('password') ?: Str::random(24);

        $user = $this->userCreator->create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        if ($this->option('admin')) {
            $user->is_admin = true;
            $user->save();
        }

        $this->line("Successfully created user #{$user->id}");
    }
}
