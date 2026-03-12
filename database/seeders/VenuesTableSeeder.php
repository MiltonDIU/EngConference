<?php
namespace Database\Seeders;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $venue = Venue::create([
            'name'          => 'Daffodil Plaza',
            'address'       => '4/2 Sobhanbag, Mirpur Road, Dhanmondi, Dhaka',
            'latitude'      => '23.75484855496525',
            'longitude'     => '90.37654019499453',
            'description'   =>  'This full day event will feature hands-on workshops on building cloud native applications and machine learning, as well as insights on the recipe for creating the next generation of unicorn startups.'
        ]);

        foreach(range(1,8) as $id)
        {
            $mediaPath = storage_path()."/seeders/venue-gallery/$id.jpg";
            if (file_exists($mediaPath)) {
                $venue->addMedia($mediaPath)->preservingOriginal()->toMediaCollection('photos');
            }
        }
    }
}
