<?php
session_start();


// 북마크 상태를 확인하기 위한 함수
function isBookmarked($x, $y) {
    if (isset($_SESSION['likedItems'])) {
        foreach ($_SESSION['likedItems'] as $item) {
            if ($item['x'] === $x && $item['y'] === $y && $item['type'] === 'cctv') {
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
    <link rel="stylesheet" href="./css/warn.css">
  
</head>
<body>
    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
            <div class="ads_wrap ads_main_big2">
                <ins class="adsbygoogle"
                    data-language="ko"
                    data-ad-client="ca-pub-2858778486116301"
                    data-ad-slot="7913086329"
                    ></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <div id="allWarnings"></div> <!-- 경고 데이터 전체를 출력할 div -->
            <div class="ads_wrap ads_main_big2">
                <ins class="adsbygoogle"
                    data-language="ko"
                    data-ad-client="ca-pub-2858778486116301"
                    data-ad-slot="7913086329"
                    ></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <div class="pagination" id="pagination"></div> <!-- 페이지네이션 번호를 출력할 div -->
        </main>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const itemsPerPage = 5;
    const pagesToShow = 5;
    let allWarnings = [];
    let totalPages = 0;

// PHP에서 변환한 북마크 데이터
const likedItems = <?php echo isset($_SESSION['likedItems']) ? json_encode($_SESSION['likedItems']) : '[]'; ?>;

    

    function renderWarnings(page) {
        const allWarningsDiv = document.getElementById('allWarnings');
        allWarningsDiv.innerHTML = '';

        const start = (page - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, allWarnings.length);

        for (let i = start; i < end; i++) {
            const item = allWarnings[i];
            const boxDiv = document.createElement('div');
            boxDiv.classList.add('box');
            boxDiv.setAttribute('data-revX', item.revX || ''); // revX 값 설정
            boxDiv.setAttribute('data-revY', item.revY || ''); // revY 값 설정
            boxDiv.setAttribute('data-outbrkType', item.outbrkType || ''); // 데이터 속성 설정
            //console.log('Checking item:', item);
            const isBookmarked = likedItems.some(likedItem => likedItem.x === item.revX && likedItem.y === item.revY && likedItem.type === 'warn');
            console.log(likedItems);
            console.log(isBookmarked);
            //console.log('Is Bookmarked:', isBookmarked);
            if (isBookmarked) {
                boxDiv.classList.add('on');
            }
            const tableHTML = `
                <div class="box_top">
                    <span class="warn-type">${item.outbrkType || ''}</span>
                    <button type="button" class="like-btn" src="./img/icon-off.png" alt="찜 버튼"></button>
                </div>
                <table>
                    <tr class="message-row">
                        <td><strong>내용</strong></td>
                        <td>${item.message || ''}</td>
                    </tr>
                    <tr class="location-row">
                        <td><strong>위치</strong></td>
                        <td>${item.revRouteName || ''}</td>
                    </tr>
                </table>
            `;
            
            boxDiv.innerHTML = tableHTML;
            allWarningsDiv.appendChild(boxDiv);

            // 2번째 박스 뒤에 광고 추가
            if (i === 1) { 
                const adsDiv = document.createElement('div');
                adsDiv.classList.add('ads_wrap', 'ads_main_big2');
                adsDiv.innerHTML = `
                    <ins class="adsbygoogle"
                        style="display: block;"
                        data-language="ko"
                        data-ad-client="ca-pub-2858778486116301"
                        data-ad-slot="7913086329">
                    </ins>
                `;
                allWarningsDiv.appendChild(adsDiv);
            }
        }
        renderPagination();
        
        (adsbygoogle = window.adsbygoogle || []).push({});

        // 버튼 클릭 이벤트 핸들러 설정
        $('.like-btn').off('click').on('click', function() {
            var btn = $(this);
            var box = btn.closest('.box');
            var revX  = box.data('revx'); // data-revX 값 가져오기
            var revY  = box.data('revy'); // data-revY 값 가져오기
            var outbrkType = box.attr('data-outbrkType'); // data-outbrkType 속성 가져오기

            console.log(box);
            
            // 현재 박스의 message와 revRouteName 값 가져오기
            var message = box.find('.message-row td:last-child').text().trim();
            var revRouteName = box.find('.location-row td:last-child').text().trim();
            var isLiked = box.hasClass('on');

            // 콘솔에 값 출력
            console.log('Button clicked. outbrkType:', outbrkType);
            console.log('Button clicked. message:', message);
            console.log('Button clicked. revRouteName:', revRouteName);

            // 서버로 전송할 데이터
            var bookmarkData = {
                type: 'warn', 
                href: '', 
                x: revX, 
                y: revY,
                text: JSON.stringify(['주의 구간', outbrkType, '내 용', message, '위 치', revRouteName]),
                action: isLiked ? 'remove' : 'add' 
            };

            // text 배열을 콘솔에 출력
            console.log('bookmarkData.text:', bookmarkData.text);

            // AJAX 요청을 통해 찜 상태 업데이트
            $.ajax({
                url: 'bookmark.php',
                type: 'POST',
                data: bookmarkData,
                success: function(response) {
                    console.log('서버 응답:', response); 
                    try {
                        var data = JSON.parse(response);
                        
                        if (data.status === 'added') {
                            box.addClass('on'); // 'on' 클래스 추가
                        } else if (data.status === 'removed') {
                            box.removeClass('on'); // 'on' 클래스 제거
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
    }

    function renderPagination() {
        const paginationDiv = document.getElementById('pagination');
        paginationDiv.innerHTML = '';

        totalPages = Math.ceil(allWarnings.length / itemsPerPage);
        const startPage = Math.max(Math.floor((currentPage - 1) / pagesToShow) * pagesToShow + 1, 1);
        const endPage = Math.min(startPage + pagesToShow - 1, totalPages);

        if (startPage > 1) {
            const prevLink = document.createElement('a');
            prevLink.textContent = '<';
            prevLink.href = '#';
            prevLink.addEventListener('click', function(event) {
                event.preventDefault();
                currentPage = Math.max(1, startPage - pagesToShow);
                renderWarnings(currentPage);
            });
            paginationDiv.appendChild(prevLink);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageLink = document.createElement('a');
            pageLink.textContent = i;
            pageLink.href = '#';
            if (i === currentPage) {
                pageLink.classList.add('active');
            } else {
                pageLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    currentPage = i;
                    renderWarnings(currentPage);
                });
            }
            paginationDiv.appendChild(pageLink);
        }

        if (endPage < totalPages) {
            const nextLink = document.createElement('a');
            nextLink.textContent = '>';
            nextLink.href = '#';
            nextLink.addEventListener('click', function(event) {
                event.preventDefault();
                currentPage = Math.min(totalPages, endPage + 1);
                renderWarnings(currentPage);
            });
            paginationDiv.appendChild(nextLink);
        }
    }

    function loadWarnings() {
        $.ajax({
            url: 'apiWarn.php',
            type: 'POST',
            data: {},
            success: function(response) {
                allWarnings = response;
                renderWarnings(currentPage);
            },
            error: function(xhr, status, error) {
                console.error('AJAX 요청 실패:', status, error);
            }
        });
    }

    loadWarnings();
});
    </script>

</body>
</html>
