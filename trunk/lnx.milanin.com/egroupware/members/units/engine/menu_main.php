<?php
        $run_result .= run("templates:draw", array(
                                                'context' => 'menuitem',
                                                'name' => 'Log Off',
                                                'location' =>  url . '../egroupware/logout.php?sessionid='.session_id()
                                        )
                                        );
?>
