<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdLocation extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the Ad that owns the stats.
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad')->withDefault([
            'title' => 'La Habana'
        ]);
    }
}
