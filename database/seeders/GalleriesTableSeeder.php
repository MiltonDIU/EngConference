<?php
namespace Database\Seeders;
use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GalleriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gallery = Gallery::create([
            'name' => 'Event'
        ]);
        foreach(range(1,8) as $id)
        {
            $mediaPath = storage_path()."/seeders/gallery/$id.jpg";
            if (file_exists($mediaPath)) {
                $gallery->addMedia($mediaPath)->preservingOriginal()->toMediaCollection('photos');
            }
        }
    }
}
