<?php

namespace Designnbuy\Base\Service;

class Impose
{
    /**
     * exec params.
     *
     * @var array
     */
    protected $params;

    private $_files;


    /**
     * Last execute result string.
     * 
     * @var string
     */
    protected $lastExecuteResult;
    protected $lastCmd;
	protected $_width;
    protected $_height;

    public function __construct( )
    {

    }

    /**
     * Add a PDF for inclusion in the merge with a valid file path. Pages should be formatted: 1,3,6, 12-16.
     * @param $filepath
     * @param $pages
     * @return void
     */
    public function addPDF($filepath, $pages = '-', $orientation = null)
    {
        if (file_exists($filepath)) {
            /*if (strtolower($pages) != '-') {
                $pages = $this->_rewritepages($pages);
            }*/
            $this->_files[] = array($filepath, $pages);
        } else {
            throw new Exception("Could not locate PDF on '$filepath'");
        }
        return $this;
    }

    /**
     * Takes our provided pages in the form of 1,3,4,16-50 and creates an array of all pages
     * @param $pages
     * @return unknown_type
     */
    private function _rewritepages($pages)
    {
        $pages = str_replace(' ', '', $pages);
        $part = explode(',', $pages);
        //parse hyphens
        foreach ($part as $i) {
            $ind = explode('-', $i);
            if (count($ind) == 2) {
                $x = $ind[0]; //start page
                $y = $ind[1]; //end page
                if ($x > $y) {
                    throw new Exception("Starting page, '$x' is greater than ending page '$y'.");
                    return false;
                }
                //add middle pages
                while ($x <= $y) {
                    $newpages[] = (int) $x;
                    $x++;
                }
            } else {
                $newpages[] = (int) $ind[0];
            }
        }
        return $newpages;
    }

    public function addParam( $param, $value = NULL )
    {
        if ( $param )
        {
            $this->params[$param] = $value;
        }
    }

    public function setParam( $param, $value = NULL )
    {
        $this->clearParams();
        $this->params[$param] = $value;
    }

    public function getParam( $param )
    {
        return $this->params[$param];
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams( $params )
    {
        return $this->params = $params;
    }

    public function clearParams()
    {
        $this->params = null;
    }



    /**
     * Execute the pdfjam.
     *
     * @param string some extra content for exec command
     *
     * @return string
     */
    public function merge( $extraParam = null )
    {
        $exec = 'pdfjam';

        if ( is_array( $this->_files ) )
        {
            foreach ($this->_files as $_files)
            {

                foreach ($_files  as $_file => $value)
                {
                    if ( $value )
                    {
                        $exec .= ' ' . $value;
                    }
                }
            }
        }

        if ( is_array( $this->params ) )
        {
            foreach ($this->params as $param => $value)
            {
                $exec .= ' --' . $param;

                if ( $value )
                {
                    $exec .= ' ' . $value;
                }
            }
        }




        $this->lastCmd = $exec . ' ' . $extraParam;
        

        $this->lastExecuteResult = shell_exec( $this->lastCmd );

        if ( trim( $this->lastExecuteResult ) == 'Nothing to do!' )
        {
            throw new \Exception( 'Nothing to do!' );
        }

        return $this->lastExecuteResult;
    }

    public function export( $filename )
    {

        if ( !$filename )
        {
            throw new \Exception( 'Need to inform a file path to save.' );
        }

        $availableFormat = array( 'png', 'ps', 'eps', 'pdf', 'plain-svg' );

        $this->addParam( 'outfile', $filename );
        $this->merge();

        if ( file_exists( $filename ) )
        {
            return true;
        }
        else
        {
            $msg = '';

            // if ( !defined( 'INKSCAPE_PATH' ) )
            // {
            // $msg = ' Define INKSCAPE_PATH is not defined. Try to define it.';
            // }

            throw new \Exception( 'Impossible to save file in path : ' . $filename . PHP_EOL . '  Inkcape cmd: ' . $this->lastCmd . PHP_EOL . 'Inkscape Error Message = ' . $this->lastExecuteResult . $msg );
        }
    }
}
?>