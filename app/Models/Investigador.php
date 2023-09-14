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

    /// procesar las palabras clave d el array keywords
    public function procesarPalabrasClave($data)
    {
        $keywords = [];
        if (isset($data['keywords'])) {
            foreach ($data['keywords']['keyword'] as $keyword) {
                $keywords[] = $keyword['content'];
            }
        }
        $this->palabras_clave = json_encode($keywords);
    }

    // coger el correo principal del mismo modo que keyword
    public function procesarCorreoPrincipal($data)
    {
        $correo_principal = '';
        if (isset($data['emails'])) {
            foreach ($data['emails']['email'] as $email) {
                if (isset($email['primary']) && $email['primary'] === true) {
                    $correo_principal = $email['email'];
                    break;
                }
            }
        }
        $this->correo_principal = $correo_principal;
    }
}
