<?php
namespace Database\Seeders;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key'   => 'title',
                'value' => 'The Annual<br><span>Marketing</span> Conference'
            ],
            [
                'key'   => 'subtitle',
                'value' => '10-12 December, Downtown Conference Center, New York'
            ],
            [
                'key'   => 'youtube_link',
                'value' => 'https://www.youtube.com/watch?v=jDDaplaOz7Q'
            ],
            [
                'key'   => 'about_description',
                'value' => 'Sed nam ut dolor qui repellendus iusto odit. Possimus inventore eveniet accusamus error amet eius aut accusantium et. Non odit consequatur repudiandae sequi ea odio molestiae. Enim possimus sunt inventore in est ut optio sequi unde.'
            ],
            [
                'key'   => 'about_where',
                'value' => 'Downtown Conference Center, New York'
            ],
            [
                'key'   => 'about_when',
                'value' => 'Monday to Wednesday<br>10-12 December'
            ],
            [
                'key'   => 'contact_address',
                'value' => 'A108 Adam Street, NY 535022, USA'
            ],
            [
                'key'   => 'contact_phone',
                'value' => '+1 5589 55488 55'
            ],
            [
                'key'   => 'contact_email',
                'value' => 'info@example.com'
            ],
            [
                'key'   => 'footer_description',
                'value' => 'In alias aperiam. Placeat tempore facere. Officiis voluptate ipsam vel eveniet est dolor et totam porro. Perspiciatis ad omnis fugit molestiae recusandae possimus. Aut consectetur id quis. In inventore consequatur ad voluptate cupiditate debitis accusamus repellat cumque.'
            ],
            [
                'key'   => 'footer_address',
                'value' => 'A108 Adam Street <br> New York, NY 535022<br> United States '
            ],
            [
                'key'   => 'footer_twitter',
                'value' => '#'
            ],
            [
                'key'   => 'footer_facebook',
                'value' => '#'
            ],
            [
                'key'   => 'footer_instagram',
                'value' => '#'
            ],
            [
                'key'   => 'footer_googleplus',
                'value' => '#'
            ],
            [
                'key'   => 'footer_linkedin',
                'value' => '#'
            ],

            [
                'key'   => 'registration_start_date',
                'value' => '2006-03-15 00:00:00'
            ],
            [
                'key'   => 'registration_close_date',
                'value' => '2026-10-01 12:00:00'
            ],
            [
                'key'   => 'event_price',
                'value' => '4500'
            ],
            [
                'key'   => 'early_registration_last_date',
                'value' => '2026-05-01 23:59:00'
            ],
            [
                'key'   => 'early_registration_event_price',
                'value' => '3500'
            ],
            [
                'key'   => 'selected_domain_discount',
                'value' => '3500'
            ],
            [
                'key'   => 'payment_last_date',
                'value' => '2026-11-15 20:00:00'
            ],
            [
                'key'   => 'seat_is_full',
                'value' => 'false'
            ],
            [
                'key'   => 'event_date',
                'value' => '2027-01-01'
            ],
            [
                'key'   => 'eur_participant_price',
                'value' => '18'
            ],
            [
                'key'   => 'inr_participant_price',
                'value' => '2000'
            ],
            [
                'key'   => 'usd_participant_price',
                'value' => '20'
            ],
            [
                'key'   => 'bdt_participant_price',
                'value' => '2500'
            ],
        ];

        foreach($settings as $setting)
        {
            Setting::create($setting);
        }
    }
}
