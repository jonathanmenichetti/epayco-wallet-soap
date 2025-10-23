<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PaymentSession;
use Faker\Provider\ar_EG\Payment;
use Ramsey\Uuid\Uuid;

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

    /**
     * Realizar un pago desde la billetera
     * @param string $document
     * @param string $phone  
     * @param float $amount
     * @return array
     */
    public function makePayment($document, $phone, $amount)
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

            // Validate sufficient balance
            $balance = $client->balance;
            logger('Balance data', ['balanceData' => $balance]);

            if ($balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // Generate session_id
            $sessionId = $this->generateSessionId();
            logger('Generated session_id', ['session_id' => $sessionId]);

            // Generate token
            $token = $this->generateToken();
            logger('Generated token', ['token' => $token]);

            // Create payment session
            $payment = PaymentSession::create([
                'session_id' => $sessionId,
                'client_id' => $client->id,
                'amount' => $amount,
                'token' => $token,
                'status' => 'pending',
            ]);

            // "Enviar" token al email (logger por ahora)
            // Retornar session_id + mensaje

            $result = [
                'success' => true,
                'cod_error' => '00',
                'message_error' => '',
                'data' => [
                    'message' => 'Token enviado al correo',
                    'payment_session' => $payment,
                    // NO retornar el token por seguridad
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

    // confirmPayment


    public function generateSessionId(): string
    {
        // return bin2hex(random_bytes(16));
        return Uuid::uuid4()->toString();
    }

    public function generateToken(): string
    {
        return rand(100000, 999999);
    }
}
