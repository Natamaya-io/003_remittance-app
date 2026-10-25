<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$endpoint = 'https://www.bi.go.id/biwebservice/wskursbi.asmx/getSubKursLokal4';
$timezone = new DateTimeZone('Asia/Jakarta');
$cursor = new DateTimeImmutable('today', $timezone);

if (!function_exists('curl_init')) {
    exit("ERROR: ekstensi cURL belum aktif. Aktifkan extension=curl di php.ini.\n");
}

for ($attempt = 0; $attempt < 14; $attempt++) {
    if ((int) $cursor->format('N') > 5) {
        $cursor = $cursor->modify('-1 day');
        continue;
    }

    $date = $cursor->format('Y-m-d');
    echo "Menguji Kurs Transaksi BI tanggal {$date}...\n";

    $curl = curl_init($endpoint);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['startdate' => $date]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 12,
        CURLOPT_TIMEOUT => 35,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => 'RemitGo-Diagnostic/22.0',
        CURLOPT_HTTPHEADER => [
            'Accept: application/xml, text/xml',
            'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);

    echo "HTTP: {$status}\n";

    if ($response === false) {
        echo "cURL error: {$error}\n\n";
        $cursor = $cursor->modify('-1 day');
        continue;
    }

    $body = trim((string) $response);
    if ($status >= 200 && $status < 300 && stripos($body, '<Table') !== false) {
        echo "SUKSES: data BI ditemukan. Panjang respons: " . strlen($body) . " byte.\n";
        exit(0);
    }

    echo "Belum ada data pada tanggal ini.\n\n";
    $cursor = $cursor->modify('-1 day');
}

exit("ERROR: data BI tidak ditemukan dalam rentang pengujian.\n");
