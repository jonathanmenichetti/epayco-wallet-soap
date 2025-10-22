<?php

namespace App\Services;

use App\Models\Client;

class WalletSoapService
{
    /**
     * Recargar billetera
     * @param string $document
     * @param string $phone  
     * @param float $amount
     * @return array
     */
    public function topUpWallet($document, $phone, $amount)
    {
        try {
            logger([
                'documento' => $document,
                'celular' => $phone,
                'monto' => $amount,
            ]);

            $client = Client::where('document', $document)
                ->where('phone', $phone)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            logger('Client found', ['client_id' => $client->id, 'balance_before' => $client->balance]);

            // Actualizar el balance del cliente
            $client->balance += $amount;
            $client->save();

            logger('Client balance updated', ['client_id' => $client->id, 'balance_after' => $client->balance]);

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'message' => 'Wallet topped up successfully',
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

    /**
     * Consultar saldo de la billetera
     * @param string $document
     * @param string $phone  
     * @return array
     */
    public function checkBalance($document, $phone)
    {
        try {
            logger([
                'documento' => $document,
                'celular' => $phone,
            ]);

            $client = Client::where('document', $document)
                ->where('phone', $phone)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            logger('Client found', ['client_id' => $client->id, 'balance' => $client->balance]);

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'balance' => $client->balance,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            logger('Consulta error', ['error' => $e->getMessage()]);
            $result = [
                'success' => false,
                'message_error' => $e->getMessage(),
            ];
            return $result;
        }
    }

    // makePayment

    // confirmPayment
}
