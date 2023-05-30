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

// Run Startup.php which does the bulk of the startup process.
require_once __DIR__ . '/Startup.php';
