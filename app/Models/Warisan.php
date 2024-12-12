<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warisan extends Model
{
    //
    protected $table = 'warisans';
    protected $primaryKey = 'id'; // Set custom_id as the primary key
    public $incrementing = true; // Enable auto-incrementing
    protected $keyType = 'int'; // Define the primary key type as integer

    protected $fillable = [
        'nama_warisan',
        'asal',
        'gambar',
    ];
}
