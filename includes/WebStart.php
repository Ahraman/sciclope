<?php
/*
 * Copyright (C) 2023, SciClope Development Team
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the 
 * GNU General Public License as published by the Free Software Foundation; either version 2 of the 
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See 
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if 
 * not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 
 * 02110-1301, USA.
 * 
 * Link: http://www.gnu.org/copyleft/gpl.html
 */
/**
 * Common setup for all web requests.
 * 
 * @file
 * @since 1.0.0
 */

// Turn off content sniffing because it is a security vulnerability.
header( 'X-Content-Type-Options: nosniff' );

// The constant SCICLOPE defined to true marks this file as a valid entry point for Setup.php.
define( 'SCICLOPE', true );

// Load the startup helper functions.
require_once __DIR__ . '/StartupUtils.php';

// The constant SC_CONFIG_CALLBACK contains the name of a callback function for handling the 
// configuration file.
if ( !defined( 'SC_CONFIG_CALLBACK' ) ) {
    SCFDetectConfigFile();
    if ( !is_readable( SC_CONFIG_FILE ) ) {
        define( 'SC_CONFIG_CALLBACK', 'SCFConfigCallbackNoConfig' );
    } else {
        define( 'SC_CONFIG_CALLBACK', 'SCFConfigCallbackDefault' );
    }
}

// Run Startup.php which does the bulk of the startup process.
require_once __DIR__ . '/Startup.php';

/**
 * Default callback function for handling configuration.
 * 
 * Currently does nothing.
 *
 * @return void
 */
function SCFConfigCallbackDefault() {
}

/**
 * Callback for when no configuration file was found by the system.
 * 
 * The function passes control to the StartupNoConfig.php, which displays a page directing the user 
 * to either install SciClope, or fix the issues regarding the current configuration file.
 *
 * @return void
 * 
 * @since 1.0.0
 */
function SCFConfigCallbackNoConfig() {
    require_once __DIR__ . '/StartupNoConfig.php';
    exit();
}