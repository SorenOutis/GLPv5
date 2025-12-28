<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            [
                'name' => 'Code Quest',
                'description' => 'Solve coding challenges to progress through quests',
                'category' => 'Coding',
                'difficulty' => 'Intermediate',
                'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop',
                'badge' => '🎮',
                'is_active' => true,
            ],
            [
                'name' => 'Algorithm Arena',
                'description' => 'Battle with algorithms in real-time competitions',
                'category' => 'Algorithms',
                'difficulty' => 'Advanced',
                'thumbnail' => 'https://images.unsplash.com/photo-1516534775068-bb57e39c146f?w=400&h=300&fit=crop',
                'badge' => '⚡',
                'is_active' => true,
            ],
            [
                'name' => 'Web Dev Maze',
                'description' => 'Navigate through web development challenges',
                'category' => 'Web Development',
                'difficulty' => 'Beginner',
                'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop',
                'badge' => '🌐',
                'is_active' => true,
            ],
            [
                'name' => 'Database Dungeons',
                'description' => 'Explore the depths of database optimization',
                'category' => 'Databases',
                'difficulty' => 'Advanced',
                'thumbnail' => 'https://images.unsplash.com/photo-1516534775068-bb57e39c146f?w=400&h=300&fit=crop',
                'badge' => '🗄️',
                'is_active' => true,
            ],
            [
                'name' => 'Design Dungeon',
                'description' => 'Master UI/UX design principles through interactive games',
                'category' => 'Design',
                'difficulty' => 'Intermediate',
                'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop',
                'badge' => '🎨',
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Climber',
                'description' => 'Scale the cloud computing mountains',
                'category' => 'Cloud',
                'difficulty' => 'Advanced',
                'thumbnail' => 'https://images.unsplash.com/photo-1516534775068-bb57e39c146f?w=400&h=300&fit=crop',
                'badge' => '☁️',
                'is_active' => true,
            ],
        ];

        foreach ($games as $game) {
            Game::create($game);
        }
    }
}
