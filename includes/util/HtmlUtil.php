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
 * Contains the ```HtmlUtil``` class, providing a number of utility functionalities for HTML.
 * 
 * @file
 * @since 1.0.0
 * @author Ahraman <ahraman12000@gmail.com>
 */
namespace SciClope\Util;

class HtmlUtil {
    /**
     * Returns the closing ta for the given HTML element. Essentially, this is ```"</$elem>"```.
     *
     * @param string $elem The name of the HTML element.
     * @return string The expression ```"</$elem>"```, but with ```$elem``` turned lowercase.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public static function closeElement( $elem ) {
        $elem = strtolower( $elem );
        return "</$elem>";
    }
}
