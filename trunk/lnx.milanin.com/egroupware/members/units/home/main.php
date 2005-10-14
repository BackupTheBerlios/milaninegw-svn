<?
//Milanegw members home page

//Initialisation
$function['home:init'][] = path . "units/home/function_init.php";

//Edit
$function['home:edit'][] = path . "units/home/function_edit.php";
$function['home:update_data'][] = path . "units/home/function_update_data.php";

//Display
$function['home:display'][] = path . "units/home/function_display.php";
$function['home:display:name'][] = path . "units/home/function_display_name.php";

//Menu
$function['menu:main'][] = path . "units/home/menu_main.php"
?>