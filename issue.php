<?php
session_start();
include './db2.php'; 

header('Content-Type: text/html; charset=utf-8');

// 좌표값 가져오기
$xFilter = isset($_GET['x']) ? $_GET['x'] : null;
$yFilter = isset($_GET['y']) ? $_GET['y'] : null;

// URL 설정
$url = 'https://openapi.its.go.kr:9443/eventInfo';
$params = [
    'apiKey' => 'c46f535c226d43d8b54df41aa72c1ca4',
    'type' => 'ex',
    'eventType' => 'all',
    'getType' => 'xml',
];

$urlWithParams = $url . '?' . http_build_query($params);

// 데이터 가져오기 (try-catch 사용)
$response = @file_get_contents($urlWithParams);
if ($response === false) {
    echo '<p>API 호출에 실패했습니다. 나중에 다시 시도해주세요.</p>';
    exit;
}

$fields = [];
$eventType = []; // 이벤트 종류
$roadName = []; // 도로 이름
$type = []; // 고속도로
$eventDetailType = []; // 작업
$startDate = []; // 시작일시
$message = []; // 내용
$roadDrcType = []; // 종점
$coordX = []; // X 좌표
$coordY = []; // Y 좌표

$xml = new SimpleXMLElement($response);
if ($xml->body->items) {
    foreach ($xml->body->items->item as $field) {
        $fieldArray = [];
        foreach ($field as $key => $value) {
            $fieldArray[$key] = (string) $value;
        }
        // 필터링 적용
        if (($xFilter === null || $fieldArray['coordX'] === $xFilter) &&
            ($yFilter === null || $fieldArray['coordY'] === $yFilter)) {
            $fields[] = $fieldArray;
            $eventType[] = $fieldArray['eventType'];
            $roadName[] = $fieldArray['roadName'];
            $type[] = $fieldArray['type'];
            $eventDetailType[] = $fieldArray['eventDetailType'];
            $startDate[] = $fieldArray['startDate'];
            $message[] = $fieldArray['message'];
            $roadDrcType[] = $fieldArray['roadDrcType'];
            $coordX[] = $fieldArray['coordX']; // X 좌표 추가
            $coordY[] = $fieldArray['coordY']; // Y 좌표 추가
        }
    }
}

// 중복된 이벤트 종류 제거
$uniqueEventTypes = array_unique($eventType);

// 모든 이벤트 데이터를 JSON으로 변환하여 자바스크립트로 전달
$fieldsJson = json_encode($fields);

// 북마크 상태를 확인하기 위한 함수
function isBookmarked($x, $y) {
    if (isset($_SESSION['likedItems'])) {
        foreach ($_SESSION['likedItems'] as $item) {
            if ($item['x'] === $x && $item['y'] === $y && $item['type'] === 'issue') {
                return true;
            }
        }
    }
    return false;
}
$i = 1;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include "./front_header.php"; ?>
    <link rel="stylesheet" href="./css/issue.css">
    <style>
        /* 추가적인 스타일은 여기에 작성 */
    </style>
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
        <!-- 이벤트 종류 목록 -->
        <div class="event-types">
            <ul id="eventTypeList">
                <li id="event-all" class="event-type" data-event-type="all">전체</li>
                <?php foreach ($uniqueEventTypes as $uniqueEventType): ?>
                    <li id="" class="event-type event-<?php echo htmlspecialchars($uniqueEventType); ?>" data-event-type="<?php echo htmlspecialchars($uniqueEventType); ?>">
                        <?php echo htmlspecialchars($uniqueEventType); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="ads_wrap ads_main_sm"> 
            <ins class="adsbygoogle"
                style="display: block;"
                data-language="ko"
                data-ad-client="ca-pub-2858778486116301"
                data-ad-slot="7913086329"
            ></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <div class="data-list" id="dataList">
            <?php if (empty($fields)): ?>
                <p>이벤트 데이터가 없습니다.</p>
            <?php else: ?>
                <?php foreach ($fields as $index => $field): ?>
                    <?php
                    if (isset($_GET['x']) && isset($_GET['y'])) {
                        $x = $_GET['x'];
                        $y = $_GET['y'];
                        echo '
                        <div class="box on" data-coordx="' . $x . '" data-coordy="' . $y . '"
                            data-event-type="' . $field['eventType'] . '"
                            data-x="' . $field['coordX'] . '"
                            data-y="' . $field['coordY'] . '"
                            id="event-' . $field['roadName'] . '">
                            <div class="left">
                                <span class="event-type event-' . $field['eventType'] . '" id="event-' . $field['eventType'] . '">
                                    ' . $field['eventType'] . '
                                </span>
                                <p class="roadname">
                                    ' . $field['roadName'] . '
                                </p>
                            </div>
                            <div class="right">
                                <button type="button" class="like-btn"></button>
                            </div>
                        </div>';
                    } else {
                        echo '
                        <div class="box ' . $isBookmarked . '"
                            data-event-type="' . $field['eventType'] . '"
                            data-x="' . $field['coordX'] . '"
                            data-y="' . $field['coordY'] . '"
                            id="event-' . $field['roadName'] . '">
                            <div class="left">
                                <span class="event-type event-' . $field['eventType'] . '" id="event-' . $field['eventType'] . '">
                                    ' . $field['eventType'] . '
                                </span>
                                <p class="roadname">
                                    ' . $field['roadName'] . '
                                </p>
                            </div>
                            <div class="right">
                                <button type="button" class="like-btn"></button>
                            </div>
                        </div>';
                    }
                    // if($i % 3 == 0) {
                    //     echo '
                    //     <div class="ads_wrap ads_main_sm">
                    //         <ins class="adsbygoogle"
                    //             style="display: block;"
                    //             data-language="ko"
                    //             data-ad-client="ca-pub-2858778486116301"
                    //             data-ad-slot="7913086329"
                    //             ></ins>
                    //         <script>
                    //             (adsbygoogle = window.adsbygoogle || []).push({});
                    //         </script>
                    //     </div>

                    //     ';
                    // }
                  
                    $i++;
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        </main>
    </div>

    <!-- 모달 창 HTML 추가 -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h2>
                <div class="left">
                    <span id="modalEventType"></span>
                    <span>고속도로</span>
                </div>
                <span class="close"><img src="./img/close-btn.png" alt="닫기 버튼"></span>
            </h2>
            <div id="modalDetails">
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataList = document.getElementById('dataList');
        const eventTypeList = document.getElementById('eventTypeList');
        const modal = document.getElementById('eventModal');
        const modalContent = document.getElementById('modalDetails');
        const modalEventType = document.getElementById('modalEventType');
        const closeModal = document.querySelector('.close');
        let fields = <?php echo $fieldsJson; ?>; // PHP에서 전달된 JSON 데이터
        let currentEventType = 'all';
        const likedItems = <?php echo isset($_SESSION['likedItems']) ? json_encode($_SESSION['likedItems']) : '[]'; ?>;
        // 필터링 함수
        function filterEvents() {
        const items = dataList.querySelectorAll('.box');
        items.forEach(item => {
            const itemType = item.dataset.eventType;
            const x = item.dataset.x;
            const y = item.dataset.y;
            const isLiked = likedItems.some(likedItem => likedItem.x === x && likedItem.y === y && likedItem.type === 'issue');
            if (currentEventType === 'all' || itemType === currentEventType) {
                item.style.display = 'flex';
                if (isLiked) {
                    item.classList.add('on');
                } else {
                    item.classList.remove('on');
                }
            } else {
                item.style.display = 'none';
            }
        });
    }

        // 이벤트 타입 클릭 시 필터링
        eventTypeList.addEventListener('click', function(event) {
            if (event.target && event.target.matches('li.event-type')) {
                currentEventType = event.target.dataset.eventType;
                filterEvents(); // 필터링 호출
            }
        });

        // 전역 변수로 modalAddClass 선언
        let modalAddClass = '';

        function formatDateTime(datetime) {
        if (datetime.length === 14) { // 형식이 "YYYYMMDDHHmmss"인 경우
            const year = datetime.substring(0, 4);
            const month = datetime.substring(4, 6);
            const day = datetime.substring(6, 8);
            const hour = datetime.substring(8, 10);
            const minute = datetime.substring(10, 12);
            const second = datetime.substring(12, 14);

            return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
        }
        return datetime; // 형식이 다를 경우 원래 문자열 반환
    }

           // 클릭한 이벤트의 상세 정보를 모달에 추가
            dataList.addEventListener('click', function(event) {
                if (event.target && event.target.closest('.box')) {
                    const box = event.target.closest('.box');
                    const x = box.dataset.x;
                    const y = box.dataset.y;
                    modalAddClass = 'event-' + box.dataset.eventType;

                    // 이벤트 데이터 찾기
                    const eventData = fields.find(field => field.coordX === x && field.coordY === y);
                    if (eventData) {
                        modalEventType.textContent = eventData.eventType;
                        modalEventType.classList.add(modalAddClass);
                        modalContent.innerHTML = `
                            <table>
                            <tr>
                                <td><strong>시작 일시</strong></td>
                                <td>${formatDateTime(eventData.startDate)}</td>
                            </tr>
                            <tr>
                                <td><strong>돌발 유형</strong></td>
                                <td>${eventData.eventDetailType}</td>
                            </tr>
                            <tr>
                                <td><strong>내용</strong></td>
                                <td>${eventData.message}</td>
                            </tr>
                            <tr>
                                <td><strong>위치</strong></td>
                                <td>${eventData.roadName} (${eventData.roadDrcType})</td>
                            </tr>
                            </table>
                        `;
                        modal.style.display = 'block'; // 모달 표시
                    }
                }
            });

            closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
            if (modalAddClass) {
                modalEventType.classList.remove(modalAddClass);
                modalAddClass = ''; // 변수 초기화
            }
            });

        // 모달 닫기 버튼 클릭 시 모달 닫기
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
            if (modalAddClass) {
                modalEventType.classList.remove(modalAddClass);
                modalAddClass = ''; // 변수 초기화
            }
        });


        // 모달 외부 클릭 시 모달 닫기
        window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            if (modalAddClass) {
            modalEventType.classList.remove(modalAddClass);
            modalAddClass = ''; // 변수 초기화
            }
        }
        });

    // 찜 버튼 클릭 처리
    $('.like-btn').on('click', function(event) {
            event.preventDefault(); // 기본 동작 방지
            event.stopPropagation(); 

            var btn = $(this);
            var box = btn.closest('.box');
            var roadName = box.find('.roadname').text(); // 도로 이름
            var coordX = box.data('x'); 
            var coordY = box.data('y'); 
            var eventType = box.data('event-type'); // 이벤트 타입

            // 북마크 상태 확인 (on 클래스 유무)
            var isLiked = box.hasClass('on');

            // 서버로 전송할 데이터
            var bookmarkData = {
                type: 'issue', 
                title: roadName, 
                href: '', 
                x: coordX, 
                y: coordY,
                text: ['돌발상황', eventType].join('||').split('||'),
                action: isLiked ? 'remove' : 'add' // 상태에 따라 add 또는 remove 설정
            };

            // AJAX 요청을 통해 북마크 상태 업데이트
            $.ajax({
                url: 'bookmark.php',
                type: 'POST',
                data: bookmarkData,
                success: function(response) {
                    console.log('서버 응답:', response); // 응답 내용 확인
                    try {
                        var data = JSON.parse(response);
                        if (data.status === 'added') {
                            box.addClass('on'); // 북마크 추가 시 on 클래스 부여
                        } else if (data.status === 'removed') {
                            box.removeClass('on'); // 북마크 제거 시 on 클래스 제거
                        }
                    } catch (e) {
                        console.error('JSON 파싱 에러:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('에러 발생: ' + error);
                }
            });
        });
            // 페이지 로드 시 모든 이벤트 표시
            filterEvents();
        });
    </script>
</body>
</html>
