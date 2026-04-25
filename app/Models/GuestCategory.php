<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestCategory extends Model
{
    use HasFactory;
    protected $table = 'guest_categories';
    protected $fillable= ['title','slug','publication_status'];
}
