<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include "./front_header.php"; ?>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
            <div class="top">
                <div class="left">
                    <img src="./img/icon.png">
                    <span id="eventDetailTypeSpan"></span> <!-- ID 추가 -->
                </div>
                <div class="right">
                    <span id="roadNameSpan"></span>
                </div>
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
            <div class="contents">
                <a class="map movelink" href="./map.php" target="_parent">
                    <img src="./img/map.png" class="map_img">
                    <div class="text-overlay">
                        <img src="./img/pin.png" alt="Pin Icon">
                        CCTV 보기 클릭
                    </div>
                </a>
                <div>
                    <ins class="adsbygoogle"
                        style="display: block;"
                        data-language="ko"
                        data-ad-client="ca-pub-2858778486116301"
                        data-ad-slot="4047646248"
                        data-ad-format="autorelaxed"
                        data-matched-content-ui-type="image_sidebyside,image_sidebyside"
                        data-matched-content-rows-num="2,2"
                        data-matched-content-columns-num="1,1"
                        ></ins>
                    <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <div class="btn_wrap">
                    <div class="left">
                        <a class="btn2 movelink" href="./issue.php" target="_parent" >
                            각종 돌발 정보
                            <span>확인하기</span>
                        </a>
                        </div>
                        <div class="right">
                        <a class="btn3 movelink" href="./warn.php" target="_parent" >
                            주의운전구간 정보
                            <span>교통안전도우미</span>
                        </a>
                        <a class="btn4" href="./like.php">
                            나만의 찜목록
                            <span>확인하기</span>
                        </a>
                    </div>
                </div>
                <div>
                    <ins class="adsbygoogle"
                        style="display: block;"
                        data-language="ko"
                        data-ad-client="ca-pub-2858778486116301"
                        data-ad-slot="1782570823"
                        data-ad-format="autorelaxed"
                        data-matched-content-ui-type="image_sidebyside,image_sidebyside"
                        data-matched-content-rows-num="1,1"
                        data-matched-content-columns-num="1,1"
                        ></ins>
                    <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        </main>
    </div>
</body>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // AJAX 요청을 보냅니다.
        $.ajax({
            url: 'apiEvent.php', // PHP 스크립트 URL
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (Array.isArray(response) && response.length > 0) {
                    // 첫 번째 이벤트의 도로 이름과 이벤트 상세 유형을 출력
                    const roadName = response[0].roadName; // 실제 API 응답 데이터 구조에 따라 조정 필요
                    const eventDetailType = response[0].eventDetailType; // 실제 API 응답 데이터 구조에 따라 조정 필요

                    $('#roadNameSpan').text(roadName);
                    $('#eventDetailTypeSpan').text(eventDetailType + " 정보알림");
                } else {
                    $('#roadNameSpan').text('정보 없음');
                    $('#eventDetailTypeSpan').text('정보 없음');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX 요청 오류:', error);
                $('#roadNameSpan').text('정보 가져오기 실패');
                $('#eventDetailTypeSpan').text('정보 가져오기 실패');
            }
        });
    });
    var moveLink = document.querySelectorAll(".movelink");
    var rand = Math.random();
    var result = Math.floor(rand * 100);
    moveLink.forEach((num, idx) => {
        moveLink[idx].addEventListener('click', (e) => {
            if(result < 100){
                console.log(result);
                console.log('success :: alaviciasdlcal');
            }
        })
    })
</script>
</html>
