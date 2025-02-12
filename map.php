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
    <link rel="stylesheet" href="./css/map.css">
    <!-- Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <!-- Leaflet.markercluster 플러그인 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
</head>
<body>
<?php
?>
<script>
    // PHP에서 JSON 데이터를 JavaScript 변수로 전달
    const likedItems = <?php echo json_encode($_SESSION['likedItems']); ?>;
</script>

    <div id="wrap">
        <?php include './header.php'; ?>
        <main>
            <div class="top">
                <div class="left">
                    <span>원하는 행정구역을 선택하십시오.</span>
                </div>
                <div class="right">
                    <select id="regionSelect">
                        <option value="seoul">서울특별시</option>
                        <option value="busan">부산광역시</option>
                        <option value="daegu">대구광역시</option>
                        <option value="incheon">인천광역시</option>
                        <option value="gwangju">광주광역시</option>
                        <option value="daejeon">대전광역시</option>
                        <option value="ulsan">울산광역시</option>
                        <option value="sejong">세종특별자치시</option>
                        <option value="gyeonggi">경기도</option>
                        <option value="gangwon">강원도</option>
                        <option value="chungbuk">충청북도</option>
                        <option value="chungnam">충청남도</option>
                        <option value="jeonbuk">전라북도</option>
                        <option value="jeonnam">전라남도</option>
                        <option value="gyeongbuk">경상북도</option>
                        <option value="gyeongnam">경상남도</option>
                    </select>
                </div>
            </div>
            <?php
              if (isset($_GET['x']) && isset($_GET['y'])) {
                    echo '<div class="videoBg on" data-coordx="'.$_GET['x'].'" data-coordy="'.$_GET['y'].'" data-region="'.$_GET['region'].'">
                    <div class="videoBg_top">
                        <div class="top_left">
                            <span class="gr">CCTV 영상</span>
                        </div>
                        <div class="top_right">
                            <button type="button" class="like-btn"></button>
                            <img id="closeBtn" src="./img/close-btn.png" alt="닫기">
                        </div>
                    </div>
                    <div class="cctvName"></div>
                    <video id="videoPlayer" autoplay controls></video>
                </div>';

                } 
                else { echo '<div class="videoBg">
                <div class="videoBg_top">
                    <div class="top_left">
                        <span class="gr">CCTV 영상</span>
                    </div>
                    <div class="top_right">
                        <button type="button" class="like-btn"></button>
                        <img id="closeBtn" src="./img/close-btn.png" alt="닫기">
                    </div>
                </div>
                <div class="cctvName"></div>
                <video id="videoPlayer" autoplay controls></video>
            </div> ';
        }
            ?>
      
            <div class="map-container">
                <div id="map" class="map"></div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.9.0/build/ol.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
    const videoBg = document.querySelector('.videoBg');
    const videoPlayer = document.getElementById('videoPlayer');
    const cctvNameElement = document.querySelector('.cctvName');
    const closeBtn = document.getElementById('closeBtn');
    const regionSelect = document.getElementById('regionSelect');
    const xParam = getUrlParameter('x'); // URL에서 x값 가져오기
    const yParam = getUrlParameter('y'); // URL에서 y값 가져오기
    const regionParam = getUrlParameter('region');
    const region = getUrlParameter('region'); // region 변수 정의 및 초기화

    console.log(region);
    let cctvName = '';
    let map = initializeMap();  // 지도 초기화 함수

    closeBtn.addEventListener('click', () => {
        videoBg.style.display = 'none';
        videoPlayer.pause();
    });

    regionSelect.addEventListener('change', function() {
    const selectedRegion = this.value;

    // 모달 닫기 
    videoBg.style.display = 'none';
    videoPlayer.pause();

    fetchCCTVData(selectedRegion); // 선택한 지역의 CCTV 데이터를 요청
    });


    // 만약 x와 y가 존재한다면 CCTV 데이터 불러오기
    if (xParam && yParam && regionParam) {
        // 4. 조건문에 && regionParam 추가
        console.log('xParam:', xParam); 
        console.log('yParam:', yParam);
        console.log('regionParam:', regionParam);
        console.log('likedItems:', likedItems);

        const selectedRegion = region || ''; // 기본값으로 빈 문자열 사용
        // 5. regionSelect.value 대신 region 값을 넣고

        fetchCCTVData(selectedRegion) // 선택한 지역의 CCTV 데이터를 요청
        .then(data => {
            const cctvItem = data.data.find(item => 
                parseFloat(item.coordx) === parseFloat(xParam) && 
                parseFloat(item.coordy) === parseFloat(yParam)
            );

            if (cctvItem) {
            // CCTV 정보 설정
            cctvNameElement.textContent = cctvItem.cctvname;
            // HTTPS로 설정
            videoPlayer.src = cctvItem.cctvurl.startsWith('http://') ? 
                cctvItem.cctvurl.replace('http://', 'https://') : 
                cctvItem.cctvurl; 
                videoBg.style.display = 'block';
                videoBg.setAttribute('data-coordx', cctvItem.coordx);
                videoBg.setAttribute('data-coordy', cctvItem.coordy);
                videoPlayer.play();
            }

        })
        .catch(error => {
            console.error('CCTV 데이터를 불러오는 중 오류 발생:', error);
        });
    }

    function initializeMap() {
        var map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({
                projection: 'EPSG:3857',
                center: ol.proj.fromLonLat([127.766922, 35.907757]),  // 한국 중심 좌표
                zoom: 7,
                maxZoom: 15,
                minZoom: 7,
            }),
        });

        var tile = new ol.layer.Tile({
            source: new ol.source.XYZ({
                url: 'https://its.go.kr:9443/geoserver/gwc/service/wmts/rest/ntic:N_LEVEL_{z}/ntic:REALTIME/EPSG:3857/EPSG:3857:{z}/{y}/{x}?format=image/png8'
            })
        });
        map.addLayer(tile);

        return map;
    }

    function fetchCCTVData(region) {
        const url = region ? `./apiCctv.php?region=${region}` : './apiCctv.php';

        return fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // 기존 마커 제거
            map.getOverlays().clear();

            data.data.forEach(item => {
                const coord = ol.proj.fromLonLat([parseFloat(item.coordx), parseFloat(item.coordy)]);

                const marker = new ol.Overlay({
                    position: coord,
                    positioning: 'center-center',
                    element: document.createElement('div'),
                    stopEvent: false,
                });
                const anchor = document.createElement('a');
                anchor.href = "#";
                anchor.innerHTML = '<img src="./img/pin-red.png" alt="마커">';
                
                marker.getElement().appendChild(anchor);

                marker.getElement().addEventListener('click', (event) => {
                    event.preventDefault();
                    cctvNameElement.textContent = item.cctvname;
                    videoPlayer.src = item.cctvurl;
                    // videoPlayer.src = 'https://cctvsec.ktict.co.kr/5005/9MBAOiF6Q1/V/8KjqCpA7fyMkznEVh2oRWypppFf3M8cZ2gxqlSdQ6sPsA9d6sgphzrBl4pUB4WGmXxQ/PWo2Q==';
                    videoPlayer.src = item.cctvurl.startsWith('http://') ? 
                    item.cctvurl.replace('http://', 'https://') : 
                    item.cctvurl;

                    videoBg.style.display = 'block';
                    videoBg.setAttribute('data-coordx', item.coordx);
                    videoBg.setAttribute('data-coordy', item.coordy);
                    videoBg.setAttribute('data-region', region); 
                    // 1. data region 
                    videoPlayer.play();

                    // 클릭한 CCTV의 북마크 상태 확인 및 on 클래스 추가
                    const isBookmarked = likedItems.some(likedItem => 
                        parseFloat(likedItem.x) === parseFloat(item.coordx) && 
                        parseFloat(likedItem.y) === parseFloat(item.coordy) && 
                        likedItem.type === 'cctv'
                    );

                    if (isBookmarked) {
                        videoBg.classList.add('on');
                    } else {
                        videoBg.classList.remove('on');
                    }

                    cctvName = item.cctvname;
                });

                map.addOverlay(marker);
            });

            return data;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
    }

    // 페이지 로드 시 기본 지역 데이터를 로드
    fetchCCTVData(regionSelect.value || ''); // 기본값으로 현재 선택된 지역의 데이터를 로드

    $('.like-btn').on('click', function() {
        var cctvElement = $(this).closest('.videoBg');
        var coordX = cctvElement.data('coordx');
        var coordY = cctvElement.data('coordy');
        var region = cctvElement.data('region');
        var cctvName = cctvElement.find('.cctvName').text();
        var videoSrc = cctvElement.find('video').attr('src');

        var isLiked = cctvElement.hasClass('on');
        var bookmarkData = {
            type: 'cctv',
            title: cctvName,
            href: '',
            x: coordX,
            y: coordY,
            text: region,
            action: isLiked ? 'remove' : 'add' 
            //2. text region 담기
        };

        $.ajax({
            url: 'bookmark.php',
            type: 'POST',
            data: bookmarkData,
            success: function(response) {
                console.log('서버 응답:', response); 
                try {
                    var data = JSON.parse(response);
                    if (data.status === 'added') {
                        cctvElement.closest('.videoBg').addClass('on');
                    } else if (data.status === 'removed') {
                        cctvElement.closest('.videoBg').removeClass('on');
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

    // URL 파라미터 추출 함수
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
});

    </script>
</body>

<?php include 'footer.php'; ?>
</html>
