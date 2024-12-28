<?php

// Đặt API Key của bạn vào đây
$apiKey = "API-Key";

// URL của Gemini API - sử dụng gemini-pro-vision cho hình ảnh
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// Nhập tin nhắn từ người dùng
$userMessage = $_POST["message"];

// Xử lý hình ảnh nếu có
$imageData = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $imagePath = $_FILES['image']['tmp_name'];
    $imageData = [
        'mimeType' => mime_content_type($imagePath),
        'data' => base64_encode(file_get_contents($imagePath)),
    ];
}

// Tạo nội dung yêu cầu (JSON)
$requestData = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage],
            ],
        ],
    ],
];

// Thêm hình ảnh vào request nếu có
if ($imageData) {
    $requestData['contents'][0]['parts'][] = [
        'inlineData' => $imageData,
    ];
}

// Chuyển đổi mảng thành JSON
$jsonData = json_encode($requestData);

// Khởi tạo cURL
$ch = curl_init($apiUrl);

// Thiết lập các tùy chọn cho cURL
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData),
    ],
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_RETURNTRANSFER => true,
]);

// Thực hiện yêu cầu và nhận phản hồi
$response = curl_exec($ch);

// Kiểm tra lỗi cURL
if (curl_errno($ch)) {
    $responseText = 'Lỗi cURL: ' . curl_error($ch);
} else {
    // Giải mã JSON phản hồi
    $responseData = json_decode($response, true);

    // Kiểm tra lỗi API
    if (isset($responseData['error'])) {
        $responseText = 'Lỗi API: ' . $responseData['error']['message'];
    } else {
        // Lấy nội dung phản hồi
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            $responseText = 'Không nhận được phản hồi từ API.';
        }
    }
}

// Đóng kết nối cURL
curl_close($ch);

// Xử lý Markdown
$responseText = preg_replace('/(\*\*|__)(.*?)\\1/', '<b>\\2</b>', $responseText); // In đậm
$responseText = preg_replace('/(\*|_)(.*?)\\1/', '<i>\\2</i>', $responseText); // In nghiêng

// Trả về phản hồi
echo $responseText;

?>
