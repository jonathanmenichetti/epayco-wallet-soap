<?php

namespace App\Http\Controllers;

use Laminas\Soap\Server as SoapServer;
use App\Services\ClientSoapService;
use Laminas\Soap\AutoDiscover as WsdlGenerator;

use function PHPUnit\Framework\isNull;

class ClientSoapController extends Controller
{
    public function wsdl()
    {
        if (!request()->has('wsdl')) {
            abort(404);
        }
        // Generate and return the WSDL
        $wsdl = new WsdlGenerator();
        $wsdl->setUri(url('/api/soap/clients'))
            ->setServiceName('ClientService')
            ->setClass(ClientSoapService::class)
            ->setBindingStyle([
                'cache_wsdl' => WSDL_CACHE_NONE,
                'style' => 'rpc',
            ]);

        return response($wsdl->toXml(), 200, [
            'Content-Type' => 'text/xml; charset=utf-8',
        ]);
    }

    public function server()
    {
        // DEBUG: Ver qué está pasando
        logger('SOAP Request received!!!', [
            'input' => file_get_contents('php://input')
        ]);

        // Create a new SOAP server instance
        $server = new SoapServer(url('/api/soap/clients?wsdl'), [
            'uri' => url('/api/soap/clients'),
            'cache_wsdl' => WSDL_CACHE_NONE,
            // 'style' => SOAP_DOCUMENT,
            // 'use' => SOAP_LITERAL
        ]);

        // Set the class that contains the SOAP methods
        $server->setClass(ClientSoapService::class);

        logger('SOAP Server initialized', []);

        $response = $server->handle();
        // ob_start();
        // $server->handle();
        // $response = ob_get_clean();

        logger('SOAP Response captured:', ['response' => $response]);

        return response($response, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
