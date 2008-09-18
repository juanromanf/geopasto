<?php /* Smarty version 2.6.18, created on 2008-09-17 15:09:40
         compiled from app/welcome.html */ ?>
<div class="x-panel-header" style="margin: 5px">	
	<span class="x-panel-header-text">
		Saludos <b><?php echo $this->_tpl_vars['user_name']; ?>
</b>... Su sesi&oacute;n inici&oacute;  a las <?php echo $this->_tpl_vars['user_time']; ?>
.</small> <br>
		Cuando finalize sus actividades en el sistema no olvide cerrar 
		su sesi&oacute;n haciendo click   
		<a href="#" style="color: #FFF; text-decoration: none" onclick="xajax_AppHome.exec({action:'Usuarios.doLogout',enableajax:true})">
			aqu&iacute;.
		</a>
	</span>
</div> 