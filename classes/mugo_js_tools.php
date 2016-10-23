<?php
class MugoJSTools
{
    function __construct()
    {
        $this->Operators = array( 'mjs_console_log' );
    }

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'mjs_console_log' => array(
                'logContent' => array(
                    'required' => false,
                    'default' => ''
                )
            )
        );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace,$currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ($operatorName) {
            case "mjs_console_log":
                if ( empty( $namedParameters['logContent'] ) )
                {
                    $operatorValue = $this->mjs_console_log($operatorValue);
                } else {
                    $operatorValue = $this->mjs_console_log($namedParameters['logContent']);
                }
                break;
        }
    }

    function mjs_console_log( $logContent)
    {
        $jsonLogContent = json_encode($logContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK, 512);
        $nodeEncoded = "";
        if (is_object($logContent)) {
            if (isset($logContent->NodeID)) {
                $nodeEncoded    = ", \"node{$logContent->NodeID}\":" . ezjscAjaxContent::nodeEncode($logContent, array(
                        'dataMap'               => array('all'),
                        'fetchPath'             => true,
                        'fetchChildrenCount'    => true,
                        'dataMapType'           => array('all'),
                        'ImagePreGenerateSizes' => array('all')
                    ));
            }
            $scriptTag = <<<EOS
               <!--- mjs_console_log output start -->
               <script>
                   console.log({
                       "object{$logContent->ContentObjectID}": {$jsonLogContent} {$nodeEncoded}
                   });
               </script>
               <!--- mjs_console_log output end --> 
EOS;
        } else {
            $scriptTag = <<<EOS
               <!--- mjs_console_log output start -->
               <script>
                   console.log({$jsonLogContent});
               </script>
               <!--- mjs_console_log output end --> 
EOS;
        }
        return $scriptTag;
    }
    private $Operators;
}
?>
