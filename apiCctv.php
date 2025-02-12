<?php
header('Content-Type: application/json');

$region = isset($_GET['region']) ? $_GET['region'] : '';

$minX = '126.781478';
$maxX = '127.087529';
$minY = '37.439346';
$maxY = '37.703139';

// 지역에 따른 범위 설정
switch ($region) {
    case 'seoul':
        $minX = '126.781478';
        $maxX = '127.087529';
        $minY = '37.439346';
        $maxY = '37.703139';
        break;
    case 'busan':
        $minX = '129.005676';
        $maxX = '129.275547';
        $minY = '35.089554';
        $maxY = '35.236875';
        break;
    case 'daegu':
        $minX = '128.557033';
        $maxX = '128.753378';
        $minY = '35.797029';
        $maxY = '36.067123';
        break;
    case 'incheon':
        $minX = '126.438874';
        $maxX = '126.743253';
        $minY = '37.321683';
        $maxY = '37.615077';
        break;
    case 'gwangju':
        $minX = '126.774128';
        $maxX = '126.976913';
        $minY = '35.115673';
        $maxY = '35.347273';
        break;
    case 'daejeon':
        $minX = '127.370301';
        $maxX = '127.578184';
        $minY = '36.266307';
        $maxY = '36.485897';
        break;
    case 'ulsan':
        $minX = '129.232564';
        $maxX = '129.348483';
        $minY = '35.452198';
        $maxY = '35.550521';
        break;
    case 'sejong':
        $minX = '127.232960';
        $maxX = '127.384870';
        $minY = '36.485133';
        $maxY = '36.669550';
        break;
    case 'gyeonggi':
        $minX = '126.553371';
        $maxX = '127.253639';
        $minY = '37.050282';
        $maxY = '37.777243';
        break;
    case 'gangwon':
        $minX = '127.614596';
        $maxX = '129.116610';
        $minY = '37.549046';
        $maxY = '38.727926';
        break;
    case 'chungbuk':
        $minX = '127.138978';
        $maxX = '127.606482';
        $minY = '36.319069';
        $maxY = '37.129863';
        break;
    case 'chungnam':
        $minX = '126.446056';
        $maxX = '127.136020';
        $minY = '36.198861';
        $maxY = '37.050140';
        break;
    case 'jeonbuk':
        $minX = '126.426064';
        $maxX = '127.369649';
        $minY = '35.319875';
        $maxY = '36.272492';
        break;
    case 'jeonnam':
        $minX = '126.073181';
        $maxX = '127.335506';
        $minY = '34.711078';
        $maxY = '35.579790';
        break;
    case 'gyeongbuk':
        $minX = '128.183114';
        $maxX = '129.341517';
        $minY = '35.785708';
        $maxY = '36.671468';
        break;
    case 'gyeongnam':
        $minX = '128.675088';
        $maxX = '129.377939';
        $minY = '35.060915';
        $maxY = '35.964087';
        break;
    case 'jeju':
        $minX = '126.195852';
        $maxX = '126.783749';
        $minY = '33.098347';
        $maxY = '33.542283';
        break;
}

$ch = curl_init();
$url = 'https://openapi.its.go.kr:9443/cctvInfo';
$queryParams = '?' . http_build_query([
    'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4',
    'type' => 'ex',
    'cctvType' => '2',
    'minX' => $minX,
    'maxX' => $maxX,
    'minY' => $minY,
    'maxY' => $maxY,
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

$xml = simplexml_load_string($response);
if ($xml === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to parse XML']);
    exit;
}

$json = json_encode($xml);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to encode JSON']);
    exit;
}

echo $json;
?>
