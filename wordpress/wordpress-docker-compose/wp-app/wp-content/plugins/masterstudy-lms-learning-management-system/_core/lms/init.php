<?php
\stmLms\Classes\Models\StmOrderItems::init();
\stmLms\Classes\Models\StmLmsPayout::init();
\stmLms\Classes\Models\StmCron::init();

add_action(
	'admin_init',
	function () {
		\stmLms\Classes\Vendor\LmsUpdates::init();
	}
);
