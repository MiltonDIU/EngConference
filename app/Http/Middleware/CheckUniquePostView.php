<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Post;
class CheckUniquePostView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
//    public function handle($request, Closure $next)
//    {
//
//        $postId = $request->route('id'); // Adjust this based on your route parameter
//        $ipAddress = $request->ip();
//        $existingView = DB::table('post_views')
//            ->where('post_id', $postId)
//            ->where('ip_address', $ipAddress)
//            ->first();
//        if (!$existingView) {
//            DB::table('posts')
//                ->where('id', $postId)
//                ->increment('views');
//
//            DB::table('post_views')->insert([
//                'post_id' => $postId,
//                'ip_address' => $ipAddress,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }
//        return $next($request);
//    }

    public function handle($request, Closure $next)
    {
        $postId = $request->route('id'); // Adjust this based on your route parameter


        if (!$this->hasViewedPost($postId)) {

            $this->markPostAsViewed($postId);
            $this->incrementPostViews($postId);

        }

        return $next($request);
    }

    private function hasViewedPost($postId)
    {

        return Session::has('viewed_post_' . $postId);
    }

    private function markPostAsViewed($postId)
    {

        Session::put('viewed_post_' . $postId, true);
    }

    private function incrementPostViews($postId)
    {
        // Update your database logic to increment post views count
        // For example:
         $post = Post::find($postId);
         $post->views++;
         $post->save();
    }


}
