<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: globalconfig.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/../includes/common.inc.php');
require_once(dirname(__FILE__).'/../includes/utils-timezones.inc.php');

// initialization stuff
pre_init();
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication(false);

// Only admins can access this page
if (!is_admin()) {
    echo $lstr['NotAuthorizedErrorText'];
    exit();
}

// Do actual page routing
route_request();

function route_request() {
    global $request;

    if(isset($request['update'])) {
        do_update_options();
    } else {
        show_options();
    }

    exit;
}

// Update the functions once the form is submitted
function do_update_options() {
    global $request;
    global $lstr;

    // Check session
    check_nagios_session_protector();
    
    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: main.php");
    }
    
    $errmsg = array();
    $errors = 0;

    // Grab form values
    $auto_check_update = grab_request_var("auto_update_check", "");
    $auto_check_updates = (empty($auto_check_update) ? 0 : 1);
    $admin_name = grab_request_var("admin_name", "");
    $admin_email = grab_request_var("admin_email", "");
    $url = grab_request_var("url", "");
    $date_format = grab_request_var("defaultDateFormat", DF_ISO8601);
    $number_format = grab_request_var("defaultNumberFormat", NF_2);
    $language = grab_request_var("defaultLanguage", "");

    // Theme settings for 2014
    $theme = grab_request_var("theme", "xi2014");
    $hc_theme = grab_request_var("hc_theme", "default");
    $perfdata_theme = grab_request_var("perfdata_theme", 0);

    // Get the timezone
    $new_timezone = grab_request_var("timezone","");

    // Make sure we have requirements
    if (in_demo_mode() == true)
        $errmsg[$errors++] = $lstr['DemoModeChangeError'];
    if (have_value($admin_name) == false)
        $errmsg[$errors++] = $lstr["NoAdminNameError"];
    if (have_value($admin_email) == false)
        $errmsg[$errors++] = $lstr["NoAdminEmailError"];
    else if (!valid_email($admin_email))
        $errmsg[$errors++] = $lstr["InvalidAdminEmailError"];
    if (have_value($url) == false)
        $errmsg[$errors++] = $lstr['BlankURLError'];
    else if (!valid_url($url))
        $errmsg[$errors++] = $lstr['InvalidURLError'];
    
    if (use_2014_features()) {
        if (have_value($language) == false)
            $errmsg[$errors++] = $lstr['BlankDefaultLanguageError'];
        if (have_value($theme) == false)
            $errmsg[$errors++] = gettext("You must set a theme");
    }

    // Handle errors
    if ($errors > 0) {
        show_options(true, $errmsg);
    }
        
    // Update options
    set_option("admin_name", $admin_name);
    set_option("admin_email", $admin_email);
    set_option("url", $url);
    set_option("default_language", $language);
    set_option("auto_update_check", $auto_check_updates);
    set_option("default_date_format", $date_format);
    set_option("default_number_format", $number_format);

    // Theme settings
    set_option("theme", $theme);
    set_option("hc_theme", $hc_theme);
    set_option("perfdata_theme", $perfdata_theme);

    // Update the timezone if we need to!
    $current_timezone = get_current_timezone();
    if (!empty($new_timezone) && $current_timezone != $new_timezone) {
        submit_command(COMMAND_CHANGE_TIMEZONE, $new_timezone);
    }
    
    // Mark that system settings were configured
    set_option("system_settings_configured", 1);
        
    // Success!
    show_options(false, $lstr['GlobalConfigUpdatedText']);
}

// Display the list of available options
function show_options($error=false, $msg="") {
    global $request;
    global $lstr;
    
    $url = get_option('url');
    $url = (empty($url) ? get_base_url() : $url);

    // Normal options
    $url = grab_request_var("url", $url);
    $admin_name = grab_request_var("admin_name", get_option('admin_name'));
    $admin_email = grab_request_var("admin_email", get_option('admin_email'));
    $language = grab_request_var("defaultLanguage", get_option('default_language'));
    $date_format = grab_request_var("defaultDateFormat", get_option('default_date_format'));
    $number_format = grab_request_var("defaultNumberFormat", intval(get_option('default_number_format')));

    // System settings for new themes
    $theme = grab_request_var("theme", get_option('theme', 'xi2014'));
    $hc_theme = grab_request_var("hc_theme", get_option('hc_theme', 'default'));
    $perfdata_theme = grab_request_var("perfdata_theme", get_option('perfdata_theme', 1));

    // Config Timezone
    $cfg_timezone = grab_request_var("timezone", get_option('timezone'));
    
    // Create defaults
    $admin_name = (empty($admin_name) ? "Nagios Fusion Admin" : $admin_name);
    $admin_email = (empty($admin_email) ? "root@localhost" : $admin_email);
        
    // Default to check for updates unless overridden
    $auc = get_option('auto_update_check');
    $auc = ($auc == "" ? 1 : $auc);
    $auto_update_check = grab_request_var("auto_update_check", $auc);
    $auto_update_check = ($auto_update_check == "on" ? 1 : $auto_update_check);

    // Get global variables
    $languages = get_languages();
    $number_formats = get_number_formats();
    $date_formats = get_date_formats();

    do_page_start(array("page_title" => $lstr['GlobalConfigPageTitle']), true);
?>

<h1><?php echo $lstr['GlobalConfigPageTitle'];?></h1>
<?php display_message($error,false,$msg); ?>

<form id="manageOptionsForm" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
    <?php echo get_nagios_session_protector();?>
    <input type="hidden" name="options" value="1">
    <input type="hidden" name="update" value="1">
    
    <div class="sectionTitle"><?php echo $lstr['GeneralProgramSettingsSectionTitle'];?></div>
    <table class="manageOptionsTable">
        <tr>
            <td><label for="urlBox"><?php echo $lstr['ProgramURLText'];?>:</label></td>
            <td><input type="text" size="45" name="url" id="urlBox" value="<?php echo encode_form_val($url); ?>" class="textfield" /></td>
        <tr>
        <tr>
            <td><label for="adminNameBox"><?php echo $lstr['AdminNameText'];?>:</label></td>
            <td><input type="text" size="30" name="admin_name" id="adminNameBox" value="<?php echo encode_form_val($admin_name); ?>" class="textfield" /></td>
        <tr>
        <tr>
            <td><label for="adminEmailBox"><?php echo $lstr['AdminEmailText'];?>:</label></td>
            <td><input type="text" size="30" name="admin_email" id="adminEmailBox" value="<?php echo encode_form_val($admin_email); ?>" class="textfield" /></td>
        <tr>
        <tr>
            <td><label for="autoUpdateCheckBox"><?php echo $lstr['AutoUpdateCheckBoxTitle'];?>:</label> <a href="<?php echo get_update_check_url(); ?>" target="_blank"><br><?php echo $lstr['CheckForUpdateNowText'];?></a></td>
            <td><input type="checkbox" class="checkbox" id="autoUpdateCheckBox" name="auto_update_check" <?php echo is_checked($auto_update_check, 1); ?>></td>
        </tr>
    </table>

    <!-- New 2014 XI Theme Setting & Timezone Settings -->
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

    <div class="sectionTitle"><?php echo gettext("Default Display/Theme Settings"); ?></div>
    <table class="manageOptionsTable">
    <tr>
        <td><label><?php echo gettext("XI User Interface Theme"); ?>:</label></td>
        <td>
            <select id="theme" name="theme">
                <option value="classic"<?php if ($theme == 'classic') { echo " selected"; } ?>><?php echo gettext("Classic XI"); ?></option>
                <option value="xi2014"<?php if ($theme == 'xi2014') { echo " selected"; } ?>><?php echo gettext("XI 2014"); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label><?php echo gettext("Highcharts Color Theme"); ?>:</label></td>
        <td>
            <select id="hc_theme" name="hc_theme">
                <option value="default"<?php if ($hc_theme == 'default') { echo " selected"; } ?>><?php echo gettext("Default (White)"); ?></option>
                <option value="gray"<?php if ($hc_theme == 'gray') { echo " selected"; } ?>><?php echo gettext("Classic (Gray)"); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="perfdata_theme"><?php echo gettext("Use Highcharts for Perfdata Graphs"); ?>:</label></td>
        <td><input type="checkbox" class="checkbox" id="perfdata_theme" name="perfdata_theme" value="1" <?php echo is_checked($perfdata_theme, 1); ?> /></td>
    </tr>
    </table>
    <?php } // End 2014 features section ?>

    <div class="sectionTitle"><?php echo $lstr['DefaultUserSettingsSectionTitle']; ?></div>
    <table class="manageOptionsTable">
        <tr>
            <td><label for="defaultLanguage"><?php echo $lstr['DefaultLanguageBoxTitle']; ?>:</label></td>
            <td>
                <select name="defaultLanguage" class="languageList dropdown">
                <?php foreach ($languages as $lang => $title) { ?>
                    <option value="<?php echo $lang; ?>" <?php echo is_selected($language, $lang); ?>><?php echo get_proper_language($title)."</option>"; ?>
                <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="defaultDateFormat"><?php echo $lstr['DefaultDateFormatBoxTitle']; ?>:</label></td>
            <td>
                <select name="defaultDateFormat" class="dateformatList dropdown">
                <?php foreach ($date_formats as $id => $txt) { ?>
                    <option value="<?php echo $id; ?>" <?php echo is_selected($id, $date_format); ?>><?php echo $txt; ?></option>
                <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="defaultNumberFormat"><?php echo $lstr['DefaultNumberFormatBoxTitle'];?>:</label></td>
            <td>
                <select name="defaultNumberFormat" class="numberformatList dropdown">
                <?php foreach ($number_formats as $id => $txt) { ?>
                    <option value="<?php echo $id; ?>" <?php echo is_selected($id, $number_format); ?>><?php echo $txt; ?></option>
                <?php } ?>
                </select>
            </td>
        </tr>
    </table>

    <div id="formButtons">
        <input type="submit" class="submitbutton" name="updateButton" value="<?php echo $lstr['UpdateSettingsButton']; ?>" id="updateButton">
        <input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton']; ?>" id="cancelButton">
    </div>
</form>

<?php
    do_page_end(true);
    exit();
}