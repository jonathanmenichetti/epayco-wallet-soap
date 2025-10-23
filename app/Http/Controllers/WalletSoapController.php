<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laminas\Soap\Server as SoapServer;
use App\Services\WalletSoapService;
use Laminas\Soap\AutoDiscover as WsdlGenerator;

use function PHPUnit\Framework\isNull;

class WalletSoapController extends Controller
{
    public function wsdl()
    {
        if (!request()->has('wsdl')) {
            abort(404);
        }

        // Generate and return the WSDL
        $wsdl = new WsdlGenerator();
        $wsdl->setUri(url('/api/soap/wallet'))
            ->setServiceName('WalletService')
            ->setClass(WalletSoapService::class)
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
        // Create a new SOAP server instance
        $server = new SoapServer(url('/api/soap/wallet?wsdl'), [
            'uri' => url('/api/soap/clients'),
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);

        // Set the class that contains the SOAP methods
        $server->setClass(WalletSoapService::class);

        $response = $server->handle();

        return response($response, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
