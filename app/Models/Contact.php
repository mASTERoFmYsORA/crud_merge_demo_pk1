<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'gender', 'profile_image', 'additional_file','merged_email'];

    public function customFields()
    {
        return $this->hasMany(ContactCustomField::class);
    }
}
