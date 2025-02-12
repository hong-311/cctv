<header>
    <a href="./home.php" target="_parent" class="movelink">
        <img src="./img/logo.png" alt="로고">
    </a>
</header>
<script>
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