<?php
session_start();

// 북마크 데이터를 받음
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $href = isset($_POST['href']) ? $_POST['href'] : '';
    $x = isset($_POST['x']) ? $_POST['x'] : '';
    $y = isset($_POST['y']) ? $_POST['y'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $text = isset($_POST['text']) ? $_POST['text'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : ''; // 추가 또는 제거
    $region = isset($_POST['region']) ? $_POST['region'] : ''; // 지역 정보 추가


    // 북마크 데이터를 배열로 생성
    $bookmarkItem = array(
        'type' => $type,
        'title' => $title,
        'href' => $href,
        'x' => $x,
        'y' => $y,
        'text' => $text,
        'region' => $region 
    );

    // 세션에 북마크 배열이 없으면 생성
    if (!isset($_SESSION['likedItems'])) {
        $_SESSION['likedItems'] = array();
    }

    if ($action === 'remove') {
        // 북마크 제거
        foreach ($_SESSION['likedItems'] as $index => $item) {
            if ($item['href'] === $href && $item['x'] === $x && $item['y'] === $y && $item['type'] === $type) {
                unset($_SESSION['likedItems'][$index]);
                $_SESSION['likedItems'] = array_values($_SESSION['likedItems']); // 인덱스 정리
                echo json_encode(array('status' => 'removed'));
                exit();
            }
        }
    } else {
        // 북마크 추가
        $_SESSION['likedItems'][] = $bookmarkItem;
        echo json_encode(array('status' => 'added')); 
    }
} else {
    // POST 요청이 아닌 경우에 대한 처리
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method'));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'get_bookmarks') {
        header('Content-Type: application/json');
        echo json_encode($_SESSION['likedItems']);
    }
}
?>