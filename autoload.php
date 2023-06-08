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
 * Responsible for autoloading classes. Not an entrypoint in its own right, but provided at top-
 * level for interface access.
 * 
 * @file
 * @since 1.0.0
 */

require_once __DIR__ . '/includes/ClassLoader.php';

// Create and register the class loader.
ClassLoader::getDefault()->register();

// Composer AutoLoader.
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    die( strtr( __DIR__, '\\', '/' )  . '/vendor/autoload.php exits but is not readable.' );
}
