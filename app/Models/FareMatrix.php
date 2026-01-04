<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareMatrix extends Model
{
    use HasFactory;

    // 1. Link to your database table
    // (Laravel expects 'fare_matrices', change this if your table is named 'fare_matrix')
    protected $table = 'fare_matrices';

    // 2. Allow these fields to be saved
    // We use $guarded = [] to allow ALL fields you created in your database
    protected $guarded = []; 
}