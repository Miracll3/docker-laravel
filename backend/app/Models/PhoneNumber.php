<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as EloquentModel; 

class PhoneNumber extends EloquentModel
{
    use HasFactory;

    protected $connection = 'mongodb'; // Specify the MongoDB connection
    protected $collection = 'phone_numbers'; // Specify the collection name
    protected $fillable = [       // Define the fields that can be mass-assigned
        'phone_number',
        'country_code',
        'type',
        'is_possible_number_length_match',
        'is_valid',
    ];
}
