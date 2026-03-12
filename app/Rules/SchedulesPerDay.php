<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SchedulesPerDay implements Rule
{
    protected $groupedSchedules;
    protected $schedules;

    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    public function passes($attribute, $value)
    {
        // Group the selected schedule IDs by day_number
        $selectedSchedules = collect($value)->groupBy(function ($item) {
            $schedule = \App\Models\Schedule::find($item);
            return $schedule ? $schedule->day_number : null;
        });

        // Compare the counts of selected schedules with the grouped schedules
        foreach ($this->schedules as $group) {
            $dayNumber = $group->first()->day_number;
            $maxId = $group->max('max_id');

            // Count the selected schedules for this day
            if ($selectedSchedules->has($dayNumber)) {
                $selectedCount = $selectedSchedules[$dayNumber]->count();
            } else {
                $selectedCount = 0;
            }

            // Check if the count exceeds the limit
            if ($selectedCount < 1 || $selectedCount > 5) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'Please select at least 1 and no more than 5 schedules for each day.';
    }
}
