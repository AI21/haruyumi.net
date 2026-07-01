<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <meta name="robots" content="noindex,nofollow,noarchive">
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link type="text/css" rel="stylesheet" href="./assets/ouji/css/common.css">
	<?php if (empty($headerCss) === false) : ?>
	<?php for ($i=0; $i<count($headerCss); $i++) : ?>
	<?php if (file_exists('./assets/ouji/css/' . strtolower($headerCss[$i]) . '.css')) : ?>
	<link rel="stylesheet" href="./assets/ouji/css/<?php echo strtolower($headerCss[$i]); ?>.css?<?php echo date('Ymdhis'); ?>">
	<?php endif; ?>
	<?php endfor; ?>
	<?php endif; ?>
</head>