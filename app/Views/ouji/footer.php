
    <footer>
        <small>Copyright (c) 春日井市弓道協会 All Rights Reserved.</small>
    </footer>
    <!-- SCRIPTS -->
    <script type="text/javascript" src="./assets/ouji/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="./assets/ouji/js/common.js"></script>
	<?php if (empty($footerJs) === false) : ?>
	<?php for ($i=0; $i<count($footerJs); $i++) : ?>
	<?php if (file_exists('./assets/ouji/js/' . strtolower($footerJs[$i]) . '.js')) : ?>
	<script src="./assets/ouji/js/<?php echo strtolower($footerJs[$i]); ?>.js?<?php echo date('Ymdhis'); ?>"></script>
	<?php endif; ?>
	<?php endfor; ?>
	<?php endif; ?>
</body>
</html>