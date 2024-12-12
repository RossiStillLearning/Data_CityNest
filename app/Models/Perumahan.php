<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perumahan extends Model
{
    //
    use HasFactory;
    protected $table = 'perumahans'; // Pastikan model merujuk ke tabel 'posts'
    protected $primaryKey = 'id'; // Set custom_id as the primary key
    public $incrementing = true; // Enable auto-incrementing
    protected $keyType = 'int'; // Define the primary key type as integer
// Define fillable fields for mass assignment
    protected $fillable = [
        'nama_perumahan',
        'alamat',
        'harga',
        'luas',
        'deskripsi',
        'kontak',
        'gambar'
    ];
}
