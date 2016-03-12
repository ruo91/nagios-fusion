<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: install.php 187 2010-07-04 16:36:27Z egalstad $

require_once(dirname(__FILE__).'/config.inc.php');
require_once(dirname(__FILE__).'/includes/auth.inc.php');
require_once(dirname(__FILE__).'/includes/utils.inc.php');
require_once(dirname(__FILE__).'/includes/pageparts.inc.php');
require_once(dirname(__FILE__).'/includes/utils-timezones.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables 
grab_request_vars();

// Check prereqs
check_prereqs();

route_request();

function route_request() {
    global $request;

    // Redirect if there is no install necessary
    if (install_needed() == false) {
        header("Location: ".get_base_url());
        exit();
    }

    $pageopt = get_pageopt("");
    switch ($pageopt) {
        case "install":
            do_install();
            break;
        default:
            show_install();
            break;
    }
}

function show_install($error=false, $msg="") {
    global $cfg;
    global $request;
    global $lstr;

    // Default values
    $url = get_base_url();
    $admin_name = "Nagios Administrator";
    $admin_email = "root@localhost";
    $admin_password = random_string(6);

    // Page start
    do_page_start(array("page_title" => $lstr['InstallPageTitle']));
?>
    <div style="padding: 0 14px;">
        <h1><?php echo $lstr['InstallPageHeader']; ?></h1>
        <?php display_message($error, "", $msg); ?>
        <p><?php echo $lstr['InstallPageMessage']; ?></p>

        <form id="manageOptionsForm" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" name="install" value="1">

            <div class="sectionTitle"><?php echo $lstr['GeneralProgramSettingsSectionTitle']; ?></div>
            <table class="manageOptionsTable">
                <tr>
                    <td><label for="urlBox"><?php echo $lstr['ProgramURLText']; ?>:</label></td>
                    <td><input type="text" size="45" name="url" id="urlBox" value="<?php echo encode_form_val($url); ?>" class="textfield" /></td>
                <tr>
                <tr>
                    <td><label for="adminNameBox"><?php echo $lstr['AdminNameText']; ?>:</label></td>
                    <td><input type="text" size="30" name="admin_name" id="adminNameBox" value="<?php echo encode_form_val($admin_name); ?>" class="textfield" /></td>
                <tr>
                <tr>
                    <td><label for="adminEmailBox"><?php echo $lstr['AdminEmailText']; ?>:</label></td>
                    <td><input type="text" size="30" name="admin_email" id="adminEmailBox" value="<?php echo encode_form_val($admin_email); ?>" class="textfield" /></td>
                <tr>
                <tr>
                    <td><label for="adminPasswordBox"><?php echo $lstr['AdminPasswordText']; ?>:</label></td>
                    <td><input type="text" size="30" name="admin_password" id="adminPasswordBox" value="<?php echo encode_form_val($admin_password); ?>" class="textfield" /></td>
                <tr>
            </table>

            <!-- New 2014 Timezone Settings -->
            <?php if (use_2014_features()) {
                $current_timezone = get_current_timezone();
                if (!empty($cfg_timezone) && $cfg_timezone != $current_timezone) {
                    $current_timezone = $cfg_timezone;
                }
                $timezones = get_timezones();
            ?>
            <div class="sectionTitle"><?php echo gettext("Timezone Settings"); ?></div>
            <table class="manageOptionsTable">
            <tr>
                <td><label><?php echo gettext("Timezone"); ?>:</label></td>
                <td>
                    <select id="timezone" name="timezone">
                        <?php
                        $set = false;
                        foreach ($timezones as $name => $tz) {
                        ?>
                        <option value="<?php echo $tz; ?>"<?php if ($tz == $current_timezone) { echo "selected"; $set = true; } ?>><?php echo $name; ?></option>
                        <?php
                        }

                        if (!$set) {
                            ?>
                            <option value="<?php echo $current_timezone; ?>" selected><?php echo $current_timezone; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            </table>
            <?php } // End of new timezone settings ?>

            <div id="formButtons">
                <input type="submit" class="submitbutton" name="updateButton" value="<?php echo $lstr['InstallButton']; ?>" id="updateButton">
            </div>
        </form>
    </div>
    
<?php

    do_page_end();
    exit();
}

    
function do_install() {
    global $lstr;

    // Get values
    $url = grab_request_var("url", "");
    $admin_name = grab_request_var("admin_name", "");
    $admin_email = grab_request_var("admin_email", "");
    $admin_password = grab_request_var("admin_password", "");

    // Check for errors
    $errors = 0;
    $errmsg = array();
    if (have_value($url) == false)
        $errmsg[$errors++] = $lstr['BlankURLError'];
    else if (!valid_url($url))
        $errmsg[$errors++] = $lstr['InvalidURLError'];
    if (have_value($admin_name) == false)
        $errmsg[$errors++] = $lstr['BlankNameError'];
    if (have_value($admin_email) == false)
        $errmsg[$errors++] = $lstr['BlankEmailError'];
    else if (!valid_email($admin_email))
        $errmsg[$errors++] = $lstr['InvalidEmailAddressError'];
    if (have_value($admin_password) == false)
        $errmsg[$errors++] = $lstr['BlankPasswordError'];

    $uid = get_user_id("nagiosadmin");
    if ($uid <= 0) {
        $errmsg[$errors++] = gettext("Unable to get user id for admin account.");
    }

    // Handle errors
    if ($errors > 0) {
        show_install(true, $errmsg);
    }

    // Set global options
    set_option("admin_name", $admin_name);
    set_option("admin_email", $admin_email);
    set_option("url", $url);
    
    // Modify the admin account
    change_user_attr($uid, "email", $admin_email);
    change_user_attr($uid, "name", $admin_name);
    change_user_attr($uid, "password", md5($admin_password));
    change_user_attr($uid, "backend_ticket", random_string(8));

    // Clear license acceptance for nagiosadmin
    set_user_meta($uid, "license_version", -1, false);

    // Clear inital task settings
    set_option("system_settings_configured", 0);
    set_option("security_credentials_updated", 0);
    set_option("mail_settings_configured", 0);
    
    // Set installation flags
    set_db_version();
    set_install_version();

    // Check trial start date
    $ts = get_trial_start();

    // Delete force install file if it exists
    if(file_exists("/tmp/nagiosfusion.forceinstall")) {
        unlink("/tmp/nagiosfusion.forceinstall");
    }

    // Turn on automatic update checks
    set_option('auto_update_check', true);

    // Do an update check
    do_update_check(true, true);

    // Get the timezone
    $new_timezone = grab_request_var("timezone","");
    set_option('timezone', $new_timezone);

    // Update the timezone if we need to!
    $current_timezone = get_current_timezone();
    if ($current_timezone != $new_timezone) {
        submit_command(COMMAND_CHANGE_TIMEZONE, $new_timezone);
    }

    show_install_complete();
}

function show_install_complete($error=false, $msg="") {
    global $request;
    global $lstr;

    // Get variables
    $admin_password = grab_request_var("admin_password", "");

    // Display page
    do_page_start($lstr['InstallCompletePageTitle']);
?>

    <h1><?php echo $lstr['InstallCompletePageHeader']; ?></h1>
    <?php display_message($error, false, $msg); ?>
    <p><?php echo $lstr['InstallCompletePageMessage']; ?></p>
    <p><?php echo gettext("You may now login to Nagios Fusion using the following credentials"); ?>:</p>
    <table>
        <tr>
            <td><?php echo $lstr['UsernameText']; ?>:</td><td><b>nagiosadmin</b></td>
        </tr>
        <tr>
            <td><?php echo $lstr['PasswordText']; ?>:</td><td><b><?php echo $admin_password; ?></b></td>
        </tr>
    </table>
    <p><a href="login.php" target="_blank"><b><?php echo gettext("Login to Nagios Fusion"); ?></b></a></p>

<?php

    do_page_end();
    exit();
}