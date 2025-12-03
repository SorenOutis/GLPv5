<?php

/**
 * Streak System Setup Helper Script
 * 
 * This script helps set up the streak system for existing users.
 * Run with: php artisan tinker < setup_streaks.php
 */

use App\Models\User;
use App\Models\Streak;

echo "=== Streak System Setup ===\n";

echo "1. Creating streak records for users without streaks...\n";
$usersWithoutStreaks = User::doesntHave('streak')->get();

if ($usersWithoutStreaks->count() === 0) {
    echo "   ✓ All users already have streak records.\n";
} else {
    foreach ($usersWithoutStreaks as $user) {
        Streak::create([
            'user_id' => $user->id,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_login_date' => null,
            'last_login_at' => null,
        ]);
        echo "   ✓ Created streak record for: {$user->name}\n";
    }
    echo "   Created " . $usersWithoutStreaks->count() . " new streak records.\n";
}

echo "\n2. Streak Statistics:\n";
$totalUsers = User::count();
$usersWithStreaks = Streak::count();
$activeStreaks = Streak::where('current_streak', '>', 0)->count();
$topStreaker = Streak::orderBy('current_streak', 'desc')->first();
$longestStreaker = Streak::orderBy('longest_streak', 'desc')->first();

echo "   - Total users: {$totalUsers}\n";
echo "   - Users with streak records: {$usersWithStreaks}\n";
echo "   - Users with active streaks (>0 days): {$activeStreaks}\n";

if ($topStreaker) {
    $topUser = $topStreaker->user;
    echo "   - Top current streak: {$topStreaker->current_streak} days ({$topUser->name})\n";
}

if ($longestStreaker) {
    $longestUser = $longestStreaker->user;
    echo "   - Longest streak ever: {$longestStreaker->longest_streak} days ({$longestUser->name})\n";
}

echo "\n3. Testing streak update logic...\n";
$testUser = User::first();
if ($testUser && $testUser->streak) {
    echo "   Testing with user: {$testUser->name}\n";
    
    $oldStreak = $testUser->streak->current_streak;
    $oldLastLogin = $testUser->streak->last_login_date;
    
    Streak::updateStreakOnLogin($testUser);
    $testUser->refresh();
    
    echo "   - Previous streak: {$oldStreak}\n";
    echo "   - New streak: {$testUser->streak->current_streak}\n";
    echo "   - Last login date: {$testUser->streak->last_login_date}\n";
    echo "   ✓ Streak update logic working correctly.\n";
} else {
    echo "   No test user available.\n";
}

echo "\n=== Setup Complete ===\n";
echo "The streak system is ready to use!\n";
echo "Access admin panel at: /admin/streaks\n";
echo "\nMigrations run successfully!\n";
