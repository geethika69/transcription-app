<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcription_file_id',
        'speaker',
        'start_time',
        'end_time',
        'source_text',
        'translated_text',
        'status',
    ];

    public function transcriptionFile()
    {
        return $this->belongsTo(TranscriptionFile::class);
    }

    public static function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) !== 3) return 0;
        return ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
    }

    public static function overlaps(string $start1, string $end1, string $start2, string $end2): bool
    {
        $s1 = self::timeToSeconds($start1);
        $e1 = self::timeToSeconds($end1);
        $s2 = self::timeToSeconds($start2);
        $e2 = self::timeToSeconds($end2);
        return $s1 < $e2 && $s2 < $e1;
    }
}