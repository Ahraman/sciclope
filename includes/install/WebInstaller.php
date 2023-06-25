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
 * The ```WebInstaller``` class represents a SciClope web installer, which uses a web browser to 
 * initialize a new SciClope instance.
 * 
 * @file
 * @since 1.0.0
 * @author Ahraman <ahraman12000@gmail.com>
 */
namespace SciClope\Install;

use SciClope\Request\WebRequest;

/**
 * The ```WebInstaller``` class represents a SciClope web installer.
 * @since 1.0.0
 * @author Ahraman <ahraman12000@gmail.com>
 */
class WebInstaller extends Installer {
    /**
     * The list of pages in the main installer page sequence.
     *
     * @var string[]
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    private static $MAIN_PAGES = [
        'Welcome',
    ];

    /**
     * The request passed to the installer page.
     *
     * @var WebRequest
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    protected $request;

    /**
     * The output for the web installer. Handles what is displayed to the user.
     * 
     * @var WebInstallerOutput
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public $output;

    /**
     * List of pages that have been filled.
     *
     * @var array
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    private $filledPages;

    /**
     * An array of possible errors thrown by PHP at various junctures. The value is transient.
     * 
     * @var string[]
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    protected $errors;

    /**
     * Constructs a new ```WebInstaller``` instance.
     *
     * @param WebRequest $request The web request for the installer.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function __construct( $request ) {
        $this->request = $request;
        $this->output = new WebInstallerOutput( $this );
    }

    /**
     * ُStarts the PHP session for the installer.
     *
     * @return bool Returns true if session successfully started, false otherwise.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function startSession() {
        if ( SCFIniGetBool( 'session.auto_start' ) || session_id() ) {
            // Session already started, we're done.
            return true;
        }

        $options = [];
        
        // Enable secure cookies if using HTTPS.
        if ( $this->request->getProtocol() === 'https' ) {
            $options[ 'cookie_secure' ] = '1';
        }

        $this->errors = [];
        set_error_handler( [ $this, 'sessionError' ] );
        try {
            session_name( 'sciclope_installer' );
            session_start( $options );
        } catch ( \Exception $e ) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();
        if ( $this->errors ) {
            return false;
        }

        return true;
    }

    /**
     * Runs the web installer, with the session data passed in as parameter.
     *
     * @param array $session The session data for the installation.
     * @return array Returns the session data with updated parameters.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function run( $session ) {
        $this->filledPages = $session[ 'filledPages' ] ?? [];
        $firstRemainingPage = $this->getFirstRemainingPage();
        
        // Find the page name
        $pageName = $this->request->getParam( 'p', '' );
        if ( !$pageName || !in_array( $pageName, self::$MAIN_PAGES ) ) {
            // Requested page is not among the main pages.
            $pageIndex = $firstRemainingPage;
        } else {
            // Locate the page in the main page sequence.
            $pageIndex = array_search( $pageName, self::$MAIN_PAGES );

            if ( $pageIndex > $firstRemainingPage ) {
                $pageIndex = $firstRemainingPage;
            }
        }

        // Get the name again, in case the index changed.
        $pageName = self::$MAIN_PAGES[ $pageIndex ];
        $page = $this->getPage( $pageName );

        $result = $page->emit();

        if ( $result === 'continue' ) {

        }

        return $session;
    }

    private function getFirstRemainingPage() {
        if ( count( $this->filledPages ) == 0 ) {
            // No page has been filled, return the index of the first page, which is 0.
            return 0;
        } else {
            // Return the index of the page after the last filled page.
            return max( array_keys( $this->filledPages ) ) + 1;
        }
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $pageName
     * @return WebInstallerPage
     */
    private function getPage( $pageName ) {
        $pageClass = 'WebInstaller' . $pageName . 'Page';
        $qualifiedClass = __NAMESPACE__ . '\\' . $pageClass; 
        return new $qualifiedClass( $this );
    }

    /**
     * Returns a 'fingerprint' for the installer - i.e. a unique identifier which can be used to 
     * distinguish separate instances of SciClope installers across the web and in the same domain.
     * 
     * Well, ideally it would do that. Currently, that would require handling URLs well, which we 
     * don't have the framework for just yet. Instead, currently it only identifies different 
     * installers within the same domain.
     *
     * @return string The fingerprint of the installer, as a hashed string of the install path and 
     * of the running version of the installer.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function getFingerprint() {
        // Ideally, the URL should be part of the fingerprint, but getting the URL for the request 
        // seemed too complicated a task for now, so I've deferred it to some time later when the 
        // framework is more robust.
        //
        // TODO: include the request URL as part of the installation fingerprint.
        return md5( serialize( [ 
            'path' => dirname( __DIR__ ),
            'version' => SC_VERSION
        ] ) );
    }

    /**
     * The error handler used for session startup.
     * 
     * Simply collects the errors generated by the caller in a field.
     *
     * @param int $errno Unused. The level of the error raised.
     * @param string $errstr The error message.
     * @param string $errfile Unused. The filename in which the error was raised.
     * @param int $errline Unused. The line number where the error was raised.
     * @return bool If the functions returns false, then ordinary error handler continues; 
     * otherwise this functions takes control over all PHP error handling. Since we don't want that 
     * here, we must return false in all cases.
     * 
     * @since 1.0.0
     * @author Ahraman <ahraman12000@gmail.com>
     */
    public function sessionError( $errno, $errstr, $errfile, $errline ) {
        $this->errors[] = $errstr;
        return false;
    }
}
