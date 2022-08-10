<?php
use GDO\Facebook\GDT_FBAuthButton;
use GDO\UI\GDT_Link;

/** @var $field GDT_FBAuthButton **/

$icon = sprintf('<img src="%sGDO/Facebook/img/fb-btn.png"
 title="%s" style="width: 300px;" />',
 GDO_WEB_ROOT, t('btn_continue_with_fb'));

echo GDT_Link::make()->labelNone()->
	href($field->href)->rawIcon($icon)->
	render();
