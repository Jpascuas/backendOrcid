<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigador extends Model
{
    use HasFactory;

    protected $table = 'investigadores'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'orcid',
        'nombre',
        'apellido',
        'palabras_clave',
        'correo_principal',
    ];

    protected $casts = [
        'palabras_clave' => 'json', // se almacena como JSON en la base de datos
    ];

}
