<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
protected $table ='feedbacks';
    protected $fillable = ['user_id','schedule_id','comment','rating'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public static function  checkFeedback($schedule_id,$user_id)
    {
        $feedback = Feedback::where('schedule_id',$schedule_id)->where('user_id',$user_id)->first();
        if ($feedback){
            return true;
        }else{
            return false;
        }
    }

    public static function feedbackAverage($schedule_id)
    {

        $feedbacks = Feedback::where('schedule_id',$schedule_id)->get();
        $i=0;
        $rating = 0;
        $result = 0;
        foreach ($feedbacks as $feedback){
            $i++;
            $rating +=$feedback->rating;
        }
        if ($i>0){
            $result = $rating / $i;
            $result = round($result, 2);
        }

        return $result.'/'.$i;

    }

}
