<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ouvrages extends Model
{
    protected $fillable = ['titre', 'auteur', 'editeur', 'couverture', 'pages', 'date_publication', 'serie'];

    public function exemplaires(){
        return $this->hasMany(exemplaires::class , 'ouvrage_id') ;
    }
    
}
