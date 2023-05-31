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
 * Handles the case where no configuration file was detected, or if it was not readable.
 * 
 * @file
 * @since 1.0.0
 */

use LightnCandy\LightnCandy;

// Find the root path relative to the base URL website. The first step cuts off the portion after 
// the '*.php' file being run, and the second step cuts off the name of the script file.
$path = $_SERVER[ 'PHP_SELF' ];
$path = mb_substr( $path, 0, mb_strpos( $path, '.php' ) );
$path = mb_substr( $path, 0, mb_strrpos( $path, '/' ) + 1 );

if ( !function_exists( 'session_name' ) ) {
    $installerStarted = false;
} else {
    if ( SCFIniGetBool( 'session.auto_start' ) ) {
        session_name( 'sciclope_installer' );
    }

    $res = session_start();
    $installerStarted = $res && isset( $_SESSION[ 'installData' ] );
}

$template = file_get_contents( __DIR__ . '/templates/NoConfig.mustache' );
$code = LightnCandy::compile( $template );
$renderer = eval( $code );
echo $renderer( [ 
    'path' => $path,
    'version' => SC_VERSION,
    'configExists' => file_exists( SC_CONFIG_FILE ),
    'installerStarted' => $installerStarted,
] );
