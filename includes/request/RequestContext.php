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
 * Contains the ```RequestContext``` class, which encapsulates the contextual information for a 
 * request.
 * 
 * @file
 * @since 1.0.0
 */
namespace SciClope\Request;

/**
 * The ```RequestContext``` is a class that contains a single request, as well as certain 
 * peripheral objects.
 * 
 * @since 1.0.0
 */
class RequestContext {
    /**
     * The request that this ```RequestContext``` instance is responsible for.
     *
     * @var WebRequest
     * 
     * @since 1.0.0
     */
    private $request;

    /**
     * The main ```RequestContext``` instance used by SciClope. Almost all calls through this class 
     * interact with this instance alone.
     *
     * @var RequestContext
     * 
     * @since 1.0.0
     */
    private static $mainContext;

    /**
     * Returns the ```RequestContext``` instance associated with the main request.
     *
     * @return RequestContext Returns the main instance.
     * 
     * @since 1.0.0
     */
    public static function getMain() {
        if ( self::$mainContext === null ) {
            self::$mainContext = new RequestContext;
        }

        return self::$mainContext;
    }

    /**
     * Returns the request object for this instance; also initializes the request if it was not already.
     *
     * @return WebRequest Returns the request object.
     * 
     * @since 1.0.0
     */
    public function getRequest() {
        if ( $this->request === null ) {
            $this->request = new WebRequest;
        }

        return $this->request;
    }
}
