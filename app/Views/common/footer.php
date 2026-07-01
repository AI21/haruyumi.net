	<!-- Modal： -->
	<div class="modal fade" id="ajax-error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title" id="error-title"></h2>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p id="error-message"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
				</div>
			</div>
		</div>
	</div>
	<div id="loading" class="position-absolute top-0 start-0 w-100 h-100 d-none">
		<div class="text-center position-absolute top-50 start-50 w-100 translate-middle">
			<div class="spinner-border text-light" role="status">
				<span class="sr-only"></span>
			</div>
		</div>
	</div>

	<footer>
		<div class="container-fluid text-center bg-dark text-light">
			<div id="cooperation" class="">
				<div class="row">
					<div class="col-6 col-sm-6 py-3"><a href="<?= URL_AIKYUREN; ?>" target="_blank">愛弓連HP</a></div>
					<div class="col-6 col-sm-6 py-3"><a href="<?= URL_ZENKYUREN; ?>" target="_blank">全弓連HP</a></div>
				</div>
			</div>
			<div id="copyrights">
				<p>&copy 2024 <?= KASUGAI_KYOKAI_NAME; ?> All rights reserved.</p>
			</div>
		</div>
	</footer>
	<script src="<?= SITE_ROOT; ?>assets/js/libs/jquery-3.7.0.min.js"></script>
	<script src="<?= SITE_ROOT; ?>assets/js/bootstrap/bootstrap.bundle.min.js"></script>
	<script src="<?= SITE_ROOT; ?>assets/js/libs/tablesorter/jquery.tablesorter.min.js"></script>
	<script src="<?= SITE_ROOT; ?>assets/js/libs/tablesorter/extras/jquery.tablesorter.pager.min.js"></script>
	<script src="<?= SITE_ROOT; ?>assets/js/navi.js?<?php echo date('Ymdhis'); ?>"></script>
	<script src="<?= SITE_ROOT; ?>assets/js/common.js?<?php echo date('Ymdhis'); ?>"></script>
	<?php if ($officerFlg === true) : ?>
	<script src="<?= SITE_ROOT; ?>assets/js/libs/tom-select.complete.min.js?<?php echo date('Ymdhis'); ?>"></script>
	<?php endif; ?>
	<?php if (empty($footerJs) === false) : ?>
	<?php for ($i=0; $i<count($footerJs); $i++) : ?>
	<?php if (file_exists('./assets/js/pages/' . strtolower($footerJs[$i]) . '.js')) : ?>
	<script src="<?= SITE_ROOT; ?>assets/js/pages/<?php echo strtolower($footerJs[$i]); ?>.js?<?php echo date('Ymdhis'); ?>"></script>
	<?php endif; ?>
	<?php endfor; ?>
	<?php endif; ?>
</body>
</html>