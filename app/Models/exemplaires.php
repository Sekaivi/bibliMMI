<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class exemplaires extends Model
{
    protected $fillable = ['ouvrage_id', 'etat', 'disponible', 'emprunteur_id', 'date_retour_souhaitee', 'reserve', 'renouvellement'];

    protected $casts = [
        'date_retour_souhaitee' => 'date',
    ];

    public function ouvrage()
    {
        return $this->belongsTo(ouvrages::class, 'ouvrage_id');
    }

    public function emprunteur()
    {
        return $this->belongsTo(usagers::class, 'emprunteur_id');
    }
}
