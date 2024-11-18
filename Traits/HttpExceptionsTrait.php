<?php

namespace Modules\Acl\Traits;

use Illuminate\Http\JsonResponse;

trait HttpExceptionsTrait
{
    protected function success($message = 'Action Completed!!', $data = [], $code = 200): jsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function fail($message = 'Action Completed!!', $data = [], $code = 500): jsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $data
        ], $code);
    }
}
