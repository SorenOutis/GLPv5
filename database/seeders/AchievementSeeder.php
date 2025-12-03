<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // Learning Achievements
            [
                'name' => 'First Steps',
                'description' => 'Complete your first lesson',
                'icon' => '📚',
                'category' => 'Learning',
                'difficulty' => 'Easy',
                'xp_reward' => 50,
            ],
            [
                'name' => 'Quick Learner',
                'description' => 'Complete 5 lessons in one day',
                'icon' => '⚡',
                'category' => 'Learning',
                'difficulty' => 'Medium',
                'xp_reward' => 150,
            ],
            [
                'name' => 'Bookworm',
                'description' => 'Complete 25 lessons total',
                'icon' => '📖',
                'category' => 'Learning',
                'difficulty' => 'Hard',
                'xp_reward' => 300,
            ],
            [
                'name' => 'Scholar',
                'description' => 'Complete 100 lessons total',
                'icon' => '🎓',
                'category' => 'Learning',
                'difficulty' => 'Legendary',
                'xp_reward' => 1000,
            ],

            // Streak Achievements
            [
                'name' => 'On Fire',
                'description' => 'Maintain a 7-day learning streak',
                'icon' => '🔥',
                'category' => 'Streak',
                'difficulty' => 'Medium',
                'xp_reward' => 200,
            ],
            [
                'name' => 'Unstoppable',
                'description' => 'Maintain a 30-day learning streak',
                'icon' => '💪',
                'category' => 'Streak',
                'difficulty' => 'Hard',
                'xp_reward' => 500,
            ],
            [
                'name' => 'Legendary Dedication',
                'description' => 'Maintain a 100-day learning streak',
                'icon' => '👑',
                'category' => 'Streak',
                'difficulty' => 'Legendary',
                'xp_reward' => 2000,
            ],

            // Achievement Milestones
            [
                'name' => 'Achievement Hunter',
                'description' => 'Unlock 5 achievements',
                'icon' => '🏆',
                'category' => 'Milestones',
                'difficulty' => 'Medium',
                'xp_reward' => 250,
            ],
            [
                'name' => 'Perfect Collector',
                'description' => 'Unlock 25 achievements',
                'icon' => '✨',
                'category' => 'Milestones',
                'difficulty' => 'Hard',
                'xp_reward' => 750,
            ],
            [
                'name' => 'Master of All',
                'description' => 'Unlock all available achievements',
                'icon' => '🌟',
                'category' => 'Milestones',
                'difficulty' => 'Legendary',
                'xp_reward' => 5000,
            ],

            // Level Achievements
            [
                'name' => 'Level Up',
                'description' => 'Reach level 5',
                'icon' => '⬆️',
                'category' => 'Levels',
                'difficulty' => 'Easy',
                'xp_reward' => 100,
            ],
            [
                'name' => 'Rising Star',
                'description' => 'Reach level 25',
                'icon' => '⭐',
                'category' => 'Levels',
                'difficulty' => 'Hard',
                'xp_reward' => 400,
            ],
            [
                'name' => 'Peak Performance',
                'description' => 'Reach level 50',
                'icon' => '🚀',
                'category' => 'Levels',
                'difficulty' => 'Legendary',
                'xp_reward' => 3000,
            ],

            // XP Achievements
            [
                'name' => 'XP Collector',
                'description' => 'Earn 1000 XP',
                'icon' => '💰',
                'category' => 'XP',
                'difficulty' => 'Easy',
                'xp_reward' => 50,
            ],
            [
                'name' => 'XP Master',
                'description' => 'Earn 10000 XP',
                'icon' => '💎',
                'category' => 'XP',
                'difficulty' => 'Hard',
                'xp_reward' => 500,
            ],
            [
                'name' => 'XP Legend',
                'description' => 'Earn 50000 XP',
                'icon' => '👸',
                'category' => 'XP',
                'difficulty' => 'Legendary',
                'xp_reward' => 2500,
            ],

            // Social Achievements
            [
                'name' => 'Social Butterfly',
                'description' => 'Post your first comment',
                'icon' => '🦋',
                'category' => 'Social',
                'difficulty' => 'Easy',
                'xp_reward' => 25,
            ],
            [
                'name' => 'Discussion Master',
                'description' => 'Participate in 50 discussions',
                'icon' => '💬',
                'category' => 'Social',
                'difficulty' => 'Medium',
                'xp_reward' => 300,
            ],
            [
                'name' => 'Community Leader',
                'description' => 'Help 10 other students',
                'icon' => '🤝',
                'category' => 'Social',
                'difficulty' => 'Hard',
                'xp_reward' => 600,
            ],

            // Special Achievements
            [
                'name' => 'Speedrunner',
                'description' => 'Complete 10 lessons in 1 hour',
                'icon' => '⏱️',
                'category' => 'Special',
                'difficulty' => 'Hard',
                'xp_reward' => 400,
            ],
            [
                'name' => 'Perfect Score',
                'description' => 'Score 100% on an assessment',
                'icon' => '💯',
                'category' => 'Special',
                'difficulty' => 'Hard',
                'xp_reward' => 350,
            ],
            [
                'name' => 'Early Bird',
                'description' => 'Complete a lesson before 8 AM',
                'icon' => '🌅',
                'category' => 'Special',
                'difficulty' => 'Easy',
                'xp_reward' => 75,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(['name' => $achievement['name']], $achievement);
        }
    }
}
