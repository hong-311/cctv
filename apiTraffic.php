<?php
header('Content-Type: application/json');

// cURL 초기화
$ch = curl_init();
$url = 'https://openapi.its.go.kr:9443/trafficInfo';
$queryParams = '?' . http_build_query([
    'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4', // 수정된 부분
    'type' => 'ex',
    'routeNo' => '0301',
    'drcType' => 'all',
    // 'minX' => '126.764582', // 대한민국 최서단 경도
    // 'maxX' => '127.183794', // 대한민국 최동단 경도
    // 'minY' => '37.413294',  // 대한민국 최남단 위도
    // 'maxY' => '37.715133',  // 대한민국 최북단 위도
    'getType' => 'xml'
]);

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data']);
    exit;
}

// Convert XML to JSON
$xml = simplexml_load_string($response);
if ($xml === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to parse XML']);
    exit;
}

// Convert XML to JSON
$json = json_encode($xml);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to encode JSON']);
    exit;
}

echo $json;
?>
