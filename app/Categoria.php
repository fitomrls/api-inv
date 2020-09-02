<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table='categorias';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'nombrecat', 
        'categoria_estado',
        'icon',
        'color',
        'users_id'
    );

    protected $hidden = ['updated_at'];

    public function users()
    {
        return $this->belongsTo('App\User');
    }

}