<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    $aConfig = array(
        /**
         * Main Section.
         */
        'title' => 'Shoutbox',
        'version' => '1.3.0',
        'vendor' => 'Cheetah',
        'update_url' => '',

        'compatible_with' => array(
            '1.3.0'
        ),

        /**
         * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
         */
        'home_dir' => 'cheetah/shoutbox/',
        'home_uri' => 'shoutbox',

        'db_prefix' => 'ch_shoutbox_',
        'class_prefix' => 'ChShoutBox',
        /**
         * Installation/Uninstallation Section.
         */
        'install' => array(
            'check_dependencies' => 0,
            'show_introduction' => 1,
            'change_permissions' => 0,
            'execute_sql' => 1,
            'update_languages' => 1,
            'recompile_main_menu' => 0,
            'recompile_member_menu' => 0,
            'recompile_site_stats' => 0,
            'recompile_page_builder' => 1,
            'recompile_profile_fields' => 0,
            'recompile_comments' => 0,
            'recompile_member_actions' => 0,
            'recompile_tags' => 0,
            'recompile_votes' => 0,
            'recompile_categories' => 0,
            'recompile_search' => 0,
            'recompile_injections' => 0,
            'recompile_permalinks' => 0,
            'recompile_alerts' => 1,
            'recompile_global_paramaters' => 1,
            'clear_db_cache'  => 1,
            'show_conclusion' => 1,
        ),
        'uninstall' => array (
            'check_dependencies' => 0,
            'show_introduction' => 0,
            'change_permissions' => 0,
            'execute_sql' => 1,
            'update_languages' => 1,
            'recompile_main_menu' => 0,
            'recompile_member_menu' => 0,
            'recompile_site_stats' => 0,
            'recompile_page_builder' => 1,
            'recompile_profile_fields' => 0,
            'recompile_comments' => 0,
            'recompile_member_actions' => 0,
            'recompile_tags' => 0,
            'recompile_votes' => 0,
            'recompile_categories' => 0,
            'recompile_search' => 0,
            'recompile_injections' => 0,
            'recompile_permalinks' => 0,
            'recompile_alerts' => 1,
            'recompile_global_paramaters' => 1,
            'clear_db_cache'  => 1,
            'show_conclusion' => 1,
        ),
        /**
         * Dependencies Section
         */
        'dependencies' => array(
        ),
        /**
         * Category for language keys.
         */
        'language_category' => 'ShoutBox',
        /**
         * Permissions Section
         */
        'install_permissions' => array(),
        'uninstall_permissions' => array(),
        /**
         * Introduction and Conclusion Section.
         */
        'install_info' => array(
            'introduction' => 'inst_intro.html',
            'conclusion' => 'inst_concl.html'
        ),
        'uninstall_info' => array(
            'introduction' => '',
            'conclusion' => 'uninst_concl.html'
        )
    );
