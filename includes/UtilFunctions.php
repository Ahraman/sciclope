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
 * Contains a number of utility functions useful in a variety of contexts.
 * 
 * @file
 * @since 1.0.0
 */

/**
 * Safe wrapper around ```ini_get()``` for boolean options.
 * 
 * Converts a number of the typically expected values like 'on' and 'yes' in addition to 
 * 'true' into the boolean value ```true``` as well.
 *
 * @param string $option The name of the option to retrieve.
 * @return bool The value of the option, converted into a boolean.
 * 
 * @since 1.0.0
 */
function SCFIniGetBool( $option ) {
    return SCFOptionStrToBool( ini_get( $option ) );
}

/**
 * Converts the string option value into boolean, with the following expression (case-insensitive) 
 * interpreted as true:
 * - 'true'
 * - 'yes'
 * - 'on'
 * - any number except 0
 * All other string values are interpreted as false.
 *
 * @param string $option The name of the option to retrieve.
 * @return bool The value of the option, converted into a boolean.
 * 
 * @since 1.0.0
 */
function SCFOptionStrToBool( $val ) {
    $val = strtolower( $val );
    // 'true', 'on', and 'yes' cannot have whitespace surrounding it, but '1' might.
    return $val === 'true'
        || $val === 'yes'
        || $val === 'on'
        || preg_match( "/^\s*[+-]?0*[1-9]/", $val );
}