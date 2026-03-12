<?php
namespace Database\Seeders;
use App\Models\Speaker;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpeakersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $speakers = [
            [
                'name'              => 'Brenden Legros',
                'slug'              => Str::slug('Brenden Legros'),
                'description'       => 'Quas alias incidunt',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 1,
            ],
            [
                'name'              => 'Hubert Hirthe',
                'slug'              => Str::slug('Hubert Hirthe'),
                'description'       => 'Consequuntur odio aut',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 2,
            ],
            [
                'name'              => 'Cole Emmerich',
                'slug'              => Str::slug('Cole Emmerich'),
                'description'       => 'Fugiat laborum et',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 3,
            ],
            [
                'name'              => 'Jack Christiansen',
                'slug'              => Str::slug('Jack Christiansen'),
                'description'       => 'Debitis iure vero',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 4,
            ],
            [
                'name'              => 'Alejandrin Littel',
                'slug'              => Str::slug('Alejandrin Littel'),
                'description'       => 'Qui molestiae natus',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 5,
            ],
            [
                'name'              => 'Willow Trantow',
                'slug'              => Str::slug('Willow Trantow'),
                'description'       => 'Non autem dicta',
                'twitter'           => '#',
                'facebook'          => '#',
                'linkedin'          => '#',
                'full_description'  => $faker->paragraph,
                'show_home'         => 1,
                'serial'            => 6,
            ],
        ];
        foreach($speakers as $key => $speaker)
        {
            $photo_id = $key+1;
            $speaker = Speaker::create($speaker);
            $directory = storage_path()."/seeders/speakers";
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $mediaPath = $directory."/$photo_id.jpg";

            if (!file_exists($mediaPath)) {
                try {
                    $imageUrl = "https://i.pravatar.cc/300?u=" . urlencode($speaker->name);
                    $imageContent = file_get_contents($imageUrl);
                    if ($imageContent !== false) {
                        file_put_contents($mediaPath, $imageContent);
                    }
                } catch (\Exception $e) {
                    // Fail silently if download fails - we still have the file_exists check below
                }
            }

            if (file_exists($mediaPath)) {
                $speaker->addMedia($mediaPath)->preservingOriginal()->toMediaCollection('photo');
            }
        }
    }
}
