<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: updates.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/../includes/common.inc.php');

// Iinitialization stuff
pre_init();
init_session();

// Grab GET or POST variables 
grab_request_vars();

// Check prereqs
check_prereqs();

// Check authentication
check_authentication(false);

// Lock the page while auto-updating is running
$locked = false;
$locked_subsys_id = 0;
define('AU_LOGFILE', get_tmp_dir()."/upgrade.log");
define('AU_LOGDIR', get_root_dir()."/var/upgrades/");

route_request();

function route_request() {
    global $request;
    global $lstr;
    
    // Only admins can access this page
    if (is_admin() == false) {
        echo $lstr['NotAuthorizedErrorText'];
        exit();
    }

    // Do an action
    $action = grab_request_var("action", "");

    switch ($action)
    {
        case "ajax-checkupdateavailable":
            check_update_available();
            break;

        case "ajax-checkstatus":
            check_autoupdate_status();
            break;

        case "ajax-performupdate":
            start_autoupdate_ajax();
            break;

        case "ajax-acknowledge":
            acknowledge_autoupdate_ajax();
            break;

        case "ajax-getlog":
            get_autoupdateupdate_log_ajax();
            break;

        case "ajax-deletelog":
            delete_autoupdate_log_ajax();
            break;

        case "downloadlog":
            download_autoupdate_log();
            break;

        default:
            get_autoupdate_status(); // Check if an auto-update is running
            show_updates_page();
            break;
    }
    
    exit();
}

// Clean up filename for deleting/downloading
function au_clean_filename($filename) {
    $filename = str_replace("..", "", $filename);
    $filename = str_replace("/", "", $filename);
    $filename = str_replace("\\", "", $filename);
    return $filename;
}

// Function to download the update log
function download_autoupdate_log()
{
    $name = grab_request_var("name", "");

    if (!empty($name)) {
        $filename = $name . ".log";
        $thefile = AU_LOGDIR . au_clean_filename($filename);
        
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($thefile); 
     
        exit();
    }
}

// Get an update log to display (AJAX)
function get_autoupdateupdate_log_ajax()
{
    $name = grab_request_var("name");
    $logs = get_autoupgrade_log_list();

    foreach ($logs as $log) {
        if ($log['name'] == $name) {
            $log['contents'] = file_get_contents(AU_LOGDIR . $log['file']);
            print json_encode($log);
            die();
        }
    }
}

// Delete a log file (AJAX)
function delete_autoupdate_log_ajax()
{
    $name = grab_request_var("name", "");

    if (!empty($name)) {
        $filename = $name . ".log";
        $command_data = AU_LOGDIR . au_clean_filename($filename);
        $id = submit_command(COMMAND_DELETE_UPGRADE_LOG, $command_data);
    }

    // send update
    print json_encode(array("success" => 1));
}

// Acknowledge the actual update having completed (AJAX)
function acknowledge_autoupdate_ajax()
{
    // Set the option
    set_option("last_update_acknowledged", "yes");
    print json_encode(array("success" => 1));
}

// Get a list of all the logs
function get_autoupgrade_log_list()
{
    $upgrades = array();
    if(is_dir(AU_LOGDIR)){
        $direntries = file_list(AU_LOGDIR, "/\.log/");
        foreach ($direntries as $de) {
            $logfile = $de;
            $name = str_replace(".log", "", $de);
            $ar = explode(".", $de);
            $ts = filemtime(AU_LOGDIR."/$logfile");
            $upgrades[] = array("file" => $logfile,
                                "name" => $name,
                                "status" => ucfirst($ar[0]),
                                "timestamp" => $ts,
                                "date" => get_datetime_string($ts));
        }
    }
    return $upgrades;
}

// Perform a Fusion upgrade using the Command Subsys (AJAX)
function start_autoupdate_ajax()
{
    global $locked;
    global $locked_subsys_id;
    global $cfg;
    
    // Set the last_update_acknowledged to "no" so that we can save output
    set_option("last_update_acknowledged", "no");

    // Remove feature in demo mode
    if (in_demo_mode() == true) {
        print json_encode(array('error' => 1, 'message' => gettext("This action is not available in Demo Mode.")));
    } else {
        // Send command to the backend!
        if($cfg['developer'] == true)
            $file = "https://assets.nagios.com/downloads/nagiosfusion/revision/".trim(file_get_contents('https://assets.nagios.com/downloads/nagiosfusion/revision/latest_rev.php'));
        else
            $file = "https://assets.nagios.com/downloads/nagiosfusion/fusion-latest.tar.gz";
        
        $command_data=array();
        $command_data[0] = $file;
        $command_data = serialize($command_data);
        
        $id = submit_command(COMMAND_UPDATE_FUSION_TO_LATEST, $command_data);
        print json_encode(array('command_id' => $id));
    }
}

// Check update status (AJAX)
function check_autoupdate_status()
{
    // Get the command id
    $command_id = grab_request_var("command_id");

    // Grab the command
    $args = array("cmd" => "getcommands",
                  "command_id" => $command_id);
    $xml = get_backend_xml_data($args);
    if ($xml) {
        $command = $xml->command[0];
    
        // Dump the logfile into PHP 
        $logfile = file_get_contents(AU_LOGFILE);
 
        if ($command->status_code != 2) { // Return running         
            print json_encode(array('running' => 1, 'stream' => $logfile));
        } else {
            if ($command->result_code == 0) {
                $msg = gettext("<strong>The update was completed successfully!</strong>");
                set_option("last_update_status", "complete");
                set_option("last_update_message", $msg);
                print json_encode(array('complete' => 1, 'message' => $msg, 'stream' => $logfile));
            } else {
                $msg = gettext("The update did not complete successfully. Please try updating manually.");
                set_option("last_update_status", "error");
                set_option("last_update_message", $msg);
                print json_encode(array('error' => 1, 'message' => $msg, 'stream' => $logfile));
            }
        }
    } else {
        print json_encode(array('error' => 1, 'message' => gettext("Command not found.")));
    }
}

// Check if there is an update available
function check_update_available()
{
    global $cfg;

    if ($cfg["developer"] == true) {
        $update_available = 1;
    } else {
        $update_available = get_option("update_available");
    }

    print json_encode(array("available" => $update_available));
}

// Get the update status
function get_autoupdate_status()
{
    global $locked;
    global $locked_subsys_id;

    // Check for any update commands ... We need to make sure it happens only once
    $args = array("cmd" => "getcommands");
    $xml = get_backend_xml_data($args);
    if ($xml) {
        foreach ($xml->command as $command) {
            if (intval($command->command) == COMMAND_UPDATE_FUSION_TO_LATEST) {
                if ($command->status_code == 1) {
                    $locked = true;
                    $locked_subsys_id = $command->attributes();
                }
            }
        }
    }
}

// Show the page that has the actual update checker and buttons
function show_updates_page()
{
    global $lstr;
    global $locked;
    global $locked_subsys_id;
    global $cfg;

    // Check to make sure we should display all the buttons
    $update_available = get_option("update_available");
    $last_update_acknowledged = get_option("last_update_acknowledged");
    $last_update_status = get_option("last_update_status");
    $last_update_message = get_option("last_update_message");
        
    do_page_start(array("page_title" => $lstr['UpdatesPageTitle']), true);

?>

<script type="text/javascript">
var command_id = <?php echo $locked_subsys_id; ?>;
var started_updating = 0;

$(document).ready(function() {

    // Set scrollbar
    $('#output').scrollTop($('#output')[0].scrollHeight);

    <?php if (!$locked && $last_update_acknowledged == "no") { ?>
        $('#update_dashlet').hide();
        $('#au_update_buttons').hide();
        $('#finish').show();
    <?php } ?>

    // Stop user from submitting unless they really want to update
    $('#perform_update').click(function() {
        if (!confirm("<?php echo gettext('Are you sure you want to upgrade Nagios Fusion?\n\nWarning: Overwriting Fusion files ... Please make backups of any edited files before running this upgrade. Does not include components unless it\'s a core Fusion component.'); ?>")) {
        } else {
            au_run_upgrade_command();
        }
    });

    <?php if ($locked) { ?>
    // If there is a command running, watch the command
    setInterval(au_watch_command, 2000);
    <?php } ?>

    // Testing the update button
    $('#check_for_update').click(function() {
        $(this).attr("disabled", true);
        $('#check_updates_spinner').show();
        au_update_dashlet();
    });

    // Display everything again
    $('#finish').click(function() {
        $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-acknowledge" }, function(data) {
            if (data.success == 1) {
                location.reload();
            }
        }, 'json');
    });

    // Delete the update history
    $('#update-history .delete').click(function() {
        var a = $(this);
        var conf = confirm("Are you sure you want to delete this update log file?");
        if (conf) {
            $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-deletelog", name:a.data('name') }, function(data) {
                if (data.success == 1) {
                    a.parents("tr").hide();
                }
            }, 'json');
        }
    });

    // View the update history
    $('#update-history .view').click(function() {
        var a = $(this);
        show_child_content_throbber();
        $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-getlog", name:a.data('name') }, function(data) {
            var text_header = "<?php echo gettext('View Update Log'); ?>";
            var update_ran = "<?php echo gettext('Update Ran:'); ?>" + " " + data.date;
            var update_status = "<?php echo gettext('Update Status:'); ?>" + " <Strong>" + data.status + "</strong>";
            var text_desc = update_status + " (" + update_ran + ")";

            var content = "<div id='popup_header'><b>" + text_header + "</b></div><div id='popup_data'><p>" + text_desc + "</p></div>";
            content += "<div><textarea style='width: 600px; height: 240px;' class='code'>" + data.contents + "</textarea></div>";
            
            //alert(content); 
            hide_child_content_throbber();
            set_child_popup_content(content);
            display_child_popup();
        }, 'json');
    });
});

// Send the actual command
function au_run_upgrade_command()
{
    $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-performupdate" }, function(data) {
        started_updating = 0;
        if (data.error == 1) {
            $('#update_error').html(data.message).show();
        } else {
            command_id = data.command_id;
            $('#update_dashlet').hide();
            $('#au_update_buttons').hide();
            $('#update-history').hide();
            $('#update_message').html("<?php echo gettext('<strong>Please wait.</strong> Upgrade is starting...'); ?>").show();
            setInterval(au_watch_command, 2000);
        }
    }, 'json');
}

// Watch the command, if it finishes we can be done
function au_watch_command()
{
    if (command_id > 0) // If command is running
    {
        $('#update_message').hide();
        $('#updating_container').show();
        $('#updating').show();
        $('#updating_text').fadeIn(600);
        $('#output').fadeIn(600);

        // If the update finishes, we should stop the updating
        $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-checkstatus", command_id:command_id }, function(data) {
            if (!data.running) {
                command_id = 0;
                $('#updating_container').removeClass('message2014');
                $('#updating').hide();

                if (data.error) {
                    $('#updating_text').addClass('error2014').html(data.message).show();
                    $('#finish').val('<?php echo gettext("Go Back"); ?>').show();
                } else if (data.complete == 1) {
                    $('#updating_text').addClass('ok2014').html(data.message).show();
                    $('#finish').val('<?php echo gettext("Complete Upgrade"); ?>').show();
                }
                
            }

            // Make sure to update data output
            $('#output').html(data.stream);
            $('#output').scrollTop($('#output')[0].scrollHeight);

        }, 'json');
    }
}

// Update the dashlet for available updates
function au_update_dashlet()
{
    var au_dashlet_id = $('.fusion_available_updates_dashlet').attr('id');
    $("#" + au_dashlet_id).each(function() {
        var optsarr = { "func": "get_available_updates_html",
                        "args": { "force": "yes" } }
        var opts = array2json(optsarr);
        get_ajax_data_innerHTML_with_callback("getfusioncoreajax", opts, true, this, "au_update_button");
    });
}

// Check if the button for updating should be shown or not
function au_update_button()
{
    $('#check_for_update').attr("disabled", false);
    $('#check_updates_spinner').hide();
    $.post("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", { action: "ajax-checkupdateavailable" }, function(data) {
        if (data.available == 1) {
            $('#update_button_container').attr('disabled', false).fadeIn(600);
        } else {
            $('#update_button_container').attr('disabled', true).fadeOut(600);
        }
    }, 'json');
}
</script>

    <h1><?php echo $lstr['UpdatesPageHeader'];?></h1>
    <p style="padding-bottom: 10px;"><?php echo $lstr['UpdatesPageNotes'];?></p>

    <div>
        <?php if (!$locked && $last_update_status == 'error' && $last_update_acknowledged == "no") { ?>
        <div id="update_error" class="error2014" style="display: block;"><?php echo $last_update_message; ?></div>
        <?php } else { ?>
        <div id="update_error" class="error2014"></div>
        <?php } // End errors ?>

        <?php if (!$locked && $last_update_status == 'complete' && $last_update_acknowledged == "no") { ?>
        <div id="update_message" class="ok2014" style="display: block;"><?php echo $last_update_message; ?></div>
        <?php } else { ?>
        <div id="update_message" class="message2014"></div>
        <?php } // End regular message ?>
        <div style="clear: both;"><div>
    </div>

    <?php
    // Make sure an upgrade isn't already in progress
    if (!$locked) {
    ?>

    <div id="au_update_buttons">
        <div>
            <div id="formButtons">
                <input type="button" class="submitbutton" value="<?php echo $lstr['CheckForUpdatesButton'];?>" id="check_for_update"> 
                <span id="check_updates_spinner" style="margin-left: 10px; display: none;"> <img src="../images/throbber.gif"> <?php echo gettext("Checking..."); ?></span>
            </div>
        </div>
        <div style="margin-top: 10px; <?php if (!$update_available && grab_array_var($cfg, 'developer', false) != true) { echo 'display: none;'; } ?>" id="update_button_container">
            <div id="formButtons">
                <input type="button" class="submitbutton" id="perform_update" value="<?php echo gettext('Upgrade to Latest Version');?>">
            </div>
        </div>
    </div>
    <div style="margin-top: 40px;" id="update_dashlet">
        <div>
            <?php display_dashlet("fusioncore_available_updates", "", null, DASHLET_MODE_OUTBOARD); ?>
        </div>
    </div>

    <?php } // End locked ?>

    <div>
        <div id="updating_container" class="message2014" style="<?php if ($locked) {  echo 'display: block;'; } ?>">
            <span id="updating" style="margin-right: 8px;"><img src="../images/throbber.gif"></span>
            <span id="updating_text" style="<?php if (!$locked) {  echo 'display: inline-block;'; } ?>"><?php echo gettext("<strong>Update in progress.</strong> Please wait. Update may take a few minutes."); ?></span>
        </div>
    </div>

    <div style="clear: both;"><div>

    <textarea id="output" class="code" style="width: 600px; height: 260px; margin-top: 20px; <?php if (!$locked && $last_update_acknowledged != "no") {  echo 'display: none;'; } ?>" readonly>
    <?php
    if ($locked || $last_update_acknowledged == "no") {
        $logfile = (file_exists(AU_LOGFILE) ? file_get_contents(AU_LOGFILE) : "");
        print $logfile;
    }
    ?>
    </textarea>

    <div>
        <?php if ($last_update_status == "complete") { ?>
        <input type="button" id="finish" style="margin-top: 20px; display: none;" class="submitbutton" value="<?php echo gettext('Complete Upgrade'); ?>">
        <?php } else { ?>
        <input type="button" id="finish" style="margin-top: 20px; display: none;" class="submitbutton" value="<?php echo gettext('Go Back'); ?>">
        <?php } ?>
    </div>

    <!-- Update History Section -->
    <div id="update-history" <?php if ($locked || $last_update_acknowledged != "yes") { echo 'style="display: none;"'; } ?>>
        <h2><?php echo gettext("Update History"); ?></h2>
        <p><?php echo gettext("History of all updates performed from the Fusion web UI."); ?></p>

        <table class="standardtable multi-color-table">
            <thead>
                <tr>
                    <th><?php echo gettext('Status'); ?></th>
                    <th><?php echo gettext('Date'); ?></th>
                    <th><?php echo gettext('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $logs = get_autoupgrade_log_list();
                if (count($logs) > 0) {
                    foreach ($logs as $log) {
                        ?>
                        <tr <?php if ($log['status'] == "Failed") { echo 'class="alert"'; } ?>>
                            <td><?php echo $log['status']; ?></td>
                            <td><?php echo $log['date']; ?></td>
                            <td>
                                <a href="#" class="view" data-name="<?php echo $log['name']; ?>"><img src="<?php echo theme_image("detail.png"); ?>" alt="<?php echo gettext("View"); ?>" title="<?php echo gettext("View"); ?>"></a> 
                                <a href="?download=<?php echo $log['name']; ?>" class="download"><img src="<?php echo theme_image("download.png"); ?>" alt="<?php echo gettext("Download"); ?>" title="<?php echo gettext("Download"); ?>"></a> 
                                <a href="#" class="delete" data-name="<?php echo $log['name']; ?>"><img src="<?php echo theme_image("delete.png"); ?>" alt="<?php echo gettext("Delete"); ?>" title="<?php echo gettext("Delete"); ?>"></a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="3"><?php echo gettext("No updates have been performed from the UI yet."); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

<?php

    do_page_end(true);
}