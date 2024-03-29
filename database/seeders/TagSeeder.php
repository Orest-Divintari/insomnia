<?php
namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'apple',
            'mac',
            'macos',
            'iphone',
            'ipad',
            'ipod',
            'iOS',
            'watchOS',
            'mac pro',
            'macbook',
            'macbook air',
            'macbook pro',
            'imac',
            'imessage',
            'mac mini',
            'airpods',
            'airpods pro',
            'airpods max',
            'earpods',
        ];

        foreach ($tags as $tag) {
            Tag::factory()->create(['name' => $tag]);
        }
    }
}