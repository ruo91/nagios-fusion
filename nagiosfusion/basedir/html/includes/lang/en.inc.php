<?php
// English (U.S.) language file
// $Id: en.inc.php 179 2010-06-22 21:53:04Z egalstad $

global $lstr;

$lstr['language_translation_complete']=true;

///////////////////////////////////////////////////////////////
// PAGE TITLES
///////////////////////////////////////////////////////////////
$lstr['MainPageTitle']="";
$lstr['MissingPageTitle']=gettext("Missing Page");
$lstr['MissingFeaturePageTitle']=gettext("Unimplemented Feature");
$lstr['LoginPageTitle']=gettext("Login");
$lstr['ResetPasswordPageTitle']=gettext("Reset Password");
$lstr['PasswordSentPageTitle']=gettext("Password Sent");
$lstr['InstallPageTitle']=gettext("Install");
$lstr['InstallErrorPageTitle']=gettext("Error");


///////////////////////////////////////////////////////////////
// PAGE HEADERS (H1 TAGS)
///////////////////////////////////////////////////////////////
$lstr['MissingPageHeader']=gettext("What the...");
$lstr['MissingFeaturePageHeader']=gettext("Wouldn't that be nice...");
$lstr['ForcedPasswordChangePageHeader']=gettext("Password Change Required");
$lstr['ResetPasswordPageHeader']=gettext("Reset Password");
$lstr['MainPageHeader']=gettext("Nagios Fusion&trade;");
$lstr['LoginPageHeader']=gettext("Login");
$lstr['PasswordSentPageHeader']=gettext("Password Sent");
$lstr['CreditsPageHeader']=gettext("Credits");
$lstr['LegalInfoPageHeader']=gettext("Legal Information");


///////////////////////////////////////////////////////////////
// H2 TAGS
///////////////////////////////////////////////////////////////
$lstr['FeedbackSendingHeader']=gettext("Sending Feedback...");
$lstr['FeedbackSuccessHeader']=gettext("Thank You!");
$lstr['FeedbackErrorHeader']=gettext("Error");

///////////////////////////////////////////////////////////////
// MENU ITEMS
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
// SUBMENU ITEMS
///////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////
// FORM LEGENDS
///////////////////////////////////////////////////////////////
$lstr['UpdateUserPrefsFormLegend']=gettext("Account Preferences");
$lstr['GeneralOptionsFormLegend']=gettext("General Options");
$lstr['UserAccountInfoFormLegend']=gettext("User Account Information");
$lstr['LoginPageLegend']=gettext("Login");



///////////////////////////////////////////////////////////////
// FORM/PAGE SECTION TITLES
///////////////////////////////////////////////////////////////
$lstr['GeneralProgramSettingsSectionTitle']=gettext("General Program Settings");
$lstr['DefaultUserSettingsSectionTitle']=gettext("Default User Settings");
$lstr["AdvancedProgramSettingsSectionTitle"]=gettext("Advanced Settings");


///////////////////////////////////////////////////////////////
// BUTTONS
///////////////////////////////////////////////////////////////
$lstr['LoginButton']=gettext("Login");
$lstr['ResetPasswordButton']=gettext("Reset Password");
$lstr['UpgradeButton']=gettext("Upgrade");
$lstr['InstallButton']=gettext("Install");
$lstr['ChangePasswordButton']=gettext("Change Password");
$lstr['UpdateButton']=gettext("Update");
$lstr['UpdateSettingsButton']=gettext("Update Settings");
$lstr['CancelButton']=gettext("Cancel");
$lstr['ContinueButton']=gettext("Continue");
$lstr['OkButton']=gettext("Ok");
$lstr['AddUserButton']=gettext("Add User");
$lstr['UpdateUserButton']=gettext("Update User");
$lstr['SubmitButton']=gettext("Submit");
$lstr['GoButton']=gettext("Go");
$lstr['UpdatePermsButton']=gettext("Update Permissions");
$lstr['UpdateDataSourceButton']=gettext("Update Settings");
$lstr['UploadFileButton']=gettext("Upload File");
$lstr['UploadPluginButton']=gettext("Upload Plugin");
$lstr['CheckForUpdatesButton']=gettext("Check For Updates Now");


///////////////////////////////////////////////////////////////
// INPUT TEXT TITLE
///////////////////////////////////////////////////////////////
$lstr['UsernameBoxTitle']=gettext("Username");
$lstr['Password1BoxTitle']=gettext("Password");
$lstr['NewPassword1BoxTitle']=gettext("New Password");
$lstr['NewPassword2BoxTitle']=gettext("Repeat New Password");
$lstr['Password2BoxTitle']=gettext("Repeat Password");
$lstr['AdminEmailBoxText']=gettext("Admin Email Address");
$lstr['EmailBoxTitle']=gettext("Email Address");
$lstr['DefaultLanguageBoxTitle']=gettext("Language");
$lstr['DefaultThemeBoxTitle']=gettext("Theme");
$lstr['NameBoxTitle']=gettext("Name");
$lstr['AuthorizationLevelBoxTitle']=gettext("Authorization Level");
$lstr['ForcePasswordChangeNextLoginBoxTitle']=gettext("Force Password Change at Next Login");
$lstr['SendAccountInfoEmailBoxTitle']=gettext("Email User Account Information");
$lstr['SendAccountPasswordEmailBoxTitle']=gettext("Email User New Password");
$lstr['DefaultDateFormatBoxTitle']=gettext("Date Format");
$lstr['DefaultNumberFormatBoxTitle']=gettext("Number Format");
$lstr['FeedbackCommentBoxText']=gettext("Comments");
$lstr['FeedbackNameBoxTitle']=gettext("Your Name (Optional)");
$lstr['FeedbackEmailBoxTitle']=gettext("Your Email Address (Optional)");

///////////////////////////////////////////////////////////////
// ERROR MESSAGES
///////////////////////////////////////////////////////////////
$lstr['InvalidUsernamePasswordError']=gettext("Invalid username or password.");
$lstr['NoUsernameError']=gettext("No username specified.");
$lstr['NoMatchingAccountError']=gettext("No account was found by that name.");
$lstr['UnableAccountEmailError']=gettext("Unable to get account email address.");
$lstr['UnableAdminEmailError']=gettext("Unable to get admin email address.");
$lstr['InvalidEmailAddressError']=gettext("Email address is invalid.");
$lstr['BlankUsernameError']=gettext("Username is blank.");
$lstr['BlankEmailError']=gettext("Email address is blank.");
$lstr['InvalidEmailError']=gettext("Email address is invalid.");
$lstr['BlankPasswordError']=gettext("Password is blank.");
$lstr['BlankSecurityLevelError']=gettext("Security level is blank.");
$lstr['AccountNameCollisionError']=gettext("An account with that username already exists.");
$lstr['AddAccountFailedError']=gettext("Failed to add account");
$lstr['AddAccountPrivilegesFailedError']=gettext("Unable to assign account privileges.");
$lstr['BlankURLError']=gettext("URL is blank.");
$lstr['MismatchedPasswordError']=gettext("Passwords do not match.");
$lstr['BlankDefaultLanguageError']=gettext("Default language not specified.");
$lstr['BlankDefaultThemeError']=gettext("Default theme not specified.");
$lstr['BlankNameError']=gettext("Name is blank.");
$lstr['InvalidURLError']=gettext("Invalid URL.");
$lstr['BadUserAccountError']=gettext("User account was not found.");
$lstr['BlankAuthLevelError']=gettext("Blank authorization level.");
$lstr['InvalidAuthLevelError']=gettext("Invalid authorization level.");
$lstr['BlankUserAccountError']=gettext("User account was not specified.");
$lstr['CannotDeleteOwnAccountError']=gettext("You cannot delete your own account.");
$lstr['NoUserAccountSelectedError']=gettext("No account selected.");
$lstr['InvalidUserAccountError']=gettext("Invalid account.");
$lstr["NoAdminNameError"]=gettext("No admin name specified.");
$lstr["NoAdminEmailError"]=gettext("No admin email address specified.");
$lstr["InvalidAdminEmailError"]=gettext("Admin email address is invalid.");

///////////////////////////////////////////////////////////////
// SHORT LINK TEXT
///////////////////////////////////////////////////////////////
$lstr['LegalLinkText']=gettext("Legal Info");
$lstr['CreditsLinkText']=gettext("Credits");
$lstr['AboutLinkText']=gettext("About");
$lstr['PrivacyPolicyLinkText']=gettext("Privacy Policy");
$lstr['CheckForUpdatesLinkText']=gettext("Check for Updates");

$lstr['FirstPageText']=gettext("First Page");
$lstr['LastPageText']=gettext("Last Page");
$lstr['NextPageText']=gettext("Next Page");
$lstr['PreviousPageText']=gettext("Previous Page");
$lstr['PageText']=gettext("Page");


///////////////////////////////////////////////////////////////
// TABLE HEADERS
///////////////////////////////////////////////////////////////
$lstr['UsernameTableHeader']=gettext("Username");
$lstr['NameTableHeader']=gettext("Name");
$lstr['EmailTableHeader']=gettext("Email");
$lstr['ActionsTableHeader']=gettext("Actions");
$lstr['DateTableHeader']=gettext("Date");
$lstr['ResultTableHeader']=gettext("Result");
$lstr['FileTableHeader']=gettext("File");
$lstr['OutputTableHeader']=gettext("Output");
$lstr['SnapshotResultTableHeader']=gettext("Snapshot Result");
$lstr['VersionTableHeader']=gettext("Version");
$lstr['StatusTableHeader']=gettext("Status");



///////////////////////////////////////////////////////////////
// SHORT TEXT
///////////////////////////////////////////////////////////////
$lstr['MissingPageText']=gettext("The page that went missing was: ");
$lstr['MissingFeatureText']=gettext("We're currently working on this feature.  Until it's completed, you can't have it!  Seriously though - just sit tight for a while and we'll get it done.");
$lstr['LoginText']=gettext("Login");
$lstr['LogoutText']=gettext("Logout");
$lstr['ForgotPasswordText']=gettext("Forgot your password?");
$lstr['LoggedOutText']=gettext("You have logged out.");
$lstr['TryInstallAgainText']=gettext("Try Again");
$lstr['UsernameText']=gettext("Username");
$lstr['PasswordText']=gettext("Password");
$lstr['AdminPasswordText']=gettext("Administrator Password");
$lstr['ErrorText']=gettext("Error");
$lstr['QueryText']=gettext("Query");
$lstr['LanguageText']=gettext("Language");
$lstr['ThemeText']=gettext("Theme");
$lstr['LoggedInAsText']=gettext("Logged in as");
$lstr['MenuText']=gettext("Menu");
$lstr['UserPrefsUpdatedText']=gettext("Settings Updated.");
$lstr['YesText']=gettext("Yes");
$lstr['NoText']=gettext("No");
$lstr['GeneralOptionsUpdatedText']=gettext("Options Updated.");
$lstr['OptionsUpdatedText']=gettext("Options Updated.");
$lstr['UserUpdatedText']=gettext("User Updated.");
$lstr['UserAddedText']=gettext("User Added.");
$lstr['UserDeletedText']=gettext("User Deleted.");
$lstr['UsersDeletedText']=gettext("Users Deleted.");
$lstr['AddNewUserText']=gettext("Add New User");
$lstr['SessionTimedOut']=gettext("Your session has timed out.");
$lstr['SearchBoxText']=gettext("Search...");
$lstr['WithSelectedText']=gettext("With Selected:");
$lstr['CheckForUpdateNowText']=gettext("Check Now");
$lstr['YourVersionIsUpToDateText']=gettext("Your version is up to date.");
$lstr['AnUpdateIsAvailableText']=gettext("An update is available.");
$lstr['NewVersionInformationText']=gettext("New version information");
$lstr['CurrentVersionInformationText']=gettext("Your current version");
$lstr['NoticesText']=gettext("Notices");
$lstr['AdminLevelText']=gettext("Admin");
$lstr['UserLevelText']=gettext("User");
$lstr['ContinueText']=gettext("Continue");
$lstr['CancelText']=gettext("Cancel");
$lstr['PerPageText']=gettext("Per Page");

$lstr['NeverText']=gettext("N/A");
$lstr['NotApplicableText']=gettext("N/A");





///////////////////////////////////////////////////////////////
// PARTING/SUBSTRING TEXT
///////////////////////////////////////////////////////////////
$lstr['TotalRecordsSubText']=gettext("total records");
$lstr['TotalMatchesForSubText']=gettext("total matches for");
$lstr['ShowingSubText']=gettext("Showing");
$lstr['OfSubText']=gettext("of");
$lstr['YourAreRunningVersionText']=gettext("You are currently running");
$lstr['WasReleasedOnText']=gettext("was released on");


///////////////////////////////////////////////////////////////
// LONGER TEXT/NOTES
///////////////////////////////////////////////////////////////
$lstr['MissingPageNote']=gettext("The page you requested seems to be missing.  It is theoretically possible - though highly unlikely - that we are to blame for this.  It is far more likely that something is wrong with the Universe.  Run for it!");
$lstr['ResetPasswordNote']=gettext("Enter your username to have your password reset and emailed to you.");
$lstr['PasswordSentNote']=gettext("Your account password has been reset and emailed to you.");
$lstr['AlreadyInstalledNote']=gettext("Nagios Reports is already installed and up-to-date.");
$lstr['UpgradeRequiredNote']=gettext("Your installation requires an upgrade.  Click the button below to begin.");
$lstr['UpgradeErrorNote']=gettext("One or more errors were encountered:");
$lstr['InstallRequiredNote']=gettext("Nagios Reports has not yet been setup.  Complete the form below to install it.");
$lstr['InstallErrorNote']=gettext("One or more errors were encountered:");
$lstr['InstallFatalErrorNote']=gettext("One or more fatal errors were encountered during the installation process:");
$lstr['UpgradeCompleteNote']=gettext("Upgrade is complete!");
$lstr['InstallCompleteNote']=gettext("Installation is complete!  You can now login with the following credentials:");
$lstr['SQLQueryErrorNote']=gettext("An error occurred while executing the following SQL query.");
$lstr['UnableConnectDBErrorNote']=gettext("Unable to connect to database");
$lstr['ForceChangePasswordNote']=gettext("You are required to change your password before proceeding.");
$lstr['FeedbackSendIntroText']=gettext("We love input!  Tell us what you think about this product and you'll directly drive future innovation!");
$lstr['FeedbackSendingMessage']=gettext("Please wait...");
$lstr['FeedbackSuccessMessage']=gettext("Thanks for helping to make this product better!  We'll review your comments as soon as we get a chance.  Until then, kudos to you for being awesome and helping drive innovation!<br><br>   - The Dedicated Team @ Nagios Enterprises");
$lstr['FeedbackErrorMessage']=gettext("An error occurred.  Please try again later.");




///////////////////////////////////////////////////////////////
// EMAIL 
///////////////////////////////////////////////////////////////

$lstr['PasswordResetEmailSubject']=gettext("Nagios Fusion Password Reset");
$lstr['PasswordChangedEmailSubject']=gettext("Nagios Fusion Password Changed");
$lstr['AccountCreatedEmailSubject']=gettext("Nagios Fusion Account Created");

$lstr['PasswordResetEmailMessage']=gettext("Your Nagios Fusion account password has been reset to:\n\n%s\n\nYou can login to Nagios Fusion at the following URL:\n\n%s\n\n");

$lstr['PasswordChangedEmailMessage']=gettext("Your Nagios Fusion account password has been changed by an administrator.  You can login using the following information:\n\nUsername: %s\nPassword: %s\nURL: %s\n\n");

$lstr['AccountCreatedEmailMessage']=gettext("An account has been created for you to access Nagios Fusion.  You can login using the following information:\n\nUsername: %s\nPassword: %s\nURL: %s\n\n");


///////////////////////////////////////////////////////////////
// TOOLTIP TEXT
///////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////
// IMAGE ALT/TITLE TEXT
///////////////////////////////////////////////////////////////
$lstr['EditAlt']=gettext("Edit");
$lstr['DeleteAlt']=gettext("Delete");
$lstr['ClearSearchAlt']=gettext("Clear Search Criteria");
$lstr['CloseAlt']=gettext("Close");
$lstr['PermissionsAlt']=gettext("Permissions");
$lstr['CustomizePermsAlt']=gettext("Customize Permissions");
$lstr['MasqueradeAlt']=gettext("Masquerade As");
$lstr['ViewAlt']=gettext("View");
$lstr['PopoutAlt']=gettext("Popout");
$lstr['AddToMyViewsAlt']=gettext("Add to My Views");
$lstr['AddViewAlt']=gettext("Add View");
$lstr['EditViewAlt']=gettext("Edit View");
$lstr['DeleteViewAlt']=gettext("Delete View");
$lstr['SendFeedbackAlt']=gettext("Send Us Feedback");
$lstr['GetPermalinkAlt']=gettext("Get Permalink");
$lstr['DownloadAlt']=gettext("Download");
$lstr['ViewOutputAlt']=gettext("View Output");
$lstr['ViewHostNotificationsAlt']=gettext("View Host Notifications");
$lstr['ViewHostStatusAlt']=gettext("View Current Host Status");
$lstr['ViewHostServiceStatusAlt']=gettext("View Current Status of Host Services");
$lstr['ViewServiceNotificationsAlt']=gettext("View Service Notifications");
$lstr['ViewServiceStatusAlt']=gettext("View Current Service Status");
$lstr['ViewHostServiceStatusAlt']=gettext("View Current Status For Host Services");
$lstr['ViewHostHistoryAlt']=gettext("View Host History");
$lstr['ViewServiceHistoryAlt']=gettext("View Service History");
$lstr['ViewHostTrendsAlt']=gettext("View Host Trends");
$lstr['ViewServiceTrendsAlt']=gettext("View Service Trends");
$lstr['ViewHostAvailabilityAlt']=gettext("View Host Availability");
$lstr['ViewServiceAvailabilityAlt']=gettext("View Service Availability");
$lstr['ViewHostHistogramAlt']=gettext("View Host Alert Histogram");
$lstr['ViewServiceHistogramAlt']=gettext("View Service Alert Histogram");
$lstr['RefreshAlt']=gettext("Refresh");
$lstr['ForceRefreshAlt']=gettext("Force Refresh");
$lstr['ClearFilterAlt']=gettext("Clear Filter");
$lstr['EditSettingsAlt']=gettext("Edit Settings");


///////////////////////////////////////////////////////////////
// DATE FORMAT TYPES
///////////////////////////////////////////////////////////////
$lstr['DateFormatISO8601Text']=gettext("YYYY-MM-DD HH:MM:SS");
$lstr['DateFormatUSText']=gettext("MM/DD/YYYY HH:MM:SS");
$lstr['DateFormatEuroText']=gettext("DD/MM/YYYY HH:MM:SS");


///////////////////////////////////////////////////////////////
// NUMBER FORMAT TYPES
///////////////////////////////////////////////////////////////
$lstr['NumberFormat1Text']=gettext("1000.00");
$lstr['NumberFormat2Text']=gettext("1,000.00");
$lstr['NumberFormat3Text']=gettext("1.000,00");
$lstr['NumberFormat4Text']=gettext("1 000,00");
$lstr['NumberFormat5Text']=gettext("1'000,00");


///////////////////////////////////////////////////////////////
// OBJECT TYPES
///////////////////////////////////////////////////////////////
$lstr['HostObjectText']=gettext("Host");
$lstr['HostGroupObjectText']=gettext("Host Group");
$lstr['ServiceObjectText']=gettext("Service");
$lstr['ServiceGroupObjectText']=gettext("Service Group");
$lstr['HostEscalationObjectText']=gettext("Host Escalation");
$lstr['ServiceEscalationObjectText']=gettext("Service Escalation");
$lstr['HostDependencyObjectText']=gettext("Host Dependency");
$lstr['ServiceDependencyObjectText']=gettext("Service Dependency");
$lstr['TimeperiodObjectText']=gettext("Timeperiod");
$lstr['ContactObjectText']=gettext("Contact");
$lstr['ContactGroupObjectText']=gettext("Contact Group");
$lstr['CommandObjectText']=gettext("Command");

$lstr['HostObjectPluralText']=gettext("Hosts");
$lstr['HostGroupObjectPluralText']=gettext("Host Groups");
$lstr['ServiceObjectPluralText']=gettext("Services");
$lstr['ServiceGroupObjectPluralText']=gettext("Service Groups");
$lstr['HostEscalationObjectPluralText']=gettext("Host Escalations");
$lstr['ServiceEscalationObjectPluralText']=gettext("Service Escalations");
$lstr['HostDependencyObjectPluralText']=gettext("Host Dependencies");
$lstr['ServiceDependencyObjectPluralText']=gettext("Service Dependencies");
$lstr['TimeperiodObjectPluralText']=gettext("Timeperiods");
$lstr['ContactObjectPluralText']=gettext("Contacts");
$lstr['ContactGroupObjectPluralText']=gettext("Contact Groups");
$lstr['CommandObjectPluralText']=gettext("Commands");


///////////////////////////////////////////////////////////////
// STATE AND CHECK TYPES
///////////////////////////////////////////////////////////////
$lstr['HostStatePendingText']=gettext("Pending");
$lstr['HostStateUnknownText']=gettext("Unknown");
$lstr['HostStateUpText']=gettext("Up");
$lstr['HostStateDownText']=gettext("Down");
$lstr['HostStateUnreachableText']=gettext("Unreachable");

$lstr['ServiceStatePendingText']=gettext("Pending");
$lstr['ServiceStateOkText']=gettext("Ok");
$lstr['ServiceStateWarningText']=gettext("Warning");
$lstr['ServiceStateUnknownText']=gettext("Unknown");
$lstr['ServiceStateCriticalText']=gettext("Critical");

$lstr['HardStateText']=gettext("Hard");
$lstr['SoftStateText']=gettext("Soft");

$lstr['PassiveCheckText']=gettext("Passive");
$lstr['ActiveCheckText']=gettext("Active");



///////////////////////////////////////////////////////////////
// UNSORTED MISC
///////////////////////////////////////////////////////////////
$lstr['FeedbackPopupTitle']=gettext("Send Us Feedback");
$lstr['AjaxErrorHeader']=gettext("Error");
$lstr['AjaxErrorMessage']=gettext("An error occurred processing your request. :-(");
$lstr['AjaxSendingHeader']=gettext("Please Wait");
$lstr['AjaxSendingMessage']=gettext("Processing...");

$lstr['AddToMyViewsHeader']=gettext("Add View");
$lstr['AddToMyViewsMessage']=gettext("Use this to add what you see on the screen to your <b>Views</b> page.");
$lstr['AddToMyViewsSuccessHeader']=gettext("View Added");
$lstr['AddToMyViewsSuccessMessage']=gettext("Success! Your view was added to your <b>Views</b> page.");
$lstr['AddToMyViewsTitleBoxTitle']=gettext("View Title");

$lstr['AddViewHeader']=gettext("Add View");
$lstr['AddViewMessage']="";
$lstr['AddViewSuccessHeader']=gettext("View Added");
$lstr['AddViewSuccessMessage']=gettext("Success! Your view was added to your <b>Views</b> page.");
$lstr['AddViewURLBoxTitle']=gettext("URL");
$lstr['AddViewTitleBoxTitle']=gettext("View Title");

$lstr['EditViewHeader']=gettext("Edit View");
$lstr['EditViewMessage']="";
$lstr['EditViewSuccessHeader']=gettext("View Changed");
$lstr['EditViewSuccessMessage']=gettext("Success! Your view was updated successfully.");
$lstr['EditViewURLBoxTitle']=gettext("URL");
$lstr['EditViewTitleBoxTitle']=gettext("View Title");

$lstr['PermalinkHeader']=gettext("Permalink");
$lstr['PermalinkMessage']=gettext("Copy the URL below to retain a direct link to your current view.");
$lstr['PermalinkURLBoxTitle']=gettext("URL");

$lstr['MyViewsPageTitle']=gettext("My Views");
$lstr['NoViewsDefinedPageHeader']=gettext("No Views Defined");
$lstr['NoViewsDefinedText']=gettext("You have no views defined.");

$lstr['MyDashboardsPageTitle']=gettext("My Dashboards");
$lstr['NoDashboardsDefinedPageHeader']=gettext("No Dashboards Defined");
$lstr['NoDashboardsDefinedText']=gettext("You have no dashboards defined.");

$lstr['AddDashboardAlt']=gettext("Add A New Dashboard");
$lstr['EditDashboardAlt']=gettext("Edit Dashboard");
$lstr['DeleteDashboardAlt']=gettext("Delete Dashboard");

$lstr['PauseAlt']=gettext("Pause");

$lstr['AvailableDashletsPageTitle']=gettext("Available Dashlets");
$lstr['AvailableDashletsPageHeader']=gettext("Available Dashlets");
$lstr['AvailableDashletsPageText']=gettext("The following dashlets can be added to any one or more of your dashboards.  How awesome!");

$lstr['AddDashboardHeader']=gettext("Add Dashboard");
$lstr['AddDashboardMessage']=gettext("Use this to add a new dashboard to your <b>Dashboards</b> page.");
$lstr['AddDashboardTitleBoxTitle']=gettext("Dashboard Title");
$lstr['AddDashboardSuccessHeader']=gettext("Dashboard Added");
$lstr['AddDashboardSuccessMessage']=gettext("Success! Your new dashboard has been added.");

$lstr['EditDashboardHeader']=gettext("Edit Dashboard");
$lstr['EditDashboardMessage']="";
$lstr['EditDashboardSuccessHeader']=gettext("Dashboard Changed");
$lstr['EditDashboardSuccessMessage']=gettext("Success! Your dashboard was updated successfully.");
$lstr['EditDashboardTitleBoxTitle']=gettext("Dashboard Title");

$lstr['DeleteDashboardHeader']=gettext("Confirm Dashboard Deletion");
$lstr['DeleteDashboardMessage']=gettext("Are you sure you want to delete this dashboard and all dashlets it contains?");
$lstr['DeleteDashboardSuccessHeader']=gettext("Dashboard Deleted");
$lstr['DeleteDashboardSuccessMessage']=gettext("The requested dashboard has been deleted.  Good riddance!");

$lstr['DeleteButton']=gettext("Delete");

$lstr['BadDashboardPageTitle']=gettext("Bad Dashboard");
$lstr['BadDashboardPageHeader']=gettext("Bad Dashboard");
$lstr['BadDashboardText']=gettext("Unfortunately for you, that dashboard is not valid...  Too bad.");

$lstr['ViewDeletedHeader']=gettext("View Deleted");
$lstr['ViewDeletedMessage']=gettext("Good riddance!");

$lstr['AddToDashboardHeader']=gettext("Add To Dashboard");
$lstr['AddToDashboardMessage']=gettext("Add this powerful little dashlet to one of your dashboards for visual goodness.");
$lstr['AddToDashboardTitleBoxTitle']=gettext("Dashlet Title");

$lstr['AddToDashboardSuccessHeader']=gettext("Dashlet Added");
$lstr['AddToDashboardSuccessMessage']=gettext("The little dashlet that could will now be busy at work on your dashboard...");
$lstr['AddToDashboardDashboardSelectTitle']=gettext("Which Dashboard?");

$lstr['ViewsPageTitle']=gettext("Views");
$lstr['AdminPageTitle']=gettext("Admin");
$lstr['DashboardsPageTitle']=gettext("Dashboards");
$lstr['SubcomponentsPageTitle']=gettext("Addons");
$lstr['SubcomponentsPageHeader']=gettext("Addons");

$lstr['NoViewsToDeleteHeader']=gettext("No View");
$lstr['NoViewsToDeleteMessage']=gettext("There is no active view to delete.");
$lstr['NoViewsToEditHeader']=gettext("No View");
$lstr['NoViewsToEditMessage']=gettext("There is no active view to edit.");

$lstr['NoDashboardsToDeleteHeader']=gettext("No Dashboard");
$lstr['NoDashboardsToDeleteMessage']=gettext("There is no active dashboard to delete.");
$lstr['NoDashboardsToEditHeader']=gettext("No Dashboard");
$lstr['NoDashboardsToEditMessage']=gettext("There is no active dashboard to edit.");

$lstr['AddItButton']=gettext("Add It");

$lstr["DashletDeletedHeader"]=gettext("Dashlet Deleted");
$lstr["DashletDeletedMessage"]=gettext("Good riddance!");

$lstr["PinFloatDashletAlt"]=gettext("Pin / Float Dashlet");
$lstr["ConfigureDashletAlt"]=gettext("Configure Dashlet");
$lstr["DeleteDashletAlt"]=gettext("Delete Dashlet");
$lstr["DashboardBackgroundColorTitle"]=gettext("Background Color");


$lstr["AccountSettingsPageTitle"]=gettext("Account Information");
$lstr["AccountSettingsPageHeader"]=gettext("Account Information");
$lstr["MyAccountSettingsSectionTitle"]=gettext("General Account Settings");
$lstr["MyAccountPreferencesSectionTitle"]=gettext("Account Preferences");

$lstr["NotificationPrefsPageTitle"]=gettext("Notification Preferences");
$lstr["NotificationPrefsPageHeader"]=gettext("Notification Preferences");


$lstr["IngoreUpdateNotices"]=gettext("Ignore Update Notices");
$lstr["DemoModeChangeError"]=gettext("Changes are disabled while in demo mode.");

$lstr["GlobalConfigPageTitle"]=gettext("System Settings");
$lstr["AutoUpdateCheckBoxTitle"]=gettext("Automatically Check for Updates");

$lstr["ManageUsersPageTitle"]=gettext("Manage Users");
$lstr["ManageUsersPageHeader"]=gettext("Manage Users");

$lstr["ManageServersPageTitle"]=gettext("Manage Fused Servers");
$lstr["ManageServersPageHeader"]=gettext("Manage Fused Servers");

$lstr['ServerAddedText']=gettext("Server added.");
$lstr['ServerUpdatedText']=gettext("Server updated.");
$lstr['ServerDeletedText']=gettext("Server deleted.");
$lstr['ServersDeletedText']=gettext("servers deleted.");
$lstr['AddNewServerText']=gettext("Add a new server");

$lstr['UpdateServerButton']=gettext("Update Server");
$lstr['AddServerButton']=gettext("Add Server");

$lstr['AddServerPageTitle']=gettext("Add Server");
$lstr['AddServerPageHeader']=gettext("Add Server");
$lstr['EditServerPageTitle']=gettext("Edit Server");
$lstr['EditServerPageHeader']=gettext("Edit Server");

$lstr['ServerNameBoxTitle']=gettext("Server Name");
$lstr['ServerAddressBoxTitle']=gettext("Server Address");
$lstr['PublicURLBoxTitle']=gettext("Public URL");
$lstr['InternalURLBoxTitle']=gettext("Internal URL");
$lstr['ServerTypeBoxTitle']=gettext("Server Type");
$lstr['ServerLocationBoxTitle']=gettext("Server Location");
$lstr['ServerNotesBoxTitle']=gettext("Server Notes");
$lstr['ServerAuthenticationMethodBoxTitle']=gettext("Authentication Method");


$lstr['NoMatchingRecordsFoundText']=gettext("Not Matching Records Found.");

$lstr['CloneAlt']=gettext("Clone");

$lstr['MasqueradeAlertHeader']=gettext("Masquerade Notice");
$lstr['MasqueradeMessageText']=gettext("You are about to masquerade as another user.  If you choose to continue you will be logged out of your current account and logged in as the selected user.  In the process of doing so, you may loose your admin privileges.");

$lstr['AddUserPageTitle']=gettext("Add New User");
$lstr['AddUserPageHeader']=gettext("Add New User");
$lstr['EditUserPageTitle']=gettext("Edit User");
$lstr['EditUserPageHeader']=gettext("Edit User");

$lstr['UserAccountGeneralSettingsSectionTitle']=gettext("General Settings");
$lstr['UserAccountPreferencesSectionTitle']=gettext("Preferences");
$lstr['UserAccountSecuritySettingsSectionTitle']=gettext("Security Settings");

$lstr['ProgramURLText']=gettext("Program URL");

$lstr['GlobalConfigUpdatedText']=gettext("Settings Updated.");

$lstr['AdminNameText']=gettext("Administrator Name");
$lstr['AdminEmailText']=gettext("Administrator Email Address");

$lstr['ForcePasswordChangePageTitle']=gettext("Password Change Required");

$lstr['CloneUserPageTitle']=gettext("Clone User");
$lstr['CloneUserPageHeader']=gettext("Clone User");
$lstr['CloneUserButton']=gettext("Clone User");

$lstr['UserClonedText']=gettext("User cloned.");

$lstr['CloneUserDescription']=gettext("Use this functionality to create a new user account that is an exact clone of another account on the system.  The cloned account will inherit all preferences, views, and dashboards of the original user.");

$lstr['SystemStatusPageTitle']=gettext("System Status");
$lstr['SystemStatusPageHeader']=gettext("System Status");
$lstr['MonitoringEngineStatusPageTitle']=gettext("Monitoring Engine Status");
$lstr['MonitoringEngineStatusPageHeader']=gettext("Monitoring Engine Status");

$lstr['CannotDeleteHomepageDashboardHeader']=gettext("Error");
$lstr['CannotDeleteHomepageDashboardMessage']=gettext("You cannot delete your home page dashboard.");

$lstr['CloneDashboardAlt']=gettext("Clone Dashboard");

$lstr['CloneButton']=gettext("Clone");

$lstr['CloneDashboardHeader']=gettext("Clone Dashboard");
$lstr['CloneDashboardMessage']=gettext("Use this to make an exact clone of the current dashboard and all its wonderful dashlets.");
$lstr['CloneDashboardSuccessHeader']=gettext("Dashboard Cloned");
$lstr['CloneDashboardSuccessMessage']=gettext("Dashboard successfully cloned.");
$lstr['CloneDashboardTitleBoxTitle']=gettext("New Title");

$lstr['CannotDeleteHomepageDashletHeader']=gettext("Error");
$lstr['CannotDeleteHomepageDashletMessage']=gettext("Deleting dashlets from the home page dashboard is disabled while in demo mode.");

$lstr['PerformanceGraphsPageTitle']=gettext("Performance Graphs");
$lstr['PerformanceGraphsPageHeader']=gettext("Performance Graphs");

$lstr['NoPerformanceGraphDataSourcesMessage']=gettext("There are no datasources to display for this service.");

$lstr['ClearDateAlt']=gettext("Clear Date");
$lstr['NotAuthorizedErrorText']=gettext("You are not authorized to access this feature.  Contact your Nagios Fusion administrator for more information, or to obtain access to this feature.");

$lstr['ReportsPageTitle']=gettext("Reports");
$lstr['ReportsPageHeader']=gettext("Reports");
$lstr['HelpPageTitle']=gettext("Help");
$lstr['HelpPageHeader']=gettext("Help");

$lstr['ApplyNagiosCoreConfigPageTitle']=gettext("Apply Configuration");
$lstr['ApplyNagiosCoreConfigPageHeader']=gettext("Apply Configuration");

$lstr['ApplyingNagiosCoreConfigPageTitle']=gettext("Applying Configuration");
$lstr['ApplyingNagiosCoreConfigPageHeader']=gettext("Applying Configuration");
$lstr['ApplyNagiosCoreConfigMessage']=gettext("Use this feature to apply any outstanding configuration changes to Nagios Core.  Changes will be applied and the monitoring engine will be restarted with the updated configuration.");

$lstr['ApplyConfigText']=gettext("Apply Configuration");
$lstr['TryAgainText']=gettext("Try Again");
$lstr['ApplyConfigSuccessMessage']=gettext("Success!  Nagios Core was restarted with an updated configuration.");
$lstr['ApplyConfigErrorMessage']=gettext("An error occurred while attempting to apply your configuration to Nagios Core.  Monitoring engine configuration files have been rolled back to their last known good checkpoint.");
$lstr['ViewConfigSuccessSnapshotMessage']=gettext("View configuration snapshots");
$lstr['ViewConfigErrorSnapshotMessage']=gettext("View a snapshot of this configuration error");

$lstr['AjaxSubmitCommandHeader']=gettext("Please Wait");
$lstr['AjaxSubmitCommandMessage']=gettext("Submitting command");

$lstr['HelpPageTitle']=gettext("Help for Nagios Fusion");
$lstr['HelpPageHeader']=gettext("Help for Nagios Fusion");
$lstr['HelpPageGeneralSectionTitle']=gettext("Get Help Online");
$lstr['HelpPageMoreOptionsSectionTitle']=gettext("More Options");

$lstr['AboutPageTitle']=gettext("About Nagios Fusion");
$lstr['AboutPageHeader']=gettext("About");

$lstr['LegalPageTitle']=gettext("Legal Information");
$lstr['LegalPageHeader']=gettext("Legal Information");

$lstr['LicensePageTitle']=gettext("License Information");
$lstr['LicensePageHeader']=gettext("License Information");


$lstr['AdminPageTitle']=gettext("Administration");
$lstr['AdminPageHeader']=gettext("Administration");


$lstr['HostStatusDetailPageTitle']=gettext("Host Status Detail");
$lstr['HostStatusDetailPageHeader']=gettext("Host Status Detail");
$lstr['ServiceStatusDetailPageTitle']=gettext("Service Status Detail");
$lstr['ServiceStatusDetailPageHeader']=gettext("Service Status Detail");
$lstr['ServiceGroupStatusPageTitle']=gettext("Service Group Status");
$lstr['ServiceGroupStatusPageHeader']=gettext("Service Group Status");
$lstr['HostGroupStatusPageTitle']=gettext("Host Group Status");
$lstr['HostGroupStatusPageHeader']=gettext("Host Group Status");
$lstr['HostStatusPageTitle']=gettext("Host Status");
$lstr['HostStatusPageHeader']=gettext("Host Status");
$lstr['ServiceStatusPageTitle']=gettext("Service Status");
$lstr['ServiceStatusPageHeader']=gettext("Service Status");
$lstr['TacticalOverviewPageTitle']=gettext("Tactical Overview");
$lstr['TacticalOverviewPageHeader']=gettext("Tactical Overview");
$lstr['OpenProblemsPageTitle']=gettext("Open Problems");
$lstr['OpenProblemsPageHeader']=gettext("Open Problems");
$lstr['HostProblemsPageTitle']=gettext("Host Problems");
$lstr['HostProblemsPageHeader']=gettext("Host Problems");
$lstr['ServiceProblemsPageTitle']=gettext("Service Problems");
$lstr['ServiceProblemsPageHeader']=gettext("Service Problems");

$lstr['HostNameTableHeader']=gettext("Host");
$lstr['ServiceNameTableHeader']=gettext("Service");
$lstr['StatusTableHeader']=gettext("Status");
$lstr['LastCheckTableHeader']=gettext("Last Check");
$lstr['CheckAttemptTableHeader']=gettext("Attempt");
$lstr['DurationTableHeader']=gettext("Duration");
$lstr['StatusInformationTableHeader']=gettext("Status Information");

$lstr['LicensePageTitle']=gettext("License Information");
$lstr['LicensePageHeader']=gettext("License Information");
$lstr['LicensePageMessage']="";

$lstr['LicenseKeySectionTitle']=gettext("License Key");
$lstr['LicenseTypeText']=gettext("License Type");
$lstr['LicenseTypeFreeText']=gettext("Free");
$lstr['LicenseTypeFreeNotes']=gettext("(Limited edition without support)");
$lstr['LicenseTypeLicensedText']=gettext("Licensed");
$lstr['LicenseInformationSectionTitle']=gettext("License Information");
$lstr['LicenseKeyText']=gettext("Your License Key");
$lstr['UpdateKeyButton']=gettext("Update Key");
$lstr['InvalidLicenseKeyError']=gettext("The license key you entered is not valid.");
$lstr['LicenseInformationUpdatedText']=gettext("License key updated successfully");
$lstr['LicenseExceededPageTitle']=gettext("License Exceeded");
$lstr['LicenseExceededPageHeader']=gettext("License Exceeded");
$lstr['LicenseExceededMessage']=gettext("You have exceeded your license, so this feature is not available.");

$lstr['AccountInfoPageTitle']=gettext("Account Information");

$lstr['NotificationMethodsSectionTitle']=gettext("Notification Methods");
$lstr['NotificationMethodsMessage']=gettext("Specify the methods by which you'd like to receive alert messages.  <br><b>Note:</b>These methods are only used if you have <a href='notifyprefs.php'>enabled notifications</a> for your account.");

$lstr['ReceiveNotificationsByEmail']=gettext("Email");
$lstr['ReceiveNotificationsByMobileTextMessage']=gettext("Mobile Phone Text Message");
$lstr['EnableNotifications']=gettext("Enable Notifications");
$lstr['EnableNotificationsMessage']=gettext("Choose whether or not you want to receive alert messages.  <br><b>Note:</b> You must specify which notification methods to use in the <a href='notifymethods.php'>notification methods</a> page.");
$lstr['EnableNotificationsSectionTitle']=gettext("Notification Status");
$lstr['MobileNumberBoxTitle']=gettext("Mobile Phone Number");
$lstr['MobileProviderBoxTitle']=gettext("Mobile Phone Carrier");
$lstr['InvalidMobileNumberError']=gettext("Invalid mobile phone number.");
$lstr['BlankMobileNumberError']=gettext("Missing mobile phone number.");
$lstr['NotificationsPrefsUpdatedText']=gettext("Notification preferences updated.");
$lstr['NotificationTypesSectionTitle']=gettext("Notification Types");
$lstr['NotificationTypesMessage']=gettext("Select the types of alerts you'd like to receive.");
$lstr['NotificationTimesSectionTitle']=gettext("Notification Times");
$lstr['NotificationTimesMessage']=gettext("Specify the times of day you'd like to receive alerts.");

$lstr['HostRecoveryNotificationsBoxTitle']=gettext("Host Recovery");
$lstr['HostDownNotificationsBoxTitle']=gettext("Host Down");
$lstr['HostUnreachableNotificationsBoxTitle']=gettext("Host Unreachable");
$lstr['HostFlappingNotificationsBoxTitle']=gettext("Host Flapping");
$lstr['HostDowntimeNotificationsBoxTitle']=gettext("Host Downtime");
$lstr['ServiceWarningNotificationsBoxTitle']=gettext("Service Warning");
$lstr['ServiceRecoveryNotificationsBoxTitle']=gettext("Service Recovery");
$lstr['ServiceUnknownNotificationsBoxTitle']=gettext("Service Unknown");
$lstr['ServiceCriticalNotificationsBoxTitle']=gettext("Service Critical");
$lstr['ServiceFlappingNotificationsBoxTitle']=gettext("Service Flapping");
$lstr['ServiceDowntimeNotificationsBoxTitle']=gettext("Service Downtime");

$lstr['NoNotificationMethodsSelectedError']=gettext("No notification methods selected.");
$lstr['InvalidTimeRangesError']=gettext("One or more time ranges is invalid.");
$lstr['BlankMobileProviderError']=gettext("No mobile carrier selected.");


$lstr['WeekdayBoxTitle']=array(
	0 => "Sunday",
	1 => "Monday",
	2 => "Tuesday",
	3 => "Wednesday",
	4 => "Thursday",
	5 => "Friday",
	6 => "Saturday",
	);

$lstr['FromBoxTitle']=gettext("From");
$lstr['ToBoxTitle']=gettext("To");

$lstr['AuthorizedForAllObjectsBoxTitle']=gettext("Can see all hosts and services");
$lstr['AuthorizedToConfigureObjectsBoxTitle']=gettext("Can (re)configure hosts and services");
$lstr['AuthorizedForAllObjectCommandsBoxTitle']=gettext("Can control all hosts and services");
$lstr['AuthorizedForMonitoringSystemBoxTitle']=gettext("Can see/control monitoring engine");
$lstr['AdvancedUserBoxTitle']=gettext("Can access advanced features");
$lstr['ReadonlyUserBoxTitle']=gettext("Has read-only access");

$lstr['NotAuthorizedPageTitle']=gettext("Not Authorized");
$lstr['NotAuthorizedPageHeader']=gettext("Not Authorized");
$lstr['NotAuthorizedForObjectMessage']=gettext("You are not authorized to view the requested object, or the object does not exist.");

$lstr['NotificationMessagesPageTitle']=gettext("Notification Messages");
$lstr['NotificationMessagesPageHeader']=gettext("Notification Messages");
$lstr['NotificationMessagesMessage']=gettext("Use this feature to customize the content of the notification messages you receive.");

$lstr['EmailNotificationMessagesSectionTitle']=gettext("Email Notifications");
$lstr['EmailNotificationMessagesMessage']=gettext("Specify the format of the host and service alert messages you receive via email.");

$lstr['MobileTextNotificationMessagesSectionTitle']=gettext("Mobile Text Notifications");
$lstr['MobileTextNotificationMessagesMessage']=gettext("Specify the format of the host and service alert messages you receive via mobile text message.");

$lstr['HostNotificationMessageSubjectBoxTitle']=gettext("Host Alert Subject");
$lstr['HostNotificationMessageBodyBoxTitle']=gettext("Host Alert Message");
$lstr['ServiceNotificationMessageSubjectBoxTitle']=gettext("Service Alert Subject");
$lstr['ServiceNotificationMessageBodyBoxTitle']=gettext("Service Alert Message");

$lstr['AgreeLicenseError']=gettext("You must agree to the license before using this software.");
$lstr['AgreeToLicenseBoxText']=gettext("I have read, understood, and agree to be bound by the terms of the license above.");

$lstr['AgreeLicensePageTitle']=gettext("License Agreement");
$lstr['AgreeLicensePageHeader']=gettext("License Agreement");
$lstr['AgreeLicenseNote']=gettext("You must agree to the Nagios Software License Terms and Conditions before continuing using this software.");

$lstr['InstallPageTitle']=gettext("Nagios Fusion Installer");
$lstr['InstallPageHeader']=gettext("Nagios Fusion Installer");
$lstr['InstallPageMessage']=gettext("Welcome to the Nagios Fusion installation.  Just answer a few simple questions and you'll be ready to go.");

$lstr['InstallCompletePageTitle']=gettext("Installation Complete");
$lstr['InstallCompletePageHeader']=gettext("Installation Complete");
$lstr['InstallCompletePageMessage']=gettext("Congratulations! You have successfully installed Nagios Fusion.");

$lstr['ConfigPageTitle']=gettext("Configuration");  // used twice
$lstr['ConfigOverviewPageTitle']=gettext("Configuration Options");
$lstr['ConfigOverviewPageHeader']=gettext("Configuration Options");
$lstr['ConfigOverviewPageNotes']=gettext("What would you like to configure?");

$lstr['ConfigAuthPageTitle']=gettext("Server Credentials");
$lstr['ConfigAuthPageHeader']=gettext("Server Credentials");
$lstr['ConfigAuthPageNotes']=gettext("You must configure authentication credentials for each server you wish to access in Nagios Fusion.  To prevent a server from being displayed elsewhere in Fusion, uncheck the server's <i>Display</i> checkbox.");

$lstr['MonitoringWizardPageHeader']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardPageTitle']=gettext("Monitoring Wizard");


$lstr['NextButton']=gettext("Next");
$lstr['BackButton']=gettext("Back");

$lstr['MonitoringWizardStep1PageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStep1PageHeader']=gettext("Monitoring Wizard - Step 1");
$lstr['MonitoringWizardStep1SectionTitle']="";
$lstr['MonitoringWizardStep1Notes']=gettext("Monitoring wizards guide you through the process of monitoring devices, servers, applications, services, and more.  Select the appropriate wizard below to get started.");

$lstr['MonitoringWizardStep2PageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStep2PageHeader']=gettext("Monitoring Wizard - Step 2");

$lstr['MonitoringWizardStep3PageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStep3PageHeader']=gettext("Monitoring Wizard - Step 3");

$lstr['MonitoringWizardStep4PageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStep4PageHeader']=gettext("Monitoring Wizard - Step 4");

$lstr['MonitoringWizardStep5PageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStep5PageHeader']=gettext("Monitoring Wizard - Step 5");

$lstr['MonitoringWizardStepFinalPageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardStepFinalPageHeader']=gettext("Monitoring Wizard - Final Step");

$lstr['MonitoringWizardCommitCompletePageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardCommitCompletePageHeader']=gettext("Monitoring Wizard");

$lstr['MonitoringWizardCommitSuccessSectionTitle']=gettext("Configuration Request Successful");
$lstr['MonitoringWizardCommitSuccessNotes']=gettext("Your configuration changes have been successfully applied to the monitoring engine.");

$lstr['MonitoringWizardCommitErrorSectionTitle']=gettext("Configuration Error");
$lstr['MonitoringWizardCommitErrorNotes']=gettext("An error occurred while attempting to apply your configuration to the monitoring engine.  Contact your Nagios administrator if this problem persists.");

$lstr['MonitoringWizardPermissionsErrorPageTitle']=gettext("Monitoring Wizard");
$lstr['MonitoringWizardPermissionsErrorPageHeader']=gettext("Monitoring Wizard - An Error Occurrred");

$lstr['MonitoringWizardPermissionsErrorSectionTitle']=gettext("Configuration Request Error");
$lstr['MonitoringWizardPermissionsErrorNotes']=gettext("An error occurred while attempting to modify the monitoring engine.  This error occurred because the wizard attempted to modify hosts or services that you do not have permission for.  Contact your Nagios Fusion administrator for more information.");

$lstr['NoConfigWizardSelectedError']=gettext("No wizard selected.");

$lstr['ApplyButton']=gettext("Apply");
$lstr['RunWizardAgainButton']=gettext("Run the Monitoring Wizard Again");

$lstr['ApplySettingsButton']=gettext("Apply Settings");

$lstr['QuickFind']=gettext("Quick Find");

$lstr['AdminPageNotes']=gettext("<p>Manage your Fusion installation with the administrative options available to you in this section.  Make sure you complete any setup tasks that are shown below before using your Fusion installation.</p>");

$lstr['SecurityCredentialsPageTitle']=gettext("Security Credentials");
$lstr['SecurityCredentialsPageHeader']=gettext("Security Credentials");

$lstr['SecurityCredentialsPageNotes']=gettext("<p>Use this form to reset various internal security credentials used by your Fusion system.  This is an important step to ensure your Fusion system does not use default passwords or tokens, which may leave it open to a security breach.</p>");

$lstr['ComponentCredentialsSectionTitle']=gettext("Component Credentials");

$lstr['ComponentCredentialsNote']=gettext("The credentials listed below are used to manage various aspects of the Fusion system.  Remember these passwords!");

$lstr['SubsystemCredentialsSectionTitle']=gettext("Sub-System Credentials");

$lstr['SubsystemCredentialsNote']=gettext("<p>You do not need to remember the credentials below, as they are only used internally by the Fusion system.</p>");

$lstr['SubsystemTicketText']=gettext("Fusion Subsystem Ticket");
$lstr['UpdateCredentialsButton']=gettext("Update Credentials");
$lstr['CurrentText']=gettext("Current");
$lstr['ConfigManagerBackendPasswordText']=gettext("Config Manager Backend Password");
$lstr['ConfigManagerAdminPasswordText']=gettext("New Config Manager Admin Password");
$lstr['ConfigManagerAdminUsernameText']=gettext("Admin Username");

$lstr["NoSubsystemTicketError"]=gettext("No subsystem ticket.");
$lstr["NoConfigBackendPasswordError"]=gettext("No config backend password.");

$lstr['SecurityCredentialsUpdatedText']=gettext("Security credentials updated successfully.");

$lstr['NagiosCoreBackendPasswordText']=gettext("Nagios Core Backend Password");

$lstr["NoNagiosCoreBackendPasswordError"]=gettext("No Nagios Core backend password.");

$lstr['AuditLogPageTitle']=gettext("Audit Log");
$lstr['AuditLogPageHeader']=gettext("Audit Log");
$lstr['AuditLogPageNotes']=gettext("The audit log provides admins with a record of changes that occur on the Fusion system, which is useful for ensuring your organization meets compliance requirements.");

$lstr['CoreConfigSnapshotsPageTitle']=gettext("Monitoring Configuration Snapshots");
$lstr['CoreConfigSnapshotsPageHeader']=gettext("Monitoring Configuration Snapshots");
$lstr['CoreConfigSnapshotsPageNotes']=gettext("The latest configuration snapshots of the Fusion monitoring engine are shown below.  Download the most recent snapshots as backups, or get vital information for troubleshooting configuration errors.");

$lstr['MonitoringPluginsPageTitle']=gettext("Monitoring Plugins");
$lstr['MonitoringPluginsPageHeader']=gettext("Monitoring Plugins");
$lstr['MonitoringPluginsPageNotes']=gettext("Manage the monitoring plugins and scripts that are installed on this system.  Use caution when deleting plugins or scripts, as they may cause your monitoring system to generate errors.");

$lstr["SelectFileBoxText"]=gettext("Browse File");
$lstr["UploadNewPluginBoxText"]=gettext("Upload A New Plugin");

$lstr['PluginUploadedText']=gettext("New plugin was installed successfully.");
$lstr['PluginUploadFailedText']=gettext("Plugin could not be installed - directory permissions may be incorrect.");

$lstr['PluginDeletedText']=gettext("Plugin deleted.");
$lstr['PluginDeleteFailedText']=gettext("Plugin delete failed - directory permissions may be incorrect.");
$lstr['NoPluginUploadedText']=gettext("No plugin selected for upload.");

$lstr['FilePermsTableHeader']=gettext("Permissions");
$lstr['FileOwnerTableHeader']=gettext("Owner");
$lstr['FileGroupTableHeader']=gettext("Group");

$lstr['ManageConfigWizardsPageTitle']=gettext("Manage Configuration Wizards");
$lstr['ManageConfigWizardsPageHeader']=gettext("Manage Configuration Wizards");
$lstr['ManageConfigWizardsPageNotes']=gettext("Manage the configuration wizards installed on this system.  You can find additional configuration wizards at <a href='http://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards' target='_blank'>Nagios Exchange</a>.  Need a custom configuration wizard created for your organization?  <a href='http://www.nagios.com/contact/' target='_blank'>Contact us</a> for pricing information.");

$lstr["UploadNewWizardBoxText"]=gettext("Upload A New Wizard");
$lstr['UploadWizardButton']=gettext("Upload Wizard");

$lstr['WizardNameTableHeader']=gettext("Wizard");
$lstr['WizardTypeTableHeader']=gettext("Wizard Type");

$lstr['NoWizardUploadedText']=gettext("No wizard selected for upload.");
$lstr['WizardUploadFailedText']=gettext("Wizard upload failed.");
$lstr['WizardScheduledForInstallText']=gettext("Wizard scheduled for installation.");
$lstr['WizardInstalledText']=gettext("Wizard installed.");
$lstr['WizardInstallFailedText']=gettext("Wizard installation failed.");
$lstr['WizardPackagingTimedOutText']=gettext("Wizard packaging timed out.");
$lstr['WizardScheduledForInstallationText']=gettext("Wizard scheduled for installation.");
$lstr['WizardDeletedText']=gettext("Wizard deleted.");
$lstr['WizardScheduledForDeletionText']=gettext("Wizard scheduled for deletion.");

$lstr['ManageDashletsPageTitle']=gettext("Manage Dashlets");
$lstr['ManageDashletsPageHeader']=gettext("Manage Dashlets");
$lstr['ManageDashletsPageNotes']=gettext("Manage the dashlets installed on this system.  You can find additional dashlets at <a href='http://exchange.nagios.org/directory/Addons/Dashlets' target='_blank'>Nagios Exchange</a>.<br>Need a custom dashlet created for your organization?  <a href='http://www.nagios.com/contact/' target='_blank'>Contact us</a> for pricing information.");



$lstr['UploadNewDashletBoxText']=gettext("Upload a New Dashlet");
$lstr['UploadDashletButton']=gettext("Upload Dashlet");

$lstr['DashletNameTableHeader']=gettext("Dashlet");

$lstr['DashletScheduledForInstallationText']=gettext("Dashlet scheduled for installation.");
$lstr['DashletUploadFailedText']=gettext("Dashlet upload failed.");
$lstr['DashletPackagingTimedOutText']=gettext("Dashlet packaging timed out.");
$lstr['DashletDeletedText']=gettext("Dashlet deleted.");
$lstr['DashletScheduledForDeletionText']=gettext("Dashlet scheduled for deletion.");
$lstr['DashletInstalledText']=gettext("Dashlet installed.");
$lstr['DashletInstallFailedText']=gettext("Dashlet installation failed.");

$lstr['ManageComponentsPageTitle']=gettext("Manage Components");
$lstr['ManageComponentsPageHeader']=gettext("Manage Components");
$lstr['ManageComponentsPageNotes']=gettext("Manage the components installed on this system.  You can find additional dashlets at <a href='http://exchange.nagios.org/directory/Addons/Components' target='_blank'>Nagios Exchange</a>.<br>Need a custom component created to extend Nagios Fusion's capabilities?  <a href='http://www.nagios.com/contact/' target='_blank'>Contact us</a> for pricing information.");

$lstr['ComponentDeletedText']=gettext("Component deleted.");
$lstr['ComponentScheduledForDeletionText']=gettext("Component scheduled for delettion.");
$lstr['ComponentUploadFailedText']=gettext("Component upload failed.");
$lstr['ComponentScheduledForInstallationText']=gettext("Component scheduled for installation.");
$lstr['ComponentInstalledText']=gettext("Component installed.");
$lstr['ComponentInstallFailedText']=gettext("Component installation failed.");
$lstr['ComponentPackagingTimedOutText']=gettext("Component packaging timed out.");

$lstr['ConfigSnapshotDeletedText']=gettext("Config snapshot deleted.");
$lstr['ConfigSnapshotScheduledForDeletionText']=gettext("Config snapshot deleted.");

$lstr["UploadNewComponentBoxText"]=gettext("Upload a New Component");
$lstr['UploadComponentButton']=gettext("Upload Component");

$lstr['ComponentNameTableHeader']=gettext("Component");
$lstr['ComponentTypeTableHeader']=gettext("Type");
$lstr['ComponentSettingsTableHeader']=gettext("Settings");

$lstr['ConfigureComponentPageTitle']=gettext("Component Configuration");
$lstr['ConfigureComponentPageHeader']=gettext("Component Configuration");

$lstr['ComponentSettingsUpdatedText']=gettext("Component settings updated.");

$lstr['ErrorSubmittingCommandText']=gettext("Error submitting command.");

$lstr['NotificationTestPageTitle']=gettext("Send Test Notifications");
$lstr['NotificationTestPageHeader']=gettext("Send Test Notifications");
$lstr['NotificationTestPageNotes']=gettext("Click the button below to send test notifications to your email and/or mobile phone.");

$lstr['SendTestNotificationsButton']=gettext("Send Test Notifications");

$lstr['MailSettingsPageTitle']=gettext("Mail Settings");
$lstr['MailSettingsPageHeader']=gettext("Mail Settings");
$lstr['MailSettingsPageMessage']=gettext("Modify the settings used by your Nagios Fusion system for sending informational messages.<br><b>Note:</b> Mail messages may fail to be delivered if your Fusion server does not have a valid DNS name.");

$lstr['MailSettingsUpdatedText']=gettext("Mail settings updated.");

$lstr['GeneralMailSettingsSectionTitle']=gettext("General Mail Settings");
$lstr['MailMethodBoxText']=gettext("Mail Method");
$lstr['MailFromAddressBoxText']=gettext("Send Mail From");

$lstr['SMTPSettingsSectionTitle']=gettext("SMTP Settings");

$lstr['SMTPHostBoxText']=gettext("Host");
$lstr['SMTPPortBoxText']=gettext("Port");
$lstr['SMTPUsernameBoxText']=gettext("Username");
$lstr['SMTPPasswordBoxText']=gettext("Password");
$lstr['SMTPSecurityBoxText']=gettext("Security");

$lstr['NoFromAddressError']=gettext("No from address specified.");
$lstr['NoSMTPHostError']=gettext("No SMTP host specified.");
$lstr['NoSMTPPortError']=gettext("No SMTP port specified.");

$lstr['EmailTestPageTitle']=gettext("Test Email Settings");
$lstr['EmailTestPageHeader']=gettext("Test Email Settings");
$lstr['EmailTestPageMessage']=gettext("Use this to test your mail settings.");
$lstr['SendTestEmailButton']=gettext("Send Test Email");

$lstr['NoPerformanceGraphsFoundForServiceText']=gettext("No performance graphs were found for this service.");
$lstr['NoPerformanceGraphsFoundForHostText']=gettext("No performance graphs were found for this host.");

$lstr['ServiceDetailsOverviewTab']=gettext("Overview");
$lstr['ServiceDetailsAdvancedTab']=gettext("Advanced");
$lstr['ServiceDetailsConfigureTab']=gettext("Configure");
$lstr['ServiceDetailsPerformanceGraphsTab']=gettext("Performance Graphs");

$lstr['HostDetailsOverviewTab']=gettext("Overview");
$lstr['HostDetailsAdvancedTab']=gettext("Advanced");
$lstr['HostDetailsConfigureTab']=gettext("Configure");
$lstr['HostDetailsPerformanceGraphsTab']=gettext("Performance Graphs");

$lstr['MonitoringProcessPageTitle']=gettext("Monitoring Process");
$lstr['MonitoringProcessPageHeader']=gettext("Monitoring Process");

$lstr['MonitoringPerformancePageTitle']=gettext("Monitoring Performance");
$lstr['MonitoringPerformancePageHeader']=gettext("Monitoring Performance");


$lstr['AcknowledgementCommentBoxText']=gettext("Your Comment");

$lstr['NetworkOutagesPageTitle']=gettext("Network Outages");
$lstr['NetworkOutagesPageHeader']=gettext("Network Outages");

$lstr['ViewHostgroupOverviewAlt']=gettext("View Hostgroup Overview");
$lstr['ViewHostgroupSummaryAlt']=gettext("View Hostgroup Summary");
$lstr['ViewHostgroupGridAlt']=gettext("View Hostgroup Grid");
$lstr['ViewHostgroupServiceStatusAlt']=gettext("View Hostgroup Service Details");
$lstr['ViewHostgroupCommandsAlt']=gettext("View Hostgroup Commands");

$lstr['ViewServicegroupOverviewAlt']=gettext("View Servicegroup Overview");
$lstr['ViewServicegroupSummaryAlt']=gettext("View Servicegroup Summary");
$lstr['ViewServicegroupGridAlt']=gettext("View Servicegroup Grid");
$lstr['ViewServicegroupServiceStatusAlt']=gettext("View Servicegroup Service Details");
$lstr['ViewServicegroupCommandsAlt']=gettext("View Servicegroup Commands");

$lstr['StatusMapPageTitle']=gettext("Network Status Map");
$lstr['StatusMapPageHeader']=gettext("Network Status Map");

$lstr['ViewStatusMapTreeAlt']=gettext("View Tree Map");
$lstr['ViewStatusMapBalloonAlt']=gettext("View Balloon Map");

$lstr['CommentsPageTitle']=gettext("Acknowledgements and Comments");
$lstr['CommentsPageHeader']=gettext("Acknowledgements and Comments");

$lstr['ConfirmDeleteServicePageTitle']=gettext("Confirm Service Deletion");
$lstr['ConfirmDeleteServicePageHeader']=gettext("Confirm Service Deletion");
$lstr['ConfirmDeleteServicePageNotes']=gettext("Are you sure you want to delete this service and remove it from the monitoring configuration?");

$lstr['DeleteServiceErrorPageTitle']=gettext("Service Deletion Error");
$lstr['DeleteServiceErrorPageHeader']=gettext("Service Deletion Error");

$lstr['ServiceDeleteScheduledPageTitle']=gettext("Service Deletion Scheduled");
$lstr['ServiceDeleteScheduledPageHeader']=gettext("Service Deletion Scheduled");

$lstr['ConfirmDeleteHostPageTitle']=gettext("Confirm Host Deletion");
$lstr['ConfirmDeleteHostPageHeader']=gettext("Confirm Host Deletion");
$lstr['ConfirmDeleteHostPageNotes']=gettext("Are you sure you want to delete this host and remove it from the monitoring configuration?");

$lstr['DeleteHostErrorPageTitle']=gettext("Host Deletion Error");
$lstr['DeleteHostErrorPageHeader']=gettext("Host Deletion Error");

$lstr['HostDeleteScheduledPageTitle']=gettext("Host Deletion Scheduled");
$lstr['HostDeleteScheduledPageHeader']=gettext("Host Deletion Scheduled");

$lstr['CreateUserAsContactBoxTitle']=gettext("Create as Monitoring Contact");

$lstr['UserIsNotContactNotificationPrefsErrorMessage']=gettext("Management of notification preferences is not available because your account is not configured to be a monitoring contact.  Contact your Nagios Fusion administrator for details.");
$lstr['UserIsNotContactNotificationMessagesErrorMessage']=gettext("Management of notification preferences is not available for your account.  Contact your Nagios Fusion administrator for details.");
$lstr['UserIsNotContactNotificationTestErrorMessage']=gettext("Testing notification messages is not available for your account.  Contact your Nagios Fusion administrator for details.");

$lstr['ReconfigureServicePageTitle']=gettext("Configure Service");
$lstr['ReconfigureServicePageHeader']=gettext("Configure Service");

$lstr['ReconfigureServiceCompletePageTitle']=gettext("Configure Service");
$lstr['ReconfigureServiceCompletePageHeader']=gettext("Configure Service");

$lstr['ReconfigureHostPageTitle']=gettext("Configure Host");
$lstr['ReconfigureHostPageHeader']=gettext("Configure Host");

$lstr['ReconfigureHostCompletePageTitle']=gettext("Configure Host");
$lstr['ReconfigureHostCompletePageHeader']=gettext("Configure Host");

$lstr['ReconfigureServiceSuccessSectionTitle']=gettext("Service Re-Configuration Successful");
$lstr['ReconfigureServiceSuccessNotes']=gettext("The service has successfully been re-configured with the new settings.");

$lstr['ReconfigureServiceErrorSectionTitle']=gettext("Service Re-Configuration Failed");
$lstr['ReconfigureServiceErrorNotes']=gettext("A failure occurred while attempting to re-configure the service with the new settings.");


$lstr['ReconfigureHostSuccessSectionTitle']=gettext("Host Re-Configuration Successful");
$lstr['ReconfigureHostSuccessNotes']=gettext("The host has successfully been re-configured with the new settings.");

$lstr['ReconfigureHostErrorSectionTitle']=gettext("Host Re-Configuration Failed");
$lstr['ReconfigureHostErrorNotes']=gettext("A failure occurred while attempting to re-configure the host with the new settings.");

$lstr['UpdatesPageTitle']=gettext("Updates");
$lstr['UpdatesPageHeader']=gettext("Updates");
$lstr['UpdatesPageNotes']=gettext("Ensure your IT infrastructure is monitored effectively by keeping up with the latest updates to Nagios Fusion.  Visit <a href='http://www.nagios.com/products/nagiosfusion/' target='_blank'>www.nagios.com</a> to get the latest versions of Nagios Fusion.");

$lstr['NotificationMethodsPageTitle']=gettext("Notification Methods");
$lstr['NotificationMethodsPageHeader']=gettext("Notification Methods");
$lstr['NotificationMethodsMessage']=gettext("Select the methods by which you'd like to receive host and service alerts.");

$lstr['NotificationsMethodsUpdatedText']=gettext("Notification methods updated.");

$lstr['BuiltInNotificationMethodsSectionTitle']=gettext("Built-In Notification Methods");
$lstr['AdditionalNotificationMethodsSectionTitle']=gettext("Additional Notification Methods");

$lstr['NotificationMethodEmailTitle']=gettext("Email");
$lstr['NotificationMethodEmailDescription']=gettext("Receive alerts via email.");

$lstr['NotificationMobileTextMessageTitle']=gettext("Mobile Phone Text Message");
$lstr['NotificationMobileTextMessageDescription']=gettext("Receive text alerts to your cellphone.");

$lstr['NoAdditionalNotificationMethodsInstalledNote']=gettext("No additional notification methods have been installed or enabled by the administrator.");


$lstr['UpgradeButton']=gettext("Finish Upgrade");

$lstr['UpgradePageTitle']=gettext("Upgrade");
$lstr['UpgradePageHeader']=gettext("Upgrade");
$lstr['UpgradePageMessage']=gettext("Your Nagios Fusion instance requires some modifications to complete the upgrade process.  Don't worry - its easy.");

$lstr['UpgradeCompletePageTitle']=gettext("Upgrade Complete");
$lstr['UpgradeCompletePageHeader']=gettext("Upgrade Complete");
$lstr['UpgradeCompletePageMessage']=gettext("Congratulations!  Your Nagios Fusion upgrade has completed successfully.");

$lstr['RecurringDowntimePageTitle']=gettext("Recurring Scheduled Downtime");
$lstr['RecurringDowntimePageHeader']=gettext("Recurring Scheduled Downtime");

$lstr['TacticalOverviewPageTitle']=gettext("Tactical Overview");
$lstr['TacticalOverviewPageHeader']=gettext("Tactical Overview");


$lstr['AutoLoginPageTitle']=gettext("Automatic Login");
$lstr['AutoLoginPageHeader']=gettext("Automatic Login");
$lstr['AutoLoginPageNotes']=gettext("These options allow you to configure a user account that should be used to automatically login visitors.  Visitors can logout of the default account and into their own if they wish.");
$lstr['AutoLoginButton']=gettext("Auto-Login");

/*
$lstr['ObjectDoesntExistPageTitle']=gettext("Non-Existent Object");
$lstr['ObjectDoesntExistPageHeader']=gettext("Non-Existent Object");
$lstr['ObjectDoesntExistPageTitle']=gettext("Non-Existent Object");
*/

// an array of friendy data source name (by template) used in performance graphs
$lstr['PerfGraphDatasourceNames']=array(

	"defaults" => array(  // defaults are used if a specific template name cannot be found
		"time" => "Time",
		"size" => "Size",
		"pl" => "Packet Loss",
		"rta" => "Round Trip Average",
		"load1" => "1 Minute Load",
		"load5" => "5 Minute Load",
		"load15" => "15 Minute Load",
		"users" => "Users",
		),
		
	// specific template names
	"check_ping" => array(
		"rta" => "Round Trip Average",
		"pl" => "Packet Loss",
		),
	"check_http" => array(
		"time" => "Response Time",
		"size" => "Page Size",
		"ds1" => "Response Time",
		"ds2" => "Page Size",
		),
	"check_dns" => array(
		"time" => "Response Time",
		),
		
	// custom template names
	"check_local_load" => array(
		"ds1" => "CPU Load",
		),
		
	);

?>
