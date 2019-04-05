<?php
return [
    'avatar_limit' => 2048000, //2M
    'avatar_type' => ['gif'=> 1, 'jpg' => 2, 'jpge' => 3, 'png' => 4], //支持的头像类型

    'document_limit' => 51200000, //50M
    'document_type' => ['pdf' => 1, 'doc' => 2, 'docx' => 3, 'txt' => 4, 'md' => 5, 'xlsx' => 6, 'ppt' => 7], //支持的文件类型
    'document_public_info' => ['id', 'name', 'type', 'tag', 'uploader', 'uploader_nickname', 'title', 'downloads', 'description', 'score', 'size', 'md5', 'created_at']
];
