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
 * Contains the ClassLoader, which is responsible for autoloading classes.
 * 
 * @file
 * @since 1.0.0
 */

/**
 * The ClassLoader is responsible for loading, both automatically and manually, the classes used by 
 * SciClope.
 * 
 * The ClassLoader is designed with a rather versatile interface, and allows for a number of 
 * functionalities, as well as for multiple instances to coexist together.
 * 
 * @since 1.0.0
 */
class ClassLoader {
    /**
     * The root directory to look for class files.
     * 
     * @var string
     * 
     * @since 1.0.0
     */
    private $rootDir;

    /**
     * The root namespace, which corresponds to the root directory and is stripped off of class 
     * names before file lookup.
     * 
     * @var string
     * 
     * @since 1.0.0
     */
    private $rootNamespace;

    /**
     * The default ```ClassLoader``` instance that SciClope uses for its purposes.
     * 
     * @var ClassLoader
     * 
     * @since 1.0.0
     */
    private static $defaultLoader;

    /**
     * Returns the default ```ClassLoader``` instance used by SciClope; this function creates the 
     * instance on demand if it was not already initialized.
     *
     * @return ClassLoader The default class loader instance.
     * 
     * @since 1.0.0
     */
    public static function getDefault() {
        if ( self::$defaultLoader === null ) {
            self::$defaultLoader = new ClassLoader( __DIR__, 'SciClope' );
        }

        return self::$defaultLoader;
    }

    /**
     * Constructs a new ```ClassLoader``` instance.
     *
     * @param string $rootDir The root directory for classes this loader is responsible for. This 
     * value is prepended to file paths before loading.
     * @param string|null $rootNamespace The root namespace. This part of the class name is culled 
     * before lookup. Effectively, it means that ```$rootNamespace``` refers to ```$rootDir``` in 
     * file system.
     * 
     * @since 1.0.0
     */
    public function __construct( $rootDir, $rootNamespace = null 
    ) {
        $this->rootDir = self::cleanupDirName($rootDir);
        $this->rootNamespace = self::cleanupNamespaceName( $rootNamespace );
    }

    /**
     * Registers the ```ClassLoader``` instance through ```spl_autoload_register()``` function.
     *
     * @param boolean $prepend Whether to push the ClassLoader at the front or at the back of the 
     * autoload queue. Defaults to ```false```.
     * @return ClassLoader The ```ClassLoader``` instance used to call this function from.
     * 
     * @since 1.0.0
     */
    public function register( $prepend = false ) {
        spl_autoload_register( [ $this, 'load' ], true, $prepend );
        return $this;
    }

    /**
     * Removes the calling ```ClassLoader``` instance from the autoload queue via the 
     * ```spl_autoload_unregister()``` function.
     *
     * @return ClassLoader The ```ClassLoader``` instance used to call this function from.
     * 
     * @since 1.0.0
     */
    public function unregister() {
        spl_autoload_unregister( [ $this, 'load' ] );
        return $this;
    }

    /**
     * Loads the file associated with a class with the given name.
     *
     * @param string|null $className The name of the class to look for.
     * @return void
     * 
     * @since 1.0.0
     */
    public function load( $className ) {
        $path = $this->find( $className );
        if ( $path !== null ) {
            require $path;
        }
    }

    /**
     * Finds the file associated with a class with the given name, and returns the path of that 
     * file.
     * 
     * Currently, the only mechanism supported for the ```ClassLoader``` to recognize a file 
     * associated with the given class name is to convert the class name into a file path relative 
     * to the provided root directory. However, other mechanisms might be useful or even necessary 
     * down the line, so this function is likely going to get a rewrite later whenever this 
     * functionality is needed.
     *
     * @param string|null $className The name of the class to look for the file of.
     * @return string|null The path of the file if it was found; returns ```null``` otherwise, or 
     * if the class name was invalid.
     * 
     * @since 1.0.0
     */
    public function find( $className ) {
        // Obligatory null-check
        if ( $className === null ) {
            return null;
        }

        // Check for the class needing namespace lookup.
        if ( ( $pos = mb_strrpos( $className, '\\' ) ) !== false ) {
            # A little bit of magic code in the next line of code.
            #
            # Essentially, we want to eliminate the starting backslash from the namespace if there 
            # is one, but we already have access to the first occurrence of a backslash through 
            # $pos. So, what we do is to just compare it to 0 (the first position) and convert the 
            # resulting bool back into int through +.
            $prefix = mb_substr( $className, +( $pos === 0 ), $pos );
            if ( mb_strpos( $prefix, $this->rootNamespace ) === 0 ) {
                # File is inside the root namespace of this class loader so we can proceed to 
                # construct the file path.

                # Remove the root namespace prefix from the class name.
                $prefix = mb_substr( $prefix, mb_strlen( $this->rootNamespace ) );
                # Make the namespace portion of class name lower case for file lookup.
                $prefix = mb_strtolower( $prefix );
                # Replace all backslashes with forward slash, and append a slash if class is in a 
                # sub-namespace. This serves as a separator between $prefix and $file in the next 
                # part, which isn't needed when the file is directly in $this->rootDir.
                $prefix = strtr( $prefix, '\\', '/' );
                if ( $prefix ) {
                    $prefix = $prefix . '/';
                }

                # Make the filepath.
                $file = mb_substr( $className, $pos + 1 );
                $prefix = $this->rootDir . $prefix;
                $path = $prefix . $file . '.php';

                # Check if the file is valid, and return it if it is.
                if ( is_file( $path ) ) {
                    return $path;
                }
            }
        }

        # Catch-all return value.
        return null;
    }

    /**
     * Cleans up the directory path for use. This function is called for root directories and only  
     * during construction.
     * 
     * This function applies the following transformations to ```$path```:
     * - Replace all backslashes with forward slashes.
     * - Append a slash at the end if there is none.
     * 
     * @param string $path The input path of the directory.
     * @return string The input ```$path``` with the transformations described applied.
     * 
     * @since 1.0.0
     */
    private static function cleanupDirName( $path ) {
        $path = strtr( $path, '\\', '/' );
        
        if ( mb_substr( $path, -1 ) !== '/' ) {
            $path = $path . '/';
        }

        return $path;
    }

    /**
     * Cleans up the namespace name for use. This function is called for root namespaces and only 
     * during construction.
     * 
     * This function applies the following transformations to ```$name```:
     * - Convert a null value to empty string.
     * - Remove prefix or suffix backslashes.
     * 
     * @param string|null $path The input path of the directory.
     * @return string The input ```$path``` with the transformations described applied.
     * 
     * @since 1.0.0
     */   
    private static function cleanupNamespaceName( $name ) {
        if ( $name === null ) {
            return '';
        }
        
        if ( mb_substr( $name, 0, 1 ) === '\\' ) {
            $name = mb_substr( $name, 1 );
        }
        
        if ( mb_substr( $name, -1 ) === '\\' ) {
            $name = mb_substr( $name, 0, -1 );
        }

        return $name;
    }
}
