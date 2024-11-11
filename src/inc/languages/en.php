<?php
return [
    'general' => [
        'profile' => 'Profile',
        'logout' => 'Logout',
        'login' => 'Login',
        'register' => 'Register',
        'welcome' => 'Welcome',
        'language' => 'Language',
        'save' => 'Save',
        'home' => 'Home',
        'administration' => 'Administration',
        'subscriptions' => 'Subscriptions',
        'podcast_sync' => 'Podcast Synchronization',
        'site_description' => 'Podcast synchronization server based on gPodder protocol with AntennaPod support',
        'back' => 'Back',
        'add' => 'Add',
        'delete' => 'Delete',
        'download' => 'Download',
        'update' => 'Update',
        'hello' => 'Hello',
        'duration' => 'Duration',
        'statistics' => 'Statistics',
        'username' => 'Username',
        'password' => 'Password',
        'email' => 'Email',
        'min_password_length' => 'Password (minimum 8 characters)',
        'latest_updates' => 'Latest Updates',
        'devices' => 'Devices'
    ],
    'errors' => [
        'schema_file_not_found' => 'MySQL schema file not found',
        'sql_error' => 'Error executing SQL command: %s\nThe command was: %s'
    ],
    'profile' => [
        'title' => 'User Profile',
        'email' => 'Email',
        'change_password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'language_settings' => 'Language Settings',
        'select_language' => 'Select Language',
        'settings_saved' => 'Settings saved successfully',
        'error_saving' => 'Error saving settings',
        'language_updated' => 'Language updated successfully',
        'password_changed' => 'Password changed successfully',
        'passwords_dont_match' => 'New passwords do not match',
        'min_password_length' => 'Minimum 8 characters',
        'timezone_settings' => 'Timezone Settings',
        'select_timezone' => 'Select Timezone',
        'timezone_updated' => 'Timezone updated successfully'
    ],
    'languages' => [
        'en' => 'English',
        'pt-BR' => 'Portuguese (Brazil)'
    ],
    'admin' => [
        'title' => 'Administration',
        'add_user' => 'Add New User',
        'user_list' => 'Users List',
        'username' => 'Username',
        'password' => 'Password',
        'confirm_delete' => 'Are you sure you want to delete this user?',
        'user_deleted' => 'User deleted successfully',
        'user_registered' => 'User registered successfully'
    ],
    'dashboard' => [
        'secret_user' => 'GPodder Secret User',
        'secret_user_note' => '(Use this username in GPodder Desktop, as it does not support passwords)',
        'latest_updates' => 'Latest 10 Updates',
        'registered_devices' => 'Registered Devices',
        'no_info' => 'No information available for this feed',
        'last_update' => 'Last Update',
        'update_all_metadata' => 'Update all feed metadata',
        'metadata_note' => 'Feed metadata updates are configured to be done by routines directly on the server, updates are done every hour.',
        'opml_feed' => 'OPML Feed'
    ],
    'devices' => [
        'mobile' => 'Mobile',
        'desktop' => 'Desktop',
        'unavailable' => 'Unavailable'
    ],
    'actions' => [
        'played' => 'Played',
        'downloaded' => 'Downloaded',
        'deleted' => 'Deleted',
        'unavailable' => 'Unavailable',
        'on' => 'on',
        'at' => 'at'
    ],
    'messages' => [
        'subscriptions_disabled' => 'Subscriptions are disabled.',
        'invalid_captcha' => 'Invalid captcha.',
        'login_success' => 'You are logged in, you can close this and return to the application.',
        'metadata_warning' => 'Episode titles and images may be missing due to trackers/ads used by some podcast providers.',
        'app_requesting_access' => 'An application is requesting access to your account.',
        'fill_captcha' => 'Fill in the following number:',
        'auto_url_error' => 'Cannot automatically detect application URL. Set the BASE_URL constant or environment variable.',
        'invalid_url' => 'Invalid URL:',
        'device_id_not_registered' => 'Device ID not registered',
        'invalid_username' => 'Invalid username',
        'invalid_username_password' => 'Invalid username/password',
        'no_username_password' => 'No username or password provided',
        'session_cookie_required' => 'Session cookie is required',
        'session_expired' => 'Session ID cookie expired and no authorization header was provided',
        'user_not_exists' => 'User does not exist',
        'logged_out' => 'Logged out',
        'unknown_login_action' => 'Unknown login action:',
        'invalid_gpodder_token' => 'Invalid gpodder token',
        'invalid_device_id' => 'Invalid device ID',
        'invalid_input_array' => 'Invalid input: requires an array with one line per feed',
        'not_implemented' => 'Not implemented yet',
        'invalid_array' => 'No valid array found',
        'missing_action_key' => 'Missing action key',
        'nextcloud_undefined_endpoint' => 'Undefined Nextcloud API endpoint',
        'output_format_not_implemented' => 'Output format not implemented',
        'email_already_registered' => 'Email address is already registered',
    ],
    'statistics' => [
        'registered_users' => 'Registered Users',
        'registered_devices' => 'Registered Devices',
        'top_10' => 'Top 10',
        'most_subscribed' => 'Most Subscribed',
        'most_downloaded' => 'Most Downloaded',
        'most_played' => 'Most Played'
    ],
    'footer' => [
        'managed_by' => 'Instance managed and maintained by',
        'with_love_by' => 'With ❤️ by',
        'version' => 'Version'
    ],
    'home' => [
        'intro' => 'This is a podcast synchronization server based on the gPodder "protocol".',
        'fork_note' => 'This project is a fork of',
        'github_project' => 'Project published on Github',
        'tested_apps' => 'Tested Applications'
    ]
];
