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
        if (is_object($logContent)) {
            ?>

            <!--- mjs_console_log output start -->
            <script>
                console.log({
                    "object<?php echo $logContent->ContentObjectID ?>": <?php echo json_encode($logContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK, 512);
                    if (isset($logContent->NodeID)) { ?>,
                    "node<?php echo $logContent->NodeID ?>": <?php echo ezjscAjaxContent::nodeEncode($logContent, array(
                    'dataMap' => array('all'),
                    'fetchPath' => true,
                    'fetchChildrenCount' => true,
                    'dataMapType' => array('all'),
                    'ImagePreGenerateSizes' => array('all')
                ));
                    } ?>
                });
            </script>
            <!--- mjs_console_log output end -->
            <?php
        } else {
            ?>

            <!--- mjs_console_log output start -->
            <script>
                console.log(
                    <?php echo json_encode($logContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK, 512); ?>
                );
            </script>
            <!--- mjs_console_log output end -->
            <?php
        }
    }
    private $Operators;
}
?>
