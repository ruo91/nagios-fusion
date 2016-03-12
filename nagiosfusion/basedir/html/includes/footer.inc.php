<!-- FOOTER START -->
<div id="footer">

<?php
// XI 2014 styling
if (get_option('theme', 'xi2014') == 'xi2014') {
?>

    <div class="well">
        <div class="row-fluid">
            <div class="span6 footer-left">
                <?php echo get_product_name(); ?> <?php if (is_authenticated() === true) echo get_product_version(); ?>
                &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                <a href="<?php echo get_update_check_url();?>" target="_blank"><?php echo gettext("Check for Updates"); ?> <i class="icon-share"></i></a>
            </div>
            <div class="span6 footer-right">
                <a href="<?php echo get_base_url();?>about/"><?php echo gettext("About"); ?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="<?php echo get_base_url();?>about/?legal"><?php echo gettext("Legal"); ?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                Copyright &copy; 2008-<?php echo date('Y'); ?> <a href="http://www.nagios.com/" target="_blank">Nagios Enterprises, LLC</a>
            </div>
        </div>
    </div>

<?php
} else { // End 2014 style
?>


<div id="footermenucontainer">
	<div id="footernotice"><?php echo get_product_name();?> <?php if (is_authenticated() === true) echo get_product_version();?>  <?php echo gettext("Copyright"); ?> &copy; 2008-<?php echo date('Y'); ?>
	<a href="http://www.nagios.com/" target="_blank">Nagios Enterprises, LLC</a>.</div>
	<ul class="footermenu">
		<li><a href="<?php echo get_base_url();?>about/"><?php echo gettext("About"); ?></a></li>
		<li><a href="<?php echo get_base_url();?>about/?legal"><?php echo gettext("Legal"); ?></a></li>
	</ul>
</div>
<div id="checkforupdates">
<a href="<?php echo get_update_check_url();?>" target="_blank">
<img src="<?php echo get_base_url();?>images/checkforupdates.png" alt="Check for updates" title="Check for updates" border="0">
</a>
</div>
<div id="keepalive">

<?php
} // End original classic style
?>
</div>

<?php 
// Do "magic" logins for servers...
handle_server_authorizations(); 
?>

</div>
<!-- FOOTER END -->