<?php
require_once 'config.php';

// получаем поисковый запрос из формы
$query = $_POST['query'];

// получаем поисковый запрос и даты из формы 
$query = $_POST['query']; $from_date = strtotime($_POST['from_date']); $to_date = strtotime($_POST['to_date']);

// выполняем запрос к API с добавлением параметров даты 
$response = file_get_contents(VK_API_ENDPOINT . "newsfeed.search?q=" . urlencode($query) . "&v=" . VK_API_VERSION . "&access_token=" . VK_API_ACCESS_TOKEN . "&start_time=" . $from_date . "&end_time=" . $to_date );

// обрабатываем ответ и выводим результаты
$response = json_decode($response, true);
if (isset($response['response'])) {
    $items = $response['response']['items'];
    foreach ($items as $item) {
        $text = $item['text'];
        $date = date('d.m.Y H:i:s', $item['date']);
        $link = "https://vk.com/feed?w=wall{$item['owner_id']}_{$item['id']}";
        echo "<p>{$text}</p>";
        echo "<p>{$date}</p>";
        echo "<p><a href=\"{$link}\">Ссылка на запись</a></p>";
        echo "<hr>";
    }
    $regexp = '/\b' . preg_quote($query, '/') . '\b/i';
    foreach ($items as $item) {
        // проверяем, содержит ли текст фразу поиска
        if (preg_match($regexp, $item['text'])) {
            // если текст содержит фразу, выводим информацию о элементе
            echo "<p>".$item['type']." (id: ".$item['id'].")</p>";
            if (isset($item['text'])) {
                echo "<p>".$item['text']."</p>";
            }
            if (isset($item['attachments'])) {
                foreach ($item['attachments'] as $attachment) {
                    if ($attachment['type'] == 'photo') {
                        echo "<img src='".$attachment['photo']['sizes'][0]['url']."'>";
                    }
                }
            }
            echo "<hr>";
        }
    }
} else {
    echo "<p>По вашему запросу ничего не найдено.</p>";
}
?>
