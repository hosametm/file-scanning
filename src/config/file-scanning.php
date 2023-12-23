<?php

return [
    "mime_types" => [
        "mov" => "video/quicktime",
        "mp4" => "video/mp4",
        "pdf" => "application/pdf",
        "doc" => "application/msword",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "ppt" => "application/vnd.ms-powerpoint",
        "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "xls" => "application/vnd.ms-excel",
        "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "pages" => "application/vnd.apple.pages",
        "numbers" => "application/vnd.apple.numbers",
        "keynote" => "application/vnd.apple.keynote",
        "png" => "image/png",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg"
    ],
    'malicious_mime_types' => [
        "application/x-msdownload",
        "application/x-pkcs12",
        "application/octet-stream",
        "application/vnd.ms-cab-compressed",
        "application/x-ms-shortcut",
        "application/x-shockwave-flash",
        "application/javascript",
        "application/x-javascript",
        "application/x-dosexec",
        "application/x-shellscript",
        "application/xml",
        "application/json"
    ]
];
