<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();
        foreach($courses as $course)
        {
            Card::factory(3)->create([
                'course_id' => $course->id,
            ]);
            foreach($course->lessons as $lesson)
            {
                Card::factory(rand(1,5))->create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id,
                ]);
            }
        }
    }
}
