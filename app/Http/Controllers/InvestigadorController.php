<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Investigador;
use GuzzleHttp\Client;
use SimpleXMLElement;

class InvestigadorController extends Controller
{

    public function create($orcid)
    {
        try {
            // Crear una instancia de Guzzle, debo de estudiar esta libreria
            $client = new Client();

            // Realizar la peticio a orcid segun documentacion
            $response = $client->get("https://pub.orcid.org/v3.0/$orcid");

            // Verificar si la peticion fue exitosa 
            if ($response->getStatusCode() == 200) {
                // Obtener el contenido de la respuesta XML
                $xmlContent = $response->getBody()->getContents();

                // Crear un espacio de nombres para los elementos ORCID , esto se saco de internet porque no funcionaba estudiar!!
                $xml = new SimpleXMLElement($xmlContent);
                $xml->registerXPathNamespace('pd', 'http://www.orcid.org/ns/personal-details');
                $xml->registerXPathNamespace('kw', 'http://www.orcid.org/ns/keywords');
                $xml->registerXPathNamespace('email', 'http://www.orcid.org/ns/email');

                // Utilizar XPath con los nombres registrados
                $orcidPath = (string)$xml->xpath('//common:path')[0];
                $givenNames = (string)$xml->xpath('//pd:given-names')[0];
                $familyName = (string)$xml->xpath('//pd:family-name')[0];

                // Obtener palabras clave 
                $keywords = [];
                foreach ($xml->xpath('//keyword:keywords/keyword:keyword/keyword:content') as $keywordElement) {
                    $keywords[] = (string)$keywordElement;
                }

                // Obtener el correo principal evaluando si primary es true
                $correoPrincipalElement = $xml->xpath('//email:email[@primary="true"]/email:email')[0];
                $correoPrincipal = (string)$correoPrincipalElement;

                // Crear un nuevo registro de Investigador
                $investigador = new Investigador;
                $investigador->orcid = $orcidPath;
                $investigador->nombre = $givenNames;
                $investigador->apellido = $familyName;
                $investigador->palabras_clave = json_encode($keywords);
                $investigador->correo_principal = $correoPrincipal;
                $investigador->save();

                // Devolver una respuesta existo
                return response()->json(['message' => 'Éxito', 'data' => 'Datos guardados correctamente'], 200);
            } else {
                // Si la solicitud no fue exitosa devolver un mensaje de error
                return response()->json(['message' => 'ORCID no encontrado'], 404);
            }
        } catch (Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir durante la solicitud
            return response()->json(['message' => 'Error al procesar la solicitud'], 500);
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
