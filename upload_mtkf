<?php
// 开启错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 日志文件路径
$log_file = __DIR__ . '/log.txt';

// 记录日志的函数
function log_message($message) {
    global $log_file;
    $message = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
    file_put_contents($log_file, $message, FILE_APPEND);
}

// 目标 URL
$target_url = 'https://kf.dianping.com/api/file/singleImage';

// 如果接收到文件
if (isset($_FILES['media'])) {
    $files = $_FILES['media'];
    $urls = [];

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            // 获取文件信息
            $filePath = $files['tmp_name'][$i];
            $fileName = $files['name'][$i];
            $fileType = $files['type'][$i];

            $headers = array(
                'Referer: https://h5.dianping.com/',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0',
            );

            // 准备 POST 数据
            $post_data = array(
                'channel' => '4',
                'file' => new CURLFile($filePath, $fileType, $fileName)
            );

            // 初始化 cURL
            $curl = curl_init();

            // 设置 cURL 选项
            curl_setopt($curl, CURLOPT_URL, $target_url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem'); // 证书路径

            // 执行请求并获取响应
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            log_message("HTTP Code: $httpCode");
            log_message("Response: $response");

            if ($response === false) {
                log_message("cURL 错误: " . curl_error($curl));
                echo json_encode(['error' => 'cURL 错误：' . curl_error($curl)]);
                curl_close($curl);
                exit;
            }

            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($responseData['data']['uploadPath'])) {
                        $urls[] = $responseData['data']['uploadPath'];
                    } else {
                        log_message("未能获取链接: " . print_r($responseData, true));
                        echo json_encode(['error' => '未能获取链接', 'response' => $responseData]);
                        curl_close($curl);
                        exit;
                    }
                } else {
                    log_message("JSON 解析错误: " . json_last_error_msg());
                    echo json_encode(['error' => 'JSON 解析错误', 'response' => $response]);
                    curl_close($curl);
                    exit;
                }
            } else {
                log_message("图片上传失败: HTTP Code $httpCode, Response: $response");
                echo json_encode(['error' => '图片上传失败', 'http_code' => $httpCode, 'response' => $response]);
                curl_close($curl);
                exit;
            }

            // 关闭 cURL 资源
            curl_close($curl);
        } else {
            log_message("无效的文件上传: " . $files['error'][$i]);
            echo json_encode(['error' => '无效的文件上传', 'file_error' => $files['error'][$i]]);
            exit;
        }
    }

    echo json_encode(['urls' => $urls]);
} else {
    log_message("未收到文件");
    echo json_encode(['error' => '未收到文件']);
}
?>
