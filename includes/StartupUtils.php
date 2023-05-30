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
 * Provides a number of utility functions useful during startup.
 * 
 * @file
 * @since 1.0.0
 */

/**
 * Detects and returns the path of the configuration file for the installation, and stores the 
 * result in the SC_CONFIG_FILE constant.
 * 
 * The configuration file is traditionally the file ```config.php``` at the root of the 
 * installation path. However, the default value can be altered via the ```SCICLOPE_CONFIG``` 
 * environment variable.
 *
 * @param string|null $installPath The installation path; defaults to the value retrieved by 
 * ```SCFDetectInstallPath()``` if it is set to ```null```.
 * @return string The path of the configuration file, or the ```SC_CONFIG_FILE``` 
 * 
 * @internal Only for use during Startup and Installer. Otherwise, use the ```SC_CONFIG_FILE``` 
 * constant directly.
 * @since 1.0.0
 */
function SCFDetectConfigFile( $installPath = null ) {
    if ( !defined( 'SC_CONFIG_FILE' ) ) {
        if ( $installPath === null ) {
            $installPath = SCFDetectInstallPath();
        }

        // Lookup config file path.
        $file = getenv( 'SCICLOPE_CONFIG' ) ?: 'config.php';

        // If the value retrieved from SCICLOPE_CONFIG environment variable is an absolute path, 
        // then  no modification is necessary. However, if it is not, or if the default value of 
        // 'config.php'  was used, then prepend the $installPath to the relative file path.
        // A file path is absolute if it starts with a slash (for Linux systems), or if it contains 
        // a colon (for Windows systems).
        if ( ( mb_strpos( $file, '/' ) === 0 ) || ( mb_strpos( $file, ':' ) === 1 ) ) {
            $file = "$installPath/$file";
        }

        define( 'SC_CONFIG_FILE', $file );
    }

    return SC_CONFIG_FILE;
}

/**
 * Detect and return the installation path for SciClope, and store the result in the 
 * ```SC_INSTALL_PATH``` constant.
 * 
 * The function uses the location of the current file (```StartupUtils.php```) to find the root of 
 * the program, but this can be overwritten by using the ```SCICLOPE_PATH``` environment variable.
 * 
 * @return string The value of the ```SC_INSTALL_PATH``` after it has been set according to the 
 * rules above.
 * 
 * @internal Only for use during Startup and Installer. Otherwise, use the ```SC_INSTALL_PATH``` 
 * constant or the ```$SCGBaseDirectory``` variable.
 * @since 1.0.0
 */
function SCFDetectInstallPath() {
    if ( !defined( 'SC_INSTALL_PATH' ) ) {
        $path = getenv( 'SCICLOPE_PATH' );
        if ( $path === false ) {
            $path = dirname( __DIR__ );
        }

        define( 'SC_INSTALL_PATH', $path );
    }

    return SC_INSTALL_PATH;
}
