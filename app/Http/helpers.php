<?php
use Illuminate\Support\Facades\Http;
function getUser($userId) {
     $url = env('SERVICE_USER_URL').'user/'.$userId;

     try {
        $response = Http::timeout(10)->get($url);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;

     } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
     }
}

function getUserById($userIds = []) {
    $url = env('SERVICE_USER_URL').'user/';

    try {
        if(count($userIds) === 0) {
            return [
                'status' => 'success',
                'http_code' => 200,
                'data' => []
            ];
        }

        $response = Http::timeout(10)->get($url, ['user_ids[]' => $userIds]);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;

    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}
