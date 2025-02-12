<?php 
include './db2.php'; 

$no = $_GET['routeNo']; 
$drcType = $_GET['drcType']; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 현재 페이지 번호
$itemsPerPage = 10; // 페이지당 항목 수
$pagesToShow = 5; // 페이지네이션에서 표시할 페이지 수

header('Content-Type: text/html; charset=utf-8');

// URL 설정
$url = 'https://openapi.its.go.kr:9443/trafficInfo';
$params = [
    'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4',
    'type' => 'ex',
    'routeNo' => $no,
    'drcType' => $drcType,
    'getType' => 'xml',
];

$urlWithParams = $url . '?' . http_build_query($params);

// 데이터 가져오기
$response = file_get_contents($urlWithParams);
if ($response === false) {
    echo '<p>API 호출에 실패했습니다.</p>';
    exit;
}

$fields = [];
// $xml = new SimpleXMLElement($response);

// // XML에서 항목을 배열로 변환
// if ($xml->body->items) {
//     foreach ($xml->body->items->item as $field) {
//         $fieldArray = [];
//         foreach ($field as $key => $value) {
//             $fieldArray[$key] = (string) $value;
//         }
//         $fields[] = $fieldArray;

//         // 150개 항목만 수집
//         if (count($fields) >= 150) {
//             break;
//         }
//     }
// }
$roadNames = [];
$roadDrcTypes = [];
$speeds = [];
$travelTimes = [];
$linkIds = [];

$xml = new SimpleXMLElement($response);
if ($xml->body->items) {
    foreach ($xml->body->items->item as $field) {
        $fieldArray = [];
        foreach ($field as $key => $value) {
            $fieldArray[$key] = (string) $value;
        }
        $fields[] = $fieldArray;
        $roadNames[] = $fieldArray['roadName'];
        $roadDrcTypes[] = $fieldArray['roadDrcType'];
        $speeds[] = $fieldArray['speed'];
        $travelTimes[] = $fieldArray['travelTime'];
        $linkIds[] = $fieldArray['linkId'];
    }
}

// 데이터베이스에서 링크 정보 가져오기
$linkMap = [];
if (!empty($linkIds)) {
    $placeholders = implode(',', array_fill(0, count($linkIds), '?'));
    $sql = "SELECT * FROM highway WHERE link_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($linkIds);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $linkMap[$row['link_id']] = [
            'node_name_st' => $row['node_name_st'],
            'node_name_end' => $row['node_name_end']
        ];
    }
}

// 중복된 도로 이름 및 방향을 제거
$uniqueRoadNames = array_unique($roadNames);

// 페이지네이션을 위한 데이터 분할
$totalItems = count($fields);
$totalPages = ceil($totalItems / $itemsPerPage);
$offset = ($page - 1) * $itemsPerPage;
$fieldsForPage = array_slice($fields, $offset, $itemsPerPage);

// 현재 페이지 그룹 계산
$groupStart = max(1, floor(($page - 1) / $pagesToShow) * $pagesToShow + 1);
$groupEnd = min($totalPages, $groupStart + $pagesToShow - 1);

// 페이지네이션 링크 생성
$prevGroup = max(1, $groupStart - $pagesToShow);
$nextGroup = min($totalPages, $groupEnd + 1);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include "./front_header.php"; ?>
    <link rel="stylesheet" href="./css/live_sub.css">
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
        <div class="select_box">
            <!-- 드롭다운 버튼 -->
            <button id="dropdownBtn">
                <?php if (!empty($roadNames) && !empty($roadDrcTypes)): ?>
                    <?php echo $roadNames[0]; ?> - <?php echo $roadDrcTypes[0]; ?>
                <?php else: ?>
                    상행
                <?php endif; ?>
            </button>

            <!-- 드롭다운 메뉴: 고유한 도로 이름만 표시 -->
            <div id="dropdownMenu" class="dropdown-content">
                <?php foreach ($uniqueRoadNames as $uniqueRoadName): ?>
                    <a href="./live_sub.php?routeNo=<?php echo $no; ?>&drcType=up&name=<?php echo $uniqueRoadName; ?>">
                        <?php echo $uniqueRoadName; ?> - 상행
                    </a>
                    <a href="./live_sub.php?routeNo=<?php echo $no; ?>&drcType=down&name=<?php echo $uniqueRoadName; ?>">
                        <?php echo $uniqueRoadName; ?> - 하행
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="data-list">
            <?php if (empty($fieldsForPage)): ?>
                <p>데이터가 없습니다.</p>
            <?php else: ?>
                <?php foreach ($fieldsForPage as $index => $field): ?>
                    <div class="box">
                        <div class="top">
                            <!-- roadDrcType에 따라 up 또는 down 클래스를 추가하여 배경색 적용 -->
                            <span class="updown <?php echo ($roadDrcTypes[$offset + $index] == 'up') ? 'up' : 'down'; ?>">
                                <?php echo $roadDrcTypes[$offset + $index]; ?>
                            </span>
                            <p class="roadname"><?php echo isset($linkMap[$field['linkId']]) ? $linkMap[$field['linkId']]['node_name_st'] . ' - ' . $linkMap[$field['linkId']]['node_name_end'] : 'N/A'; ?></p>
                        </div>
                        <div class="details">
                            <span class="speed">속도 : <?php echo $speeds[$offset + $index]; ?> km/h</span><br>
                            <!-- travelTime을 시간과 분으로 변환 -->
                            <?php
                                $hours = floor($travelTimes[$offset + $index] / 60);
                                $minutes = $travelTimes[$offset + $index] % 60;
                            ?>
                            <span class="time">
                            시간 : 
                                <?php 
                                if ($hours > 0) {
                                    echo $hours . "시간 ";
                                }
                                echo $minutes . "분";
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 페이지네이션 -->
        <div class="pagination">
            <?php if ($totalPages > 1): ?>
                <?php if ($groupStart > 1): ?>
                    <a href="?routeNo=<?php echo $no; ?>&drcType=<?php echo $drcType; ?>&page=<?php echo $prevGroup; ?>"><</a>
                <?php endif; ?>

                <?php for ($i = $groupStart; $i <= $groupEnd; $i++): ?>
                    <a href="?routeNo=<?php echo $no; ?>&drcType=<?php echo $drcType; ?>&page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($groupEnd < $totalPages): ?>
                    <a href="?routeNo=<?php echo $no; ?>&drcType=<?php echo $drcType; ?>&page=<?php echo $nextGroup; ?>">></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        </main>
    </div>
</body>
<?php include 'footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('dropdownBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');
    
    // PHP에서 첫 번째 값을 자바스크립트 변수로 전달
    const firstRoadName = "<?php echo !empty($roadNames) ? $roadNames[0] : ''; ?>";
    const firstDrcType = "<?php echo !empty($roadDrcTypes) ? $roadDrcTypes[0] : ''; ?>";

    // 버튼 텍스트 설정
    dropdownBtn.textContent = firstRoadName + " (" + firstDrcType + ")";

    dropdownBtn.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownBtn.classList.toggle('on');
        dropdownMenu.classList.toggle('on');
    });

    window.addEventListener('click', function() {
        dropdownBtn.classList.remove('on');
        dropdownMenu.classList.remove('on');
    });
});

</script>
</html>
