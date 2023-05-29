<?php // For broken web servers: ><pre>

// ================================================================================================
// ATTENTION!!! IF YOU'RE SEEING THIS IN YOUR WEB BROSER, THEN READ ON. The server is probably not 
// configured to run PHP applications properly!
//
// If you're a user, then contact the server administrators to let them know that the website is 
// down. If you are the administrator in charge, then you have to check to see what is out of 
// order... or, just crawl in a corner and cry...
// ================================================================================================

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
 * This file is the main entry point for web browser navigations.
 * 
 * @file
 * @since 1.0.0
 */

// The constant SC_ENTRY_POINT determines the entry point used to run SciClope. Here, it is set to 
// the value 'index'. 
define( 'SC_ENTRY_POINT', 'index' );

// Call WebStart.php to set up the environment.
require_once __DIR__ . '/includes/WebStart.php';

// Run the main function.
SCFIndexMain();

/**
 * The main function for 'index.php'.
 *
 * @return void
 * @since 1.0.0
 */
function SCFIndexMain() {
    echo 'Hello world!';
}