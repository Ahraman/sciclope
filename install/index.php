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
 * The index file for the installer.
 * 
 * This file may conflict with query for a hypothetical page named 'install', so a better strategy 
 * involving specific GET requests might be useful.
 * 
 * @file
 * @since 1.0.0
 */
use SciClope\Install\WebInstaller;
use SciClope\Request\RequestContext;

// The constant SCICLOPE_INSTALLER defined to true means that currently, the installer is running.
define( 'SCICLOPE_INSTALLER' , true );

// The constant SC_CONFIG_CALLBACK contains the name of a callback function for handling the 
// configuration file. Here, we set it to SCFConfigCallbackInstall before calling WebStart.php for 
// the installer to take control over the configuration.
define( 'SC_CONFIG_CALLBACK', 'SCFConfigCallbackInstall' );

chdir( dirname( __DIR__ ) ); // Set the working directory to the root path.
require dirname( __DIR__ ) . '/includes/WebStart.php';

SCFInstallerMain();

/**
 * Main entrypoint function for the installer.
 * 
 * Initializes and begins the installation procedure.
 *
 * @return void
 */
function SCFInstallerMain() {
    $request = RequestContext::getMain()->getRequest();
    $installer = new WebInstaller( $request );

    // Try and start the installation session.
    if ( !$installer->startSession() ) {
        exit();
    }

    // Get the unique fingerprint for this installer. Makes different runs of SciClope installers 
    // not interfere with one another if ran from different file locations or URLs.
    $fingerprint = $installer->getFingerprint();
    if ( isset( $_SESSION[ 'installation' ][ $fingerprint ] ) ) {
        // Load our data from the current session.
        $session = $_SESSION[ 'installation' ][ $fingerprint ];
    } else {
        // Installer just started or we lost the data, reseting session data.
        $session = array();
    }

    // Run the installer.
    $session = $installer->run( $session );
    // Save the installation data to the session.
    $_SESSION[ 'installation' ][ $fingerprint ] = $session;
}

/**
 * Callback function for handling configuration when installer is called.
 * 
 * It prevents a loop from forming when including WebStart.php here.
 *
 * @return void
 * 
 * @since 1.0.0
 */
function SCFConfigCallbackInstall() {
}
