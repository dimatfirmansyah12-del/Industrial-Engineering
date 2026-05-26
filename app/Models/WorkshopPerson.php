<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopPerson extends Model
{
    protected $fillable = [
        'name',
        'photo',
        'photo_position_x',
        'photo_position_y',
        'photo_zoom',
        'current_work',
        'progress_percentage',
        'progress_note',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'photo_position_x' => 'integer',
        'photo_position_y' => 'integer',
        'photo_zoom' => 'integer',
        'progress_percentage' => 'integer',
        'sort_order' => 'integer',
    ];

    public static function defaultNames(): array
    {
        return ['Widi', 'Wahid', 'Iwan', 'Eka', 'Rizki', 'Dimat'];
    }

    public static function ensureDefaults(): void
    {
        foreach (self::defaultNames() as $index => $name) {
            self::updateOrCreate(
                ['name' => $name],
                [
                    'status' => 'Active',
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
