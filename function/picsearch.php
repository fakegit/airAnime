<?php
//参考文档及API token获取:https://soruly.github.io/whatanime.ga/
ini_set("error_reporting", "E_ALL & ~E_NOTICE");
require_once 'chttochs/convert.php';
require_once '../config.php';
$picurl = $_POST["picurl"];

if ($picurl != '' && preg_match('/^http(s)?:\\/\\/.+/', $picurl)) {
    $image_file = $picurl;
    $image_info = getimagesize($image_file);

    if (!$image_info) {
        die(json_encode(['picurl' => 'https://i.loli.net/2018/08/09/5b6bff9e96b22.jpg'], JSON_UNESCAPED_UNICODE));
    }

    $type = pathinfo($image_file, PATHINFO_EXTENSION);

    if ($type == 'jpg') {
        $type = 'jpeg';
    }

    $imgbase64 = 'data:image/' . $type . ';base64,' . chunk_split(base64_encode(file_get_contents($image_file)));
    $post_data = array(
        "image" => $imgbase64
    );
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://trace.moe/api/search?token=' . $GLOBALS['picS_token']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($curl);
    curl_close($curl);

    $oriData = json_decode($data, true);
    $rst = $oriData['docs'][0];
    $rst['picurl'] = $picurl;
    $rst = json_encode($rst, JSON_UNESCAPED_UNICODE);
    $rst = zhconversion_hans($rst);

    echo $rst;
} else {
    echo json_encode(['picurl' => 'https://i.loli.net/2018/08/09/5b6bff9e96b22.jpg'], JSON_UNESCAPED_UNICODE);
}
