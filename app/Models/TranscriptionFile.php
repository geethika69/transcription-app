<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscriptionFile extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function segments()
    {
        return $this->hasMany(Segment::class);
    }
}