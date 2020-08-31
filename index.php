<?php

// set server key yg didapat dari halaman midtrans
$server_key = "SB-Mid-server-QL7x_5hvHaGyghMK_mwqZ3lB";

// variabel environment production 
$is_production = false;

if ($is_production){
    $api_url = "https://app.midtrans.com/snap/v1/transactions";
} else {
    $api_url = "https://app.sandbox.midtrans.com/snap/v1/transactions";
}


// jika url yg diminta tidak ada tulisan '/charge'
if(!strpos($_SERVER['REQUEST_URI'], '/charge')){
    http_response_code(404);
    echo "path tidak ditemukan, mungkin maksud kamu /charge";
    exit();
}

// jika request method != 'POST'
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(404);
    echo "Halaman tidak ditemukan atau request method yg digunakan salah";
    exit();
}

$request_body = file_get_contents('php://input');
                header('Content-Type: application/json');

$charge_result = chargeApi($api_url, $server_key, $request_body);

http_response_code($charge_result['http_code']);
echo $charge_result['body'];

function chargeApi($api_url, $server_key, $request_body){
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        //tambahkan header ke permintaan, termasuk otorisasi yang dihasilkan dari server_key
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic '. base64_encode($server_key . ':')
        ),
        CURLOPT_POSTFIELDS => $request_body
    );
    curl_setopt_array($ch, $curl_options);
    $result = array(
        'body' => curl_exec($ch),
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
    );

    return $result;
}