<html lang="ja">
<head data-site-root="<?= SITE_ROOT; ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="HandheldFriendly" content="True">
	<title><?= KASUGAI_KYOKAI_NAME; ?> 情報ページ</title>
	<link rel=”icon” id="favicon" href="<?= SITE_ROOT; ?>assets/img/favicon.ico">
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/libs/tablesorter/jquery.tablesorter.pager.min.css">
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/common.css?<?php echo date('Ymdhis'); ?>">
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/navi.css?<?php echo date('Ymdhis'); ?>">
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/page.css?<?php echo date('Ymdhis'); ?>">
	<?php if ($officerFlg === true) : ?>
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/libs/tom-select.css?<?php echo date('Ymdhis'); ?>">
	<?php endif; ?>
	<?php if (empty($headerCss) === false) : ?>
	<?php for ($i=0; $i<count($headerCss); $i++) : ?>
	<?php if (file_exists('./assets/css/pages/' . strtolower($headerCss[$i]) . '.css')) : ?>
	<link rel="stylesheet" href="<?= SITE_ROOT; ?>assets/css/pages/<?php echo strtolower($headerCss[$i]); ?>.css?<?php echo date('Ymdhis'); ?>">
	<?php endif; ?>
	<?php endfor; ?>
	<?php endif; ?>
</head>
</body>
