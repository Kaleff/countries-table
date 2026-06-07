<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse(array $data = [], $message = 'Success', $statusCode = 200, $dataKey = 'data')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if (!empty($data)) {
            $response[$dataKey] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse($message = 'Error', $statusCode = 500, $details = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }
}
