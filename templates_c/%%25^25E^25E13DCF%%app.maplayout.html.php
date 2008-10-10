<?php /* Smarty version 2.6.18, created on 2008-10-08 08:28:22
         compiled from app.maplayout.html */ ?>
<img id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-img" class="map-panel" 
	width="<?php echo $this->_tpl_vars['map']->getMapWidth(); ?>
" height="<?php echo $this->_tpl_vars['map']->getMapHeight(); ?>
" 
	src="<?php echo $this->_tpl_vars['map']->drawMap(); ?>
"/>
	
<input type="hidden" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-oe" value="<?php echo $this->_tpl_vars['map']->getExtent(true); ?>
" >
<input type="hidden" size="50" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-ex" value="<?php echo $this->_tpl_vars['map']->getExtent(true); ?>
" >
<input type="hidden" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-x" value="0" >
<input type="hidden" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-y" value="0" >
<input type="hidden" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-action" value="pan" >
<input type="hidden" id="<?php echo $this->_tpl_vars['map']->getName(); ?>
-tmp-file" value="<?php echo $this->_tpl_vars['map']->getTmpFile(); ?>
" >