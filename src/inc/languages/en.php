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
        'statistics' => 'Statistics'
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
        'min_password_length' => 'Minimum 8 characters'
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
        'metadata_warning' => 'Episode titles and images may be missing due to trackers/ads used by some podcast providers.'
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
    ]
];
