<?php
namespace Database\Seeders;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sponsors = [
            [
                'name' => 'Strider',
                'link' => '#'
            ],
            [
                'name' => 'Runtastic',
                'link' => '#'
            ],
            [
                'name' => 'EditShare',
                'link' => '#'
            ],
            [
                'name' => 'InFocus',
                'link' => '#'
            ],
            [
                'name' => 'gategroup',
                'link' => '#'
            ],
            [
                'name' => 'Cadent',
                'link' => '#'
            ],
            [
                'name' => 'Ceph',
                'link' => '#'
            ],
            [
                'name' => 'Alitalia',
                'link' => '#'
            ],
        ];

        foreach($sponsors as $key => $sponsor)
        {
            $photo_id = $key + 1;
            $sponsor = Sponsor::create($sponsor);
            $mediaPath = storage_path()."/seeders/supporters/$photo_id.png";
            if (file_exists($mediaPath)) {
                $sponsor->addMedia($mediaPath)->preservingOriginal()->toMediaCollection('logo');
            }
        }
    }
}
