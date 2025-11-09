<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Source\Entities\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'The Guardian',
                'slug' => 'guardian',
                'url' => 'https://content.guardianapis.com',
                'is_active' => true,
            ],
            [
                'name' => 'NewsAPI.org',
                'slug' => 'newsapiorg',
                'url' => 'https://newsapi.org',
                'is_active' => true,
            ],
            [
                'name' => 'The New York Times',
                'slug' => 'nytimes',
                'url' => 'https://api.nytimes.com',
                'is_active' => true,
            ],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate(
                ['slug' => $source['slug']],
                $source
            );
        }
    }
}
