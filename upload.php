<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $file = $_FILES['media'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filePath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileType = $file['type'];

        $ch = curl_init();
        $cfile = new CURLFile($filePath, $fileType, $fileName);
        $data = array('media' => $cfile);

        curl_setopt($ch, CURLOPT_URL, 'https://openai.weixin.qq.com/weixinh5/webapp/h774yvzC2xlB4bIgGfX2stc4kvC85J/cos/upload');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            echo $response;
        } else {
            http_response_code($httpCode);
            echo json_encode(['error' => '图片上传失败']);
        }

        curl_close($ch);
    } else {
        http_response_code(400);
        echo json_encode(['error' => '无效的文件上传']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => '不支持的请求方法']);
}
?>
