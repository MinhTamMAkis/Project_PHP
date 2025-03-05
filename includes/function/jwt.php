<?php
function base64UrlDecode($data) {
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function createJWT($payload, $secret, $exp = 3600) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    
    // Thêm thời gian hết hạn cho token (tính bằng giây)
    $payload['exp'] = time() + $exp;
    $payload = json_encode($payload);

    // Mã hóa base64
    $headerEncoded = base64UrlEncode($header);
    $payloadEncoded = base64UrlEncode($payload);

    // Tạo signature
    $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
    $signatureEncoded = base64UrlEncode($signature);

    return "$headerEncoded.$payloadEncoded.$signatureEncoded";
}
function verifyJWT($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return false;
    }

    [$headerEncoded, $payloadEncoded, $signatureReceived] = $parts;

    $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    
    if (isset($payload['exp']) && time() > $payload['exp']) {
        return false; // Token hết hạn
    }

    $signatureExpected = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
    $signatureExpectedEncoded = base64UrlEncode($signatureExpected);

    return hash_equals($signatureExpectedEncoded, $signatureReceived) ? $payload : false;
}

function getBearerToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        return null;
    }
    $matches = [];
    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        return $matches[1];
    }
    return null;
}

function authenticateUser() {
    $secretKey = "your-secret-key"; // Thay bằng key bí mật của bạn
    $token = getBearerToken();
    if (!$token) {
        echo json_encode(['error' => 'Missing token']);
        http_response_code(401);
        exit;
    }

    $payload = verifyJWT($token, $secretKey);
    if (!$payload) {
        echo json_encode(['error' => 'Invalid or expired token']);
        http_response_code(403);
        exit;
    }

    return $payload; // Trả về thông tin user nếu token hợp lệ
}
