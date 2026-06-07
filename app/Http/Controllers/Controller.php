<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse(array $data = [], $message = 'Success', $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse($message = 'Error', $statusCode = 500, $details = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }
}
