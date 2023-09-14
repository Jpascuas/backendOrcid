<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Investigador; 

class InvestigadorController extends Controller
{

    public function create(Request $request, $orcid)
    {

        // Realizar una solicitud HTTP a la API de ORCID
        $response = Http::get("https://pub.orcid.org/v3.0/$orcid");

        // Validar que los datos del investigador se obtuvieron correctamente
        if ($response->successful()) {
            $data = $response->json();

            // Crear un nuevo registro de Investigador
            $investigador = new Investigador;
            $investigador->orcid = $data['orcid-identifier']['path'];
            $investigador->nombre = $data['name']['given-names']['value'];
            $investigador->apellido = $data['name']['family-name']['value'];

            // Procear el array dekeywords 
            $investigador->procesarPalabrasClave($data);

            // Procesar el correo principal similar a kayword
            $investigador->procesarCorreoPrincipal($data);

            $investigador->save();

            return response()->json(['message' => 'Éxito', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'ORCID no encontrado'], 404);
        }
    }

    public function delete($orcid)
    {
        // Implementa la lógica para eliminar un investigador por su ORCID
    }

    public function list()
    {
        // Implementa la lógica para listar todos los investigadores
    }

    public function show($orcid)
    {
        // Implementa la lógica para mostrar detalles de un investigador por su ORCID
    }
}
