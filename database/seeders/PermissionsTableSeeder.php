<?php
namespace Database\Seeders;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
//            [
//                'id'         => '1',
//                'title'      => 'user_management_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '2',
//                'title'      => 'permission_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '3',
//                'title'      => 'permission_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '4',
//                'title'      => 'permission_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '5',
//                'title'      => 'permission_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '6',
//                'title'      => 'permission_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '7',
//                'title'      => 'role_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '8',
//                'title'      => 'role_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '9',
//                'title'      => 'role_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '10',
//                'title'      => 'role_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '11',
//                'title'      => 'role_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '12',
//                'title'      => 'user_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '13',
//                'title'      => 'user_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '14',
//                'title'      => 'user_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '15',
//                'title'      => 'user_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '16',
//                'title'      => 'user_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '17',
//                'title'      => 'setting_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '18',
//                'title'      => 'setting_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '19',
//                'title'      => 'setting_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '20',
//                'title'      => 'setting_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '21',
//                'title'      => 'setting_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '22',
//                'title'      => 'speaker_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '23',
//                'title'      => 'speaker_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '24',
//                'title'      => 'speaker_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '25',
//                'title'      => 'speaker_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '26',
//                'title'      => 'speaker_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '27',
//                'title'      => 'schedule_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '28',
//                'title'      => 'schedule_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '29',
//                'title'      => 'schedule_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '30',
//                'title'      => 'schedule_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '31',
//                'title'      => 'schedule_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '32',
//                'title'      => 'venue_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '33',
//                'title'      => 'venue_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '34',
//                'title'      => 'venue_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '35',
//                'title'      => 'venue_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '36',
//                'title'      => 'venue_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '37',
//                'title'      => 'hotel_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '38',
//                'title'      => 'hotel_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '39',
//                'title'      => 'hotel_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '40',
//                'title'      => 'hotel_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '41',
//                'title'      => 'hotel_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '42',
//                'title'      => 'gallery_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '43',
//                'title'      => 'gallery_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '44',
//                'title'      => 'gallery_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '45',
//                'title'      => 'gallery_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '46',
//                'title'      => 'gallery_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '47',
//                'title'      => 'sponsor_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '48',
//                'title'      => 'sponsor_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '49',
//                'title'      => 'sponsor_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '50',
//                'title'      => 'sponsor_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '51',
//                'title'      => 'sponsor_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '52',
//                'title'      => 'faq_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '53',
//                'title'      => 'faq_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '54',
//                'title'      => 'faq_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '55',
//                'title'      => 'faq_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '56',
//                'title'      => 'faq_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '57',
//                'title'      => 'amenity_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '58',
//                'title'      => 'amenity_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '59',
//                'title'      => 'amenity_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '60',
//                'title'      => 'amenity_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '61',
//                'title'      => 'amenity_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '62',
//                'title'      => 'price_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '63',
//                'title'      => 'price_edit',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '64',
//                'title'      => 'price_show',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '65',
//                'title'      => 'price_delete',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '66',
//                'title'      => 'price_access',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//
//            [
//                'id'         => '67',
//                'title'      => 'events_create',
//                'created_at' => '2019-09-24 19:16:02',
//                'updated_at' => '2019-09-24 19:16:02',
//            ],
//            [
//                'id'         => '68',
//                'title'      => 'events_edit',
//                'created_at' => '2022-01-15 19:16:02',
//                'updated_at' => '2022-01-15 19:16:02',
//            ],
//            [
//                'id'         => '69',
//                'title'      => 'events_show',
//                'created_at' => '2022-01-15 19:16:02',
//                'updated_at' => '2022-01-15 19:16:02',
//            ],
//            [
//                'id'         => '70',
//                'title'      => 'events_delete',
//                'created_at' => '2022-01-15 19:16:02',
//                'updated_at' => '2022-01-15 19:16:02',
//            ],
//            [
//                'id'         => '71',
//                'title'      => 'events_access',
//                'created_at' => '2022-01-15 19:16:02',
//                'updated_at' => '2022-01-15 19:16:02',
//            ],
//
//            [
//                'id'         => '72',
//                'title'      => 'admin_dashboard',
//                'created_at' => '2022-01-15 19:16:02',
//                'updated_at' => '2022-01-15 19:16:02',
//            ],

//            [
//                'title' => 'blog_category_create',
//            ],
//            [
//                'title' => 'blog_category_edit',
//            ],
//            [
//                'title' => 'blog_category_show',
//            ],
//            [
//                'title' => 'blog_category_delete',
//            ],
//            [
//                'title' => 'blog_category_access',
//            ],
//            [
//                'title' => 'tag_create',
//            ],
//            [
//                'title' => 'tag_edit',
//            ],
//            [
//                'title' => 'tag_show',
//            ],
//            [
//                'title' => 'tag_delete',
//            ],
//            [
//                'title' => 'tag_access',
//            ],
//            [
//                'title' => 'blogs_post_access',
//            ],
//            [
//                'title' => 'post_create',
//            ],
//            [
//                'title' => 'post_edit',
//            ],
//            [
//                'title' => 'post_show',
//            ],
//            [
//                'title' => 'post_delete',
//            ],
//            [
//                'title' => 'post_access',
//            ],
//            [
//                'title' => 'comment_create',
//            ],
//            [
//                'title' => 'comment_edit',
//            ],
//            [
//                'title' => 'comment_show',
//            ],
//            [
//                'title' => 'comment_delete',
//            ],
//            [
//                'title' => 'comment_access',
//            ],
//            [
//                'title' => 'upload_medium_create',
//            ],
//            [
//                'title' => 'upload_medium_edit',
//            ],
//            [
//                'title' => 'upload_medium_show',
//            ],
//            [
//                'title' => 'upload_medium_delete',
//            ],
//            [
//                'title' => 'upload_medium_access',
//            ],
//            [
//                'title' => 'event_activity_create',
//            ],
//            [
//                'title' => 'event_activity_edit',
//            ],
//            [
//                'title' => 'event_activity_show',
//            ],
//            [
//                'title' => 'event_activity_delete',
//            ],
//            [
//                'title' => 'event_activity_access',
//            ],
//            [
//                'title' => 'referral_create',
//            ],
//            [
//                'title' => 'referral_edit',
//            ],
//            [
//                'title' => 'referral_show',
//            ],
//            [
//                'title' => 'referral_delete',
//            ],
//            [
//                'title' => 'referral_access',
//            ],
//            [
//                'title' => 'attendance_create',
//            ],
//            [
//                'title' => 'attendance_show',
//            ],
//            [
//                'title' => 'attendance_taken',
//            ],
//            [
//                'title' => 'attendance_certificate',
//            ],
//            [
//                'title' => 'attendance_access',
//            ],

            [
                'title' => 'email_data_bank_access',
            ],
            [
                'title' => 'data_bank_category_create',
            ],
            [
                'title' => 'data_bank_category_edit',
            ],
            [
                'title' => 'data_bank_category_show',
            ],
            [
                'title' => 'data_bank_category_delete',
            ],
            [
                'title' => 'data_bank_category_access',
            ],
            [
                'title' => 'data_bank_create',
            ],
            [
                'title' => 'data_bank_edit',
            ],
            [
                'title' => 'data_bank_show',
            ],
            [
                'title' => 'data_bank_delete',
            ],
            [
                'title' => 'data_bank_access',
            ],
            [
                'title' => 'custom_email_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
