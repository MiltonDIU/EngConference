<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an end time to schedule items and loads the final BNC 2026 programme rundown
 * (public/documents/BNC_2026_Programme_Rundown_With_Transport.pdf).
 * The old schedule is no longer needed and is removed completely.
 */
return new class extends Migration
{
    private const MARKER = 'BNC2026 final programme';

    private array $speakerNames = [
        'amitav' => 'Amitav Ghosh',
        'dipesh' => 'Dipesh Chakrabarty',
        'tabassum' => 'Tabassum Zaman',
        'parui' => 'Avishek Parui',
        'wouters' => 'Wouters',
    ];

    private function programme(): array
    {
        // [day, start, end, title, subtitle, speakers]
        return [
            [1, '06:45', null, 'Transport - Morning Departure', 'From Dhaka and Surrounding Areas to Daffodil International University.', []],
            [1, '08:00', '09:30', 'Registration & Delegate Reception', 'Conference kit and token collection; spot registration if required.', []],
            [1, '08:00', '09:30', 'Welcome Snacks', 'Refreshments for delegates, speakers and invited guests.', []],
            [1, '09:30', '10:00', 'Inaugural Ceremony', 'Formal opening and inaugural addresses.', []],
            [1, '10:00', '10:15', 'Cultural Welcome to Amitav Ghosh', 'Short cultural reception for the keynote speaker.', ['amitav']],
            [1, '10:15', '11:15', 'Keynote Speech by Amitav Ghosh', 'Opening-day keynote address.', ['amitav']],
            [1, '11:30', '13:00', 'Parallel Session I - Onsite', '30 concurrent paper-presentation sessions (Sessions 1-30).', []],
            [1, '13:00', '14:00', 'Networking Lunch', 'Lunch for conference delegates and invited guests.', []],
            [1, '14:00', '14:40', 'Plenary Session I', 'Dr. Tabassum Zaman and Prof. Dr. Raihan Sharif.', ['tabassum']],
            [1, '14:40', '16:10', 'Parallel Session II - Onsite', '30 concurrent paper-presentation sessions (Sessions 31-60).', []],
            [1, '16:10', '17:00', 'চায়ের আড্ডা with Amitav Ghosh', 'Informal tea and literary conversation with Amitav Ghosh.', ['amitav']],
            [1, '17:00', '18:00', 'Roundtable Policy Dialogue I', 'Restricted session: for session chairs only.', []],
            [1, '18:00', null, 'Transport - Evening Departure', 'From Daffodil International University.', []],
            [1, '19:30', '20:30', 'Parallel Session III - Online', 'Virtual academic paper presentations.', []],
            [1, '21:00', '22:00', 'Plenary Session II (Online) - University of New Mexico', 'Special international plenary in collaboration with UNM, USA.', []],

            [2, '06:45', null, 'Transport - Morning Departure', 'From Dhaka and Surrounding Areas to Daffodil International University.', []],
            [2, '08:00', '09:00', 'Welcome Snacks', 'Morning refreshments and delegate reception.', []],
            [2, '09:00', '10:00', 'Keynote Address - Professor Dipesh Chakrabarty', 'Day-two keynote address.', ['dipesh']],
            [2, '10:10', '11:40', 'Parallel Session IV - Onsite', '25 concurrent paper-presentation sessions (Sessions 61-85).', []],
            [2, '11:40', '13:00', 'Networking Lunch', 'Lunch for conference delegates and invited guests.', []],
            [2, '13:00', '14:30', 'Plenary Session III', 'Dr. Jelle J. P. Wouters, Dr. Avishek Parui and Dr. Shamim Mondol.', ['wouters', 'parui']],
            [2, '14:30', '15:10', 'Special Session on Climate Vulnerability with Nepali Delegates', 'Focused exchange with delegates from Nepal.', []],
            [2, '15:10', '15:30', 'Tea & Networking Break', 'Refreshments and informal exchange.', []],
            [2, '15:30', '16:30', 'Roundtable Policy Dialogue II', 'Second thematic discussion with invited participants.', []],
            [2, '16:30', '17:00', 'Conference Closing Ceremony', 'Concluding remarks and acknowledgements.', []],
            [2, '17:00', '18:00', 'Closing Cultural Programme', 'Cultural performances to conclude the onsite programme.', []],
            [2, '18:00', null, 'Transport - Evening Departure', 'From Daffodil International University.', []],
            [2, '19:30', '20:30', 'Parallel Session V - Online', 'Evening virtual paper presentations. Timing is proposed; final link and time will be circulated.', []],
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Programme items run as a time range (e.g. 09:30 - 10:00 AM); empty = single point in time
            $table->time('end_time')->nullable()->after('start_time');
            // Breaks, transport and parallel sessions have no speaker
            $table->unsignedBigInteger('speaker_id')->nullable()->change();
        });

        $now = now();

        $speakerIds = [];
        foreach ($this->speakerNames as $key => $name) {
            $speakerIds[$key] = DB::table('speakers')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $name . '%')
                ->value('id');
        }

        DB::transaction(function () use ($now, $speakerIds) {
            $oldIds = DB::table('schedules')->pluck('id');
            DB::table('schedule_speaker')->whereIn('schedule_id', $oldIds)->delete();
            DB::table('schedules')->whereIn('id', $oldIds)->delete();

            foreach ($this->programme() as [$day, $start, $end, $title, $subtitle, $speakers]) {
                $id = DB::table('schedules')->insertGetId([
                    'day_number' => $day,
                    'start_time' => $start . ':00',
                    'end_time' => $end ? $end . ':00' : null,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'speaker_id' => null,
                    'total_seat' => 0,
                    'is_workshop' => '0',
                    'event_session' => '0',
                    'is_active' => '1',
                    'about' => self::MARKER,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($speakers as $key) {
                    if ($speakerIds[$key]) {
                        DB::table('schedule_speaker')->insert(['schedule_id' => $id, 'speaker_id' => $speakerIds[$key]]);
                    }
                }
            }

            DB::table('settings')->updateOrInsert(
                ['key' => 'schedule_start_date'],
                ['value' => '2026-12-04', 'updated_at' => $now, 'created_at' => $now]
            );
            // Conference runs 4-5 December 2026
            DB::table('settings')->where('key', 'event_date')->update(['value' => '2026-12-04', 'updated_at' => $now]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The old schedule was removed for good; rolling back only takes out the final programme
        $ids = DB::table('schedules')->where('about', self::MARKER)->pluck('id');

        DB::table('schedule_speaker')->whereIn('schedule_id', $ids)->delete();
        DB::table('schedules')->whereIn('id', $ids)->delete();
        DB::table('settings')->where('key', 'schedule_start_date')->delete();

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
};
