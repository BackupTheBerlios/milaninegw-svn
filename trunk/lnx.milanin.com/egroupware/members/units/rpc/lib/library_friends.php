<?php

    /*
     *  Functions related to ".tbl_prefix."friends.     */



    // All ".tbl_prefix."friends.of an elgg user, returns a query result construct
   
    function getElggFriends($ident)
    {
        //$ident = userNameToId($user);
        
        $result = db_query("select ".tbl_prefix."friends.friend as user_id,
                            ".tbl_prefix."users.name from ".tbl_prefix."friends 
                            left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend 
                            where ".tbl_prefix."friends.owner = '$ident'");

        return $result;
    }

    // Return all relations, nice for full data analysis

    function getElggFamily()
    {
        // Get a list of all ".tbl_prefix."users. including ".tbl_prefix."friends.(new query)
        
        // Get a list of all ".tbl_prefix."users.and call getElggFriends for each (expensive),
    
    }
    
    // Add a friend to user's ".tbl_prefix."friends.list
    
    function addElggFriend($user, $friend)
    {
        // Check identification and authorization, else throw exception
    }
    
    // Remove a foe from user's ".tbl_prefix."friends.list
    
    function removeElggFriend($user, $foe)
    {
        // Check identification and authorization, else throw exception
    }
        
?>
