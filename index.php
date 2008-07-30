<?php
	header("Cache-Control: no-cache"); 
	header("Pragma: no-cache");
	require('./app.config.php');

	AppSession::startSession();

	$application = new AppHome(TRUE);
	$application->DisplayLayout(TRUE);
	
	if(AppSession::isValid()) {
	?>
	<script type="text/javascript">
		xajax_AppHome.exec({ action: 'AppHome.Index', jscallback : 'AppHome.init();' } );
	</script>
	<?php
	} else {
	?>
	<script type="text/javascript">
		xajax_AppHome.exec({ action: 'UsuariosUI.displayLogin', jscallback : 'UsuariosUI.displayLogin();', target: 'layout-body' } );
	</script>
	<?php
	}
?>