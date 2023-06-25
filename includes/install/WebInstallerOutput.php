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
 * Contains the ```WebInstallerOutput``` class, representing a SciClope web installer, which uses a web browser to 
 * initialize a new SciClope instance.
 * 
 * @file
 * @since 1.0.0
 * @author Ahraman <ahraman12000@gmail.com>
 */
namespace SciClope\Install;

class WebInstallerOutput {
    /**
     * The parent ```WebInstaller``` instance.
     *
     * @var WebInstaller
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    protected $parent;

    /**
     * A buffer containing content that has not yet been flushed into the output.
     *
     * @var string
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    private $buffer = '';

    /**
     * Boolean flag marking if the header 
     *
     * @var boolean
     */
    private $headerDone = false;

    /**
     * Constructs a new ```WebInstallerOutput``` instance that is associated with the web installer 
     * instance provided.
     *
     * @param WebInstaller $parent The parent web installer.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function __construct( $parent ) {
        $this->parent = $parent;
    }

    public function flushHeader() {
        $this->headerDone = true;
    }

    public function addHtml( $html ) {
        $this->buffer .= $html;

        $this->flush();
    }

    public function flush() {
        if ( !$this->headerDone ) {
            $this->flushHeader();
        }
        if ( strlen( $this->buffer ) ) {
            echo $this->buffer;
            flush();
            $this->buffer = '';
        }
    }
}