<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PaymentSession;
use Faker\Provider\ar_EG\Payment;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB; // Importar la clase DB para transacciones

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
            // Start transaction
            DB::beginTransaction();

            $client = Client::where('document', $document)
                ->where('phone', $phone)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }


            // Update client balance
            $client->balance += $amount;
            $client->save();


            // Commit transaction
            DB::commit();

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'message' => 'Wallet topped up successfully',
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
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
            $client = Client::where('document', $document)
                ->where('phone', $phone)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'balance' => $client->balance,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
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
            // Start transaction
            DB::beginTransaction();

            $client = Client::where('document', $document)
                ->where('phone', $phone)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            // Validate sufficient balance
            $balance = $client->balance;

            if ($balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // Generate session_id
            $sessionId = $this->generateSessionId();

            // Generate token
            $token = $this->generateToken();

            // Create payment session
            $payment = PaymentSession::create([
                'session_id' => $sessionId,
                'client_id' => $client->id,
                'amount' => $amount,
                'token' => $token,
                'status' => 'pending',
            ]);

            // Commit transaction
            DB::commit();

            $result = [
                'success' => true,
                'cod_error' => '00',
                'message_error' => '',
                'data' => [
                    'message' => 'Payment session created successfully',
                    'client_id' => $payment->client_id,
                    'amount' => $payment->amount,
                    'session_id' => $payment->session_id,
                    'token' => $payment->token,
                ]
            ];
            return $result;
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            $result = [
                'success' => false,
                'message_error' => $e->getMessage(),
            ];
            return $result;
        }
    }

    /**
     * Confirmar un pago desde la billetera
     * @param string $sessionId
     * @param string $token  
     * @return array
     */
    public function confirmPayment($session_id, $token)
    {
        try {
            logger([
                'sessionId' => $session_id,
                'token' => $token,
            ]);

            // Start transaction
            DB::beginTransaction();

            $payment = PaymentSession::where('session_id', $session_id)
                ->where('token', $token)
                ->where('status', 'pending')
                ->first();

            if (!$payment) {
                throw new \Exception('There is no pending payment with the provided session ID and token');
            }

            $client = Client::where('id', $payment->client_id)
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            // Validate sufficient balance
            $balance = $client->balance;

            if ($balance < $payment->amount) {
                throw new \Exception('Insufficient balance');
            }

            // Update client balance
            $client->balance -= $payment->amount;
            $client->save();

            // Update payment session status to confirmed
            $payment->status = 'confirmed';
            $payment->save();

            // Commit transaction
            DB::commit();

            $result = [
                'success' => true,
                'message_error' => '',
                'data' => [
                    'message' => 'Payment confirmed successfully',
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            $result = [
                'success' => false,
                'message_error' => $e->getMessage(),
            ];
            return $result;
        }
    }

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
