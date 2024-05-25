<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $files = $_FILES['media'];
    $urls = [];

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $filePath = $files['tmp_name'][$i];
            $fileName = $files['name'][$i];
            $fileType = $files['type'][$i];

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
                $responseData = json_decode($response, true);
                if (isset($responseData['url'])) {
                    $urls[] = $responseData['url'];
                } else {
                    echo json_encode(['error' => '未能获取链接']);
                    exit;
                }
            } else {
                http_response_code($httpCode);
                echo json_encode(['error' => '图片上传失败']);
                exit;
            }

            curl_close($ch);
        } else {
            http_response_code(400);
            echo json_encode(['error' => '无效的文件上传']);
            exit;
        }
    }

    echo json_encode(['urls' => $urls]);
} else {
    http_response_code(405);
    echo json_encode(['error' => '不支持的请求方法']);
}
?>
