<?php

namespace App\Services;

use App\Models\Client; // Importar el modelo Client

class ClientSoapService
{
    /**
     * Registro de un nuevo cliente.
     * @param string $document
     * @param string $full_name  
     * @param string $email
     * @param string $phone
     * @return array
     */
    public function registerClient($document, $full_name, $email, $phone)
    {

        try {

            logger([
                'documento' => $document,
                'nombres' => $full_name,
                'email' => $email,
                'celular' => $phone,
            ]);

            // Crear un nuevo cliente en la base de datos
            $client = Client::create([
                'document' => $document,
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
            ]);

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'message' => 'Client registered successfully',
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            logger('Inserción error', ['error' => $e->getMessage()]);
            $result = [
                'success' => false,
                'message_error' => $e->getMessage(),
            ];
            return $result;
        }
    }
}
