<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class usagers extends Model
{
    protected $fillable = ['nom', 'prenom', 'email', 'identifiant', 'passe', 'blocage'];
    public function exemplaires()
    {
        return $this->hasMany(exemplaires::class, 'emprunteur_id');
    }

    public function ouvrages()
    {
        return $this->hasManyThrough(ouvrages::class, exemplaires::class, 'emprunteur_id', 'id', 'id', 'ouvrage_id');
    }
}
