<?php
/**
 * This extension provides JavaScript template operators for eZ Publish template files.
 * See https://github.com/moismailzai/mugojstools
 */
class MugoJSTools
{
    /**
     * The array of operators, used for registering operators.
     * @var array
     */
    private $Operators;

    /**
     * mjs_console_log: This operator generates a JavaScript console.log() dump in the browser console. Supports pipes
     *                  and can take any variable or literal input, including strings, objects, and ez nodes.
     */
    function __construct()
    {
        $this->Operators = array( 'mjs_console_log' );
    }

    /**
     * Returns the template operators
     */
    function operatorList()
    {
        return $this->Operators;
    }

    /**
     * Returns true to tell the template engine that the parameter list exists per operator type.
     * @return boolean
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * Returns an array of named parameters, this allows for easier retrieval of operator parameters. This also requires
     * the function modify() has an extra parameter called $namedParameters. The position of each element (starts at 0)
     * represents the position of the original sequenced parameters. The key of the element is used as parameter name,
     * while the contents define the type and requirements.
     *
     * The keys of each element content is:
     *      type:       defines the type of parameter allowed
     *      required:   boolean which says if the parameter is required or not, if missing and required an error is
     *                  displayed
     *      default:    the default value if the parameter is missing
    */
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

    /**
     * Modifies the input variable $operatorValue and sets the output result in the same variable.
     *
     * @param   object    $tpl	                The template object which called this class
     * @param   string    $operatorName	        The name of this operator
     * @param   array     $operatorParameters	The parameters for this operator
     * @param   string    $rootNamespace	    The namespace which this operator works in
     * @param   string    $currentNamespace	    The current namespace for functions, this is usually used in functions
     *                                          for setting new variables
     * @param   mixed     $operatorValue	    The input/output value
     * @param   object    $namedParameters	    The parameters as named lookups, only required if namedParameterList()
     *                                          is defined. The values of each parameter is also fetched for you
     * @return  mixed     $operatorValue
     */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
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

    /**
     * Returns a <script> tag which prints the contents of $logContent to the browser console using console.log().
     *
     * When $logContent is an EZ node, the following template is used:
     *           {
     *               objectID#: json_encode($logContent),
     *                 nodeID#: ezjscAjaxContent::nodeEncode($logContent)
     *           }
     *
     * @param mixed $logContent
     * @return string $scriptTag
     */
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
}
