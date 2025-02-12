<?php
header('Content-Type: application/json');

$url = 'https://openapi.its.go.kr:9443/posIncidentInfo';
$params = [
    'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4',
    'minX' => '126.764582', // 대한민국 최서단 경도
    'maxX' => '127.183794', // 대한민국 최동단 경도
    'minY' => '37.413294',  // 대한민국 최남단 위도
    'maxY' => '37.715133',  // 대한민국 최북단 위도
    'getType' => 'xml'
];

$urlWithParams = $url . '?' . http_build_query($params);

$response = file_get_contents($urlWithParams);

if ($response === false) {
    echo json_encode(['error' => 'XML 데이터를 가져오는데 실패했습니다.']);
} else {
    $xml = new SimpleXMLElement($response);
    
    $fields = [];
    if ($xml->body->items) {
        foreach ($xml->body->items->item as $field) {
            $fieldArray = [];
            foreach ($field as $key => $value) {
                $fieldArray[$key] = (string) $value;
            }
            $fields[] = $fieldArray;
        }
    }
    
    echo json_encode($fields);
}
?>
