<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        if (!User::where('email', 'admin@admin.admin')->exists()) {
            \App\Models\User::query()->create([
                'name' => 'Admin',
                'email' => 'admin@admin.admin',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);
        }

        $this->call(CourseSeeder::class);
        $this->call(LessonSeeder::class);
        $this->call(CardSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(AnswerSeeder::class);
        $this->call(PromptSeeder::class);
    }
}
