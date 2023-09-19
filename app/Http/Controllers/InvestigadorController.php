<?php

namespace App\Http\Controllers;

use App\Models\Investigador;
use SimpleXMLElement;
use App\Http\Resources\InvestigadorCollection;

class InvestigadorController extends Controller
{
    public function create($orcid)
    {
        try {
            // Configurar la URL de la solicitud
            $url = "https://pub.orcid.org/v3.0/$orcid";
    
            // Inicializar una sesión cURL
            $ch = curl_init($url);
    
            // Configurar opciones de cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/xml', // Solicitar XML
            ]);
    
            // Realizar la solicitud
            $response = curl_exec($ch);
    
            // Verificar si la solicitud fue exitosa
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                // Crear un objeto SimpleXMLElement desde la respuesta XML
                $xml = new SimpleXMLElement($response);
    
                // // Registrar los espacios de nombres
    
                $xml->registerXPathNamespace('pd', 'http://www.orcid.org/ns/personal-details');
                $xml->registerXPathNamespace('kw', 'http://www.orcid.org/ns/keywords');
                $xml->registerXPathNamespace('email', 'http://www.orcid.org/ns/email');
    
                // Utilizar XPath para obtener los datos necesarios
                $pathElements = $xml->xpath('//common:path');
                $orcidPath = !empty($pathElements) ? (string)$pathElements[0] : '';

                $givenNamesElements = $xml->xpath('//pd:given-names');
                $givenNames = !empty($givenNamesElements) ? (string)$givenNamesElements[0] : '';

                $familyNameElements = $xml->xpath('//pd:family-name');
                $familyName = !empty($familyNameElements) ? (string)$familyNameElements[0] : '';

    
                // Obtener palabras clave
                $keywords = [];
                foreach ($xml->xpath('//keyword:keywords/keyword:keyword/keyword:content') as $keywordElement) {
                    $keywords[] = (string)$keywordElement;
                }
    
                // Obtener el correo principal evaluando si primary es true
                $emailElements = $xml->xpath('//email:email[@primary="true"]/email:email');
                $correoPrincipal = !empty($emailElements) ? (string)$emailElements[0] : '';
    
                // Crear un nuevo registro de Investigador
                $investigador = new Investigador;
                $investigador->orcid = $orcidPath;
                $investigador->nombre = $givenNames;
                $investigador->apellido = $familyName;
                $investigador->palabras_clave = json_encode($keywords);
                $investigador->correo_principal = $correoPrincipal;
                $investigador->save();
    
                // Devolver una respuesta exitosa
                return response()->json(['message' => 'Éxito', 'data' => 'Datos guardados correctamente'], 200);
            } else {
                // Si la solicitud no fue exitosa, devolver un mensaje de error
                return response()->json(['message' => 'ORCID no encontrado'], 404);
            }
        } catch (Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir durante la solicitud
            return response()->json(['message' => 'Error al procesar la solicitud'], 500);
        } finally {
            // Cerrar la sesión cURL
            curl_close($ch);
        }
    }

    public function list()
    {
        // Paginar de 2 en 2 deacuerdo al collection y resoruce que se creo enhttp/resources con su respectivo comando
        $investigadores = Investigador::paginate(2); 

        return new InvestigadorCollection($investigadores);
    }
    
    public function delete($orcid)
    {
        // Validar el formato del parámetro ORCID, se saco de internet
        if (!preg_match('/^\\d{4}-\\d{4}-\\d{4}-\\d{3}[0-9X]$/', $orcid)) {
            return response()->json(['message' => 'Formato ORCID no válido'], 400);
        }

        // Intentar eliminar el investigador con el ORCID proporcionado
        $investigador = Investigador::where('orcid', $orcid)->first();

        if (!$investigador) {
            return response()->json(['message' => 'ORCID no encontrado'], 404);
        }

        // Eliminar el investigador
        $investigador->delete();

        return response()->json(['message' => 'Investigador eliminado correctamente'], 200);

    }

    public function detail($orcid)
    {
        // Validar el formato del parámetro ORCID se saca de inernet
        if (!preg_match('/^\\d{4}-\\d{4}-\\d{4}-\\d{3}[0-9X]$/', $orcid)) {
            return response()->json(['message' => 'Formato ORCID no válido'], 400);
        }

        // Obtener los detalles del investigador con el ORCID proporcionado
        $investigador = Investigador::where('orcid', $orcid)->first();

        if (!$investigador) {
            return response()->json(['message' => 'ORCID no encontrado'], 404);
        }

        // Construir una respuesta JSON con los detalles del investigador
        $response = [
            'orcid' => $investigador->orcid,
            'nombre' => $investigador->nombre,
            'apellido' => $investigador->apellido,
            'correo' => $investigador->correo_principal,
            'palabras_clave' => json_decode($investigador->palabras_clave),
        ];

        return response()->json($response, 200);

        }
}
