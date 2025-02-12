<?php
    header('Content-Type: application/json');

    $url = 'https://openapi.its.go.kr:9443/cctvInfo';
 // XML 데이터를 가져올 URL
    $params = [
        'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4',
        'type' => 'ex',
        'cctvType' => '1',
        'getType' => 'xml',
        // 'minX' => $_GET['minX'],
        // 'maxX' => $_GET['maxX'],
		// 'minY' => $_GET['minY'],
		// 'maxY' => $_GET['maxY']
        'minX' => '126.800000',
        'maxX' => '127.890000',
		'minY' => '34.900000',
		'maxY' => '35.100000'
    ];

    $urlWithParams = $url . '?' . http_build_query($params);

    $response = file_get_contents($urlWithParams);

    if ($response === false) {
        echo json_encode(['error' => 'XML 데이터를 가져오는데 실패했습니다.']);
    } else {
        $xml = new SimpleXMLElement($response);
        $fields = [];
        foreach ($xml->data as $field) {
            $fieldArray = [];
            foreach ($field as $key => $value) {
                $fieldArray[$key] = (string) $value;
            }
            $fields[] = $fieldArray;

        }

        echo json_encode($fields);
    }
?>
