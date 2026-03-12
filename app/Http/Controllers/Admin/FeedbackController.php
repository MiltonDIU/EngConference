<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class FeedbackController extends Controller
{
        public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->errors()], 422);
            return redirect()->route('admin.home')->withErrors($validator)->withInput();

        }

        // Collect user_id from authenticated user
        $user_id = Auth::id();

        // Check if user has already posted feedback for this schedule
        $existingFeedback = Feedback::where('user_id', $user_id)
            ->where('schedule_id', $request->input('schedule_id'))
            ->exists();

        if ($existingFeedback) {
//            return response()->json(['errors' => 'You have already posted feedback for this schedule.'], 422);
            return redirect()->route('admin.home')->with('feedbackErrors', 'You have already posted feedback for this schedule.');

        }
        // Create a new feedback record
        $feedback = new Feedback();
        $feedback->user_id = $user_id;
        $feedback->schedule_id = $request->input('schedule_id');
        $feedback->rating = $request->input('rating');
        $feedback->comment = $request->input('comment');
        $feedback->save();
        return redirect()->route('admin.home')->with('message', 'Feedback successfully submitted.');

    }
}
