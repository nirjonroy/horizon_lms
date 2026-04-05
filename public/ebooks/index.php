<?php

$query = $_SERVER['QUERY_STRING'] ?? '';
$target = '/books-to-go' . ($query !== '' ? '?' . $query : '');

header('Location: ' . $target, true, 301);
exit;
