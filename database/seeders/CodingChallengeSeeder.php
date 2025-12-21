<?php

namespace Database\Seeders;

use App\Models\CodingChallenge;
use Illuminate\Database\Seeder;

class CodingChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CodingChallenge::create([
            'title' => 'Hello World',
            'description' => 'Write a program that prints "Hello, World!" to the console.',
            'difficulty' => 'Beginner',
            'language' => 'JavaScript',
            'category' => 'Basics',
            'xp_reward' => 50,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Sum of Two Numbers',
            'description' => 'Create a function that returns the sum of two numbers.',
            'difficulty' => 'Beginner',
            'language' => 'JavaScript',
            'category' => 'Functions',
            'xp_reward' => 75,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Fibonacci Sequence',
            'description' => 'Generate the first n numbers in the Fibonacci sequence.',
            'difficulty' => 'Intermediate',
            'language' => 'Python',
            'category' => 'Algorithms',
            'xp_reward' => 150,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Palindrome Checker',
            'description' => 'Check if a string is a palindrome (reads the same forwards and backwards).',
            'difficulty' => 'Intermediate',
            'language' => 'JavaScript',
            'category' => 'Strings',
            'xp_reward' => 125,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Binary Search Implementation',
            'description' => 'Implement a binary search algorithm to find a target value in a sorted array.',
            'difficulty' => 'Advanced',
            'language' => 'Java',
            'category' => 'Algorithms',
            'xp_reward' => 250,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Reverse a String',
            'description' => 'Write a function that reverses a given string without using built-in reverse functions.',
            'difficulty' => 'Beginner',
            'language' => 'Python',
            'category' => 'Strings',
            'xp_reward' => 60,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Prime Number Checker',
            'description' => 'Create a function that determines whether a given number is prime.',
            'difficulty' => 'Intermediate',
            'language' => 'Java',
            'category' => 'Math',
            'xp_reward' => 100,
            'is_active' => true,
        ]);

        CodingChallenge::create([
            'title' => 'Merge Sorted Arrays',
            'description' => 'Merge two sorted arrays into a single sorted array without using built-in sort functions.',
            'difficulty' => 'Advanced',
            'language' => 'JavaScript',
            'category' => 'Arrays',
            'xp_reward' => 200,
            'is_active' => true,
        ]);
    }
}
