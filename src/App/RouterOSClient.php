<?php

namespace App;

use PEAR2\Net\RouterOS;

class RouterOSClient extends RouterOS\Client
{
    /**
     * @param RouterOS\Request $request
     * @return array
     */
    public function fetch( RouterOS\Request $request )
    {
        return $this->transformResponses( $this->sendSync( $request ) , RouterOS\Response::TYPE_DATA );
    }

    /**
     * @param RouterOS\ResponseCollection $responses
     * @param string                      $type
     * @return array
     */
    protected function transformResponses( RouterOS\ResponseCollection $responses , $type = RouterOS\Response::TYPE_DATA )
    {
        $ret    =   array();

        foreach( $responses as $key => $response )
        {
            if( $response->getType() !== $type )
            {
                continue;
            }

            $args    =  $response->getAllArguments();
            foreach( $args as $argKey => &$argVal )
            {
                if( 'false' === $argVal )
                {
                    $argVal =   false;
                }
                else if( 'true' === $argVal )
                {
                    $argVal =   true;
                }
            }
            unset($argKey,$argVal);

            $ret[] =   $args;
        }

        return $ret;
    }
}