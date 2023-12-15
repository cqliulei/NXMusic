<?php
// 设置编码
header('content-type:application/json;charset=utf-8');
// 设置key
$key = 'your_encryption_key';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");


// 加密
function encrypt($data, $key)
{
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

// 解密
function decrypt($data, $key)
{
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

// 获取文件夹
function getFolderDirectory($dir)
{
    // $dir = './songs/grade5/Unit 1';
    $files = scandir($dir);
    $folders = array();
    foreach ($files as $file) { //is_dir 函数检查指定的文件是否是目录 ($dir . '/' . $file) && true:false
        // $file_name = null;
        if ($file != '.' && $file != '..') {
            $fileType = null;
            $name = null;
            $title = null;
            //scandir 返回指定目录中的文件和目录的数组 is_file:函数检查指定的文件名是否是正常的文件 pathinfo:函数以数组的形式返回文件路径的信息。
            $filePath = $dir . '/' . $file;
            $name = $file;

            if (is_file($filePath)) { //文件
                $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
                $file_info = pathinfo($file);

                $title = $file_info['filename'];
                // echo $file . ' - ' . $fileType . "\n";
            } else { //文件夹
                // echo $file . "\n"; 
                $fileType = 'folder';
            }
            if ($fileType == 'folder' || $fileType == 'mp3' || $fileType == 'aac' || $fileType == 'wma' || $fileType == 'wave'|| $fileType == 'wav') {
                $folders[] = array(
                    'id' => uniqid(),
                    'title' => $title,
                    'file_type' => $fileType,
                    'is_dir' => is_dir($filePath),
                    'name' => $name,
                    'src' => $filePath
                );
            }

            // $folders['type'] => $fileType;
            // $folders['is_dir'] => is_dir($filePath);
        }
    }
    return $folders;
}

// 检查加密请求
function checkEncryptedRequest()
{
    $encryptedData = $_POST['encryptedData']; // 加密的数据
    $decryptedData = decrypt($encryptedData, $GLOBALS['key']); // 解密数据
    $requestData = json_decode($decryptedData, true); // json编码
    return $requestData;
}

// 加密响应
function encryptResponse($responseData)
{
    $jsonResponse = json_encode($responseData); // 加密JSON
    $encryptedResponse = encrypt($jsonResponse, $GLOBALS['key']); // 加密JSON
    return $encryptedResponse;
}

// 处理请求
function handleRequest()
{
    // $requestData = checkEncryptedRequest(); // 检查加密请求
    // if ($requestData === false) {
    //     return '?????';
    // }

    // $directory = $requestData['directory']; // 请求目录

    // if (isset($_GET['directory'])) {
    //     $directory = $_GET['directory'];
    //     // echo 'directory????' . $directory . "\n";
    // }

    // $directory = './songs/';
    $directory = $_GET['name'];
    $folders = getFolderDirectory($directory); // 获取文件夹

    $responseData = array(
        'data' => $folders,
        'info' => '', //返回文本信息
        'code' => 200, //成功
    );

    // print_r($responseData['data']);

    // $encryptedResponse = encryptResponse($responseData); // 加密响应
    $responseData =  json_encode($responseData);
    // print_r($responseData);

    return $responseData;
}

// 响应请求 
echo handleRequest();
