<?php
include './db.php'; 
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start(); // 세션 시작

// 변수 초기화
$no = isset($_GET['no']) ? $_GET['no'] : ''; 
$sql = "SELECT * FROM cctv"; 
$params = [$no];
$result = query($sql, $params)->fetch();
$category = $result['name'];

$name = "";

if (!empty($result)) {
    $name = $result['name'];
}

// 북마크 상태를 확인하기 위한 함수
function isBookmarked($name) {
    if (isset($_SESSION['likedItems'])) {
        foreach ($_SESSION['likedItems'] as $item) {
            if ($item['title'] === $name && $item['type'] === 'live' ) {
                return true;
            }
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include "./front_header.php"; ?>
    <link rel="stylesheet" href="./css/live.css">
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
            <div class="section_<?php echo htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?>">
                <?php
                $sql2 = "SELECT * FROM cctv";
                $result2 = query($sql2)->fetchAll();
                
                echo '<div class="btn_group">';
                foreach ($result2 as $key => $val) {
                    $isBookmarked = isBookmarked($val['name']) ? 'on' : '';
                    
                    echo '<a class="box ' . htmlspecialchars($isBookmarked, ENT_QUOTES, 'UTF-8') . '" href="./live_sub.php?routeNo=' . urlencode($val['no']) . '&drcType=up&name=' . urlencode($val['name']) . '" data-road="' . htmlspecialchars($val['name'], ENT_QUOTES, 'UTF-8') . '">';
                    echo '<div class="left">';
                    echo '<span class="name">' . htmlspecialchars($val['name'], ENT_QUOTES, 'UTF-8') . '</span>';
                    echo '</div>';
                    echo '<div class="right">';
                    echo '<button type="button" class="like-btn" data-title="' . htmlspecialchars($val['name'], ENT_QUOTES, 'UTF-8') . '" data-href="./live_sub.php?routeNo=' . urlencode($val['no']) . '&drcType=up&name=' . urlencode($val['name']) . '"></button>';
                    echo '<img src="./img/go-btn.png" alt="가기 버튼">';
                    echo '</div>';
                    echo '</a>'; // 'box' a 태그 닫기
                }
                echo '</div>'; // 'btn_group' div 닫기
                ?>
            </div> 
        </main>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 찜 버튼 클릭 시 동작
        $('.like-btn').on('click', function(event) {
            event.preventDefault(); // 기본 동작(페이지 이동) 방지
            var btn = $(this);
            var title = btn.data('title'); // 타이틀
            var href = btn.data('href'); // href 값 가져오기

            // → a태그의 on class 유무 판별
            var isLiked = btn.closest('a').hasClass('on');
            
            // 서버로 전송할 데이터
            var bookmarkData = {
                type: 'live', // live 유형
                title: title,
                href: href, // href 값 추가
                text: '교통정보',
                action: isLiked ? 'remove' : 'add' 
            };

            // AJAX 요청을 통해 찜 상태 업데이트
            $.ajax({
                url: 'bookmark.php',
                type: 'POST',
                data: bookmarkData,
                success: function(response) {
                    console.log('서버 응답:', response); // 응답 내용 확인
                    try {
                        var data = JSON.parse(response);
                        if (data.status === 'added') {
                            // 'on' 클래스 추가
                            btn.closest('a').addClass('on'); // 'on' 클래스 추가
                        } else if (data.status === 'removed') {
                            // 'on' 클래스 제거
                            btn.closest('a').removeClass('on'); // 'on' 클래스 제거
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
    });
    </script>
</body>
</html>
