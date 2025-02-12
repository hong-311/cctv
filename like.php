<?php
include './db.php'; // 데이터베이스 연결 파일 포함

session_start(); // 세션 시작

// AJAX 요청에 따라 세션 데이터 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
    $index = $_POST['index'];
    
    // 세션에서 해당 항목 삭제
    if (isset($_SESSION['likedItems'][$index])) {
        unset($_SESSION['likedItems'][$index]);
        // 배열의 인덱스 재정렬
        $_SESSION['likedItems'] = array_values($_SESSION['likedItems']);
    }

    // 성공 메시지 전송
    echo json_encode(array('status' => 'success', 'index' => $index));
    exit();
}
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include "./front_header.php"; ?>
    <link rel="stylesheet" href="./css/like.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery 추가 -->
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
        <div class="event-types">
                <ul>
                    <li class="filter" data-type="all">전체</li>
                    <!-- <li class="filter" data-type="cctv">CCTV 영상</li> -->
                    <!-- <li class="filter" data-type="live">교통 정보</li> -->
                    <li class="filter" data-type="issue">돌발 상황</li>
                    <li class="filter" data-type="warn">주의 구간</li>
                </ul>
            </div>
            <div>
                <ins class="adsbygoogle"
                    style="display: block;"
                    data-language="ko"
                    data-ad-client="ca-pub-2858778486116301"
                    data-ad-slot="4047646248"
                    data-ad-format="autorelaxed"
                    data-matched-content-ui-type="image_sidebyside,image_sidebyside"
                    data-matched-content-rows-num="3,3"
                    data-matched-content-columns-num="1,1"
                    ></ins>
                <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
		    </div>
            <ul class="liked-cctv-list">
                <?php
                    // 세션에 찜 항목이 있는 경우에만 출력
                    if (!empty($_SESSION['likedItems'])) {
                        // 북마크 목록 출력
                        foreach ($_SESSION['likedItems'] as $index => $val) {
                            if ($val['type'] === 'cctv') {
                                echo '<li class="' . $val['type'] . '">';
                                echo '<a href="./map.php?x=' . $val['x'] . '&y=' . $val['y'] . '&region=' . $val['text'] . '">';
                                // 3. text에서 값 가져와서 파라미터에 region 값 넣기
                                echo '<div class="box_top">';
                                echo '<span>CCTV 영상</span>';
                                echo '<img class="like-btn" data-id="' . $index . '" src="./img/icon-fill-green.png" alt="찜 버튼">';
                                echo '</div>';
                                echo '<p class="title">' . $val['title'] . '</p>';
                                echo '</a>';
                                echo '</li>';
                            } else if ($val['type'] === 'live') {
                                echo '<li class="' . $val['type'] . '">';
                                echo '<a href="' . $val['href'] . '">';
                                echo '<div class="box_top">';
                                echo '<span>' . $val['text'] . '</span>';
                                echo '<img class="like-btn" data-id="' . $index . '" src="./img/icon-fill-green.png" alt="찜 버튼">';
                                echo '</div>';
                                echo '<p class="title">' . $val['title'] . '</p>';
                                echo '</a>';
                                echo '</li>';
                            } else if ($val['type'] === 'issue') {
                                echo '<li class="' . $val['type'] . '">';
                                echo '<a href="./issue.php?x=' . $val['x'] . '&y=' . $val['y'] . '">';
                                // 'text'가 배열일 경우 처리
                                if (is_array($val['text'])) {
                                    echo '<div class="box_top">';
                                    echo '<div class="left">';
                                    echo '<span class="issue-type">' . $val['text'][0] . '</span>'; // '돌발상황'
                                    echo '<span class="event-type event-' . $val['text'][1] . '">';
                                    echo $val['text'][1]; // 'eventType'
                                    echo '</span>';
                                    echo '</div>';
                                    echo '<img class="like-btn" data-id="' . $index . '" src="./img/icon-fill-green.png" alt="찜 버튼">';
                                    echo '</div>';
                                } else {
                                    echo '<span>' . $val['text'] . '</span>';
                                }
                                echo '<p class="title">' . $val['title'] . '</p>';
                                echo '</a>';
                                echo '</li>';
                            } else if ($val['type'] === 'warn') {
                                echo '<li class="' . $val['type'] . '">';
                                // 'text'가 JSON 문자열로 전달된 경우 처리
                                if (isset($val['text']) && $val['text'] !== '') {
                                    $textArray = json_decode($val['text'], true);
                                    if (is_array($textArray)) {
                                        echo '<div class="box">';
                                        echo '<div class="box_top">';
                                        echo '<div class="left">';
                                        echo '<span class="warn-type">' . $textArray[0] . '</span>';
                                        echo '<span class="outbrkType">' . $textArray[1] . '</span>';
                                        echo '</div>';
                                        echo '<img class="like-btn" data-id="' . $index . '" src="./img/icon-fill-green.png" alt="찜 버튼">';
                                        echo '</div>';

                                        echo '<table>';
                                        echo '<tr class="message-row">';
                                        echo '<td><strong>내용</strong></td>';
                                        echo '<td>' . $textArray[3] . '</td>';
                                        echo '</tr>';
                                        echo '<tr class="location-row">';
                                        echo '<td><strong>위치</strong></td>';
                                        echo '<td>' . $textArray[5] . '</td>';
                                        echo '</tr>';
                                        echo '</table>';
                                        echo '</div>';
                                    } else {
                                        echo '<span>' . $val['text'] . '</span>';
                                    }
                                }
                                echo '<p class="title">' . $val['title'] . '</p>';
                                echo '</a>';
                                echo '</li>';
                            }
                        }
                    } else {
                    }
                ?>
            </ul>
        </main>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        $(document).ready(function() {
            // 페이지 로드 시 모든 항목을 기본적으로 보이도록 설정
            $('.liked-cctv-list li').show();
            
            // 필터 클릭 시 항목 토글
            $('.filter').on('click', function() {
                var filterType = $(this).data('type');
                
                // 모든 필터 항목에서 active 클래스 제거
                $('.filter').removeClass('active');
                
                // 클릭한 필터 항목에 active 클래스 추가
                $(this).addClass('active');
                
                // 모든 항목 숨기기
                $('.liked-cctv-list li').hide();
                
                // 선택한 필터 타입에 맞는 항목만 보이기
                if (filterType === 'all') {
                    $('.liked-cctv-list li').show();
                } else {
                    $('.liked-cctv-list li.' + filterType).show();
                }
            });

            // 찜 버튼 클릭 처리
            $(".like-btn").click(function(event) {
                event.preventDefault(); 
                var button = $(this);
                var id = button.data('id'); // data-id 속성에서 인덱스 값 가져오기
                
                $.ajax({
                    type: "POST",
                    url: "", // 현재 파일에 POST 요청을 보냄
                    data: { index: id },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.status === "success") {
                                button.closest('li').remove(); // 성공하면 목록에서 해당 항목 삭제
                            }
                        } catch (e) {
                            console.error('응답 파싱 오류:', e);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>