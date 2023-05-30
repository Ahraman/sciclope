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
 * The startup process common to all SciClope entry points.
 *
 * The entry point (such as ```WebStart.php```) must do the following:
 * - define the ```SCICLOPE``` constant set to ```true```.
 * 
 * This file does the following:
 * - detect the installation path and assign it to the ```SC_INSTALL_PATH``` constant and the 
 * ```$SCBaseDirectory``` global variable;
 * - Setup and register the autoloader.
 * 
 * @file
 * @since 1.0.0
 */

// Checks to see if the SCICLOPE constant is defined, and that it is set to true. All valid 
// entry points must have this value defined and set to true.
if ( !defined( 'SCICLOPE' ) || !SCICLOPE ) {
    exit( 1 );
}

// The SC_ENTRY_POINT determines from which entry point the program has started. In most 
// instances it is just the script filename without the .php file extension. This value must be 
// defined, so it is given a default here if the constant was not defined up to this point.
if ( !defined( 'SC_ENTRY_POINT' ) ) {
    // Currently, the only possible values for SC_ENTRY_POINT are 'index' or 'unknown'.
    define( 'SC_ENTRY_POINT', 'unknown' );
}

// Set the character encoded internally used by PHP for reading HTTP input and writing output. This 
// is also the default encoding used by mbstring functions.
mb_internal_encoding( 'UTF-8' );
