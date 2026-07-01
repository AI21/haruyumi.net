
	<header class="">
		<div class="container-md">
			<nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark flex-wrap web-bg" aria-label="メイン・ナビゲーション">
				<div id="header-nav" class="container-fluid">
					<h1 class="navbar-brand"><a href="<?= SITE_ROOT; ?>" class="link-light text-decoration-none"><?= KASUGAI_KYOKAI_NAME; ?></a></h1>
					<?php if (empty($nonMenu) === true) : ?>
					<button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="ナビゲーションの切替">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="navbar-collapse offcanvas-collapse justify-content-md-center container-md bg-dark web-bg" id="navbarsExampleDefault">
						<ul class="navbar-nav mb-2 mb-md-0 nav-justified w-100">
							<?= get_menu_html($memuData['mainMenu'], $memuData['controllerName']); ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>
			</nav>
		</div>
	</header>
	<div class="container-md mb-2">
		<?= get_breadcrumb_html($memuInfo, $page, $fiscalYearId); ?>
	</div>
	