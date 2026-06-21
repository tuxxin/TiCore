<?php
namespace Tuxxin\TiCore\Addons\RestApiKit\Controllers;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/**
 * Public JSON API behind the 'apikey' middleware. The verified key record is
 * available on $req->param('_apikey') (set by ApiKeyAuth).
 */
final class ApiController
{
    public function ping(Request $req): Response
    {
        return Response::json([
            'ok'      => true,
            'service' => 'rest-api-kit',
            'version' => 'v2',
            'time'    => gmdate('c'),
        ]);
    }

    public function whoami(Request $req): Response
    {
        $key = $req->param('_apikey', []);
        return Response::json([
            'ok'        => true,
            'key_id'    => isset($key['id']) ? (int) $key['id'] : null,
            'key_name'  => $key['name'] ?? null,
            'key_prefix'=> $key['key_prefix'] ?? null,
        ]);
    }
}
