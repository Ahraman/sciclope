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
 * Contains the ```WebRequest``` class, which encapsulates information regarding a web request.
 * 
 * @file
 * @since 1.0.0
 */
namespace SciClope\Request;

/**
 * The ```WebRequest``` class contains all of the data that is part of a web request. This data is 
 * supplied by various means, mainly by the ```GET``` and ```POST``` HTTP command parameters, which 
 * are respectively retrieved using the ```$_GET``` and ```$_SET``` pre-defined variables.
 * 
 * @since 1.0.0
 */
class WebRequest {
    /**
     * The timestamp of when the request began, precise within microsecond.
     * 
     * @var float
     * 
     * @since 1.0.0
     */
    private $requestTime;

    /**
     * The request parameters, as initialized from ```$_GET``` and ```$_POST``` variables.
     *
     * @var array
     * 
     * @since 1.0.0
     */
    private $params;

    /**
     * The query parameters, specifically the parameters from ```$_GET``` alone.
     *
     * @var string[]
     * 
     * @since 1.0.0
     */
    private $queryParams;

    /**
     * Construct a new ```WebRequest``` instance.
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->requestTime = $_SERVER[ 'REQUEST_TIME_FLOAT' ];
        $this->params = $_GET + $_POST;
        $this->queryParams = $_GET;
    }

    /**
     * Fetches the value from the request parameter with the given name as an integer, and 
     * defaulting to a given value if the field with the given name was not found. Will always 
     * return an integer if ```$default``` is also an integer.
     * 
     * @param string $name The name of the parameter to retrieve.
     * @param int $default The default value to return if the field was not set. Usually set to 0.
     * @return int The value of the field as an integer.
     * 
     * @since 1.0.0
     */
    public function getParamInt( $name, $default = 0 ) {
        return intval( $this->getRawParam( $name, $default ) );
    }

    /**
     * Fetches the value from the request parameter with the given name as text and returns it in 
     * noramlized form.
     * 
     * @param string $name The name of the parameter to retrieve.
     * @param int $default The default value to return if the field was not set. Usually set to ''.
     * @return string The value of the field in normalized string form.
     * 
     * @since 1.0.0
     */
    public function getParamText( $name, $default = '' ) {
        $val = $this->getParam( $name, $default );
        return str_replace( "\r\n", "\n", $val );
    }

    /**
     * Fetches the value of the request parameter with the given name as string, without any more 
     * modifications. Useful for when the parameter is expected to only contain simple ASCII data.
     * 
     * This function simply calls {@see getRawValue}, with the default value changed to empty 
     * string.
     * 
     * @param string $name The name of the parameter to retrieve.
     * @param int $default The default value to return if the field was not set. Usually set to ''.
     * @return string The value of the field in normalized string form.
     * 
     * @since 1.0.0
     */
    public function getParamRawText( $name, $default = '' ) {
        return $this->getRawParam( $name, $default );
    }

    /**
     * Fetches the value of the request parameter with the given name as a string, but without any 
     * Unicode or line break normalization. This is a fast alternative for values that are known to 
     * be simple, e.g. pure ASCII text, numbers, or boolean values.
     * 
     * @param string $name The name of the parameter to retreive.
     * @param int|string|null $default The default value to return if no value was set.
     * @return string|null The raw value of the parameter as a string, or null if none was found.
     * 
     * @since 1.0.0
     */
    public function getRawParam( $name, $default = null ) {
        // PHP does not allow for dots (among a few other characters) in field names, and replaces 
        // them with underscores. We have to do the same as well here to avoid bugs.
        //
        // https://www.php.net/variables.external#language.variables.external.dot-in-names
        $name = strtr( $name, '.', '_' );
        if ( isset( $this->params[ $name ] ) && !is_array( $this->params[ $name ] ) ) {
            $val = $this->params[ $name ];
        } else {
            $val = $default;
        }

        return $val === null ? null : (string)$val;
    }

    /**
     * Fetches the value of the request parameter with the given name as a string, and partially 
     * normalizes it as well.
     * 
     * Array parameters are discarded for security reasons.
     * 
     * @param string $name The name of the parameter to retreive.
     * @param int|string|null $default The default value to return if no value was set.
     * @return string|null The raw value of the parameter as a string, or null if none was found.
     * 
     * @since 1.0.0
     */
    public function getParam( $name, $default = null ) {
        $val = $this->getGPCParam( $this->params, $name, $default );
        if ( is_array( $val ) ) {
            $val = $default;
        }

        return $val === null ? null : (string)$val;
    }

    /**
     * Fetches the value of the parameter with the given name within the provided array. Partially 
     * normalizes text values.
     *
     * @param array $arr The array containing the parameter.
     * @param string $name The name of the parameter to retrieve.
     * @param mixed $default The default value to return if the parameter was not set.
     * @return mixed The value of the array, or ```$default``` if none was found.
     * 
     * @since 1.0.0
     */
    public function getGPCParam( $arr, $name, $default ) {
        // PHP does not allow for dots (among a few other characters) in field names, and replaces 
        // them with underscores. We have to do the same as well here to avoid bugs.
        //
        // https://www.php.net/variables.external#language.variables.external.dot-in-names
        $name = strtr( $name, '.', '_' );
        if ( !isset( $arr[ $name ] ) ) {
            return $default;
        }

        $val = $arr[ $name ];

        // Optimization: Skip UTF-8 normalization for simple ASCII strings.
        if ( !is_string( $val ) || preg_match( '/[^\x20-\x7E]/', $val ) !== 0 ) {
            // Perform UTF-8 normalization.
            $val = self::normalizeUnicode( $val );
        }

        return $val;
    }

    private static function normalizeUnicode( $val ) {
        if ( is_array( $val ) ) {
            foreach ( $val as $key => $value ) {
                $val[ $key ] = self::normalizeUnicode( $value );
            }
        } else {
            // Checking for existence of the function 'normalizer_normalize' shouldn't be done 
            // every time this function is called, and ideally should be a constant. Do this 
            // whenever refactoring this code.
            //
            // TODO: Make string clean up its own class.
            if ( function_exists( 'normalizer_normalize' ) ) {
                $val = normalizer_normalize( $val, \Normalizer::FORM_C );
                // TODO: the normalizer_normalize doesn't do a lot of the useful transformations 
                // that we might want, so we have to come up with a more elaborate solution. For 
                // prototypes, this should be enough for now.
            }
        }

        return $val;
    }
}
