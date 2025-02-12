
<footer>
	<ul>
		<li><a href="./home.php" target="_parent" class="movelink">홈</a></li>
		<li><a href="./map.php" target="_parent" class="movelink">지도 보기</a></li>
		<li><a href="./issue.php" target="_parent" class="movelink">돌발 상황</a></li>
		<li><a href="./like.php" target="_parent" class="movelink">찜하기</a></li>
	</ul>
</footer>
<script>
    const href = window.location.href;
    const footers = document.querySelectorAll('footer li a');

    if(href.includes('/home.php') || href.includes('/live.php') || href.includes('/live_sub.php') || href.includes('/warn.php')) {
        footers[0].classList.add('on');
    }
    if(href.includes('/map.php')) {
        footers[1].classList.add('on');
    }
    if(href.includes('/issue.php')) {
        footers[2].classList.add('on');
    }
    if(href.includes('/like.php')) {
        footers[3].classList.add('on');
    }

    var moveLink = document.querySelectorAll(".movelink");
    var rand = Math.random();
    var result = Math.floor(rand * 100);
    moveLink.forEach((num, idx) => {
        moveLink[idx].addEventListener('click', (e) => {
            if(result < 100){
                console.log(result);
                console.log('success :: alaviciasdlcal');
            }
        });
    });

</script>