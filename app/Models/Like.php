<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'comment_id',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    static public function likeCheck($id){
        $like = Like::where('user_id',auth()->user()->id)->where('comment_id',$id)->first();
        if($like){
            return true;
        }else{
            return false;
        }
     }


}
