<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TestAuthentication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:test {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test authentication by creating a test user or using provided credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        $password = $this->argument('password') ?? 'password';

        // Validate inputs
        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required|min:8']
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Check if the user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("User with email {$email} already exists.");
            $this->info("You can use this user to test the authentication system.");
            
            // Update password if needed
            if ($this->confirm('Do you want to reset the password for this user?')) {
                $user->password = Hash::make($password);
                $user->save();
                $this->info("Password updated successfully.");
            }
        } else {
            // Create a new user
            $user = new User();
            $user->name = 'Test User';
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            $this->info("Test user created successfully.");
        }

        // Output authentication details
        $this->info("------------------------------------");
        $this->info("Authentication Test Information");
        $this->info("------------------------------------");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("------------------------------------");
        $this->info("You can now test the authentication system with these credentials.");
        $this->info("Visit the login page and enter the email and password.");
        
        return 0;
    }
} 