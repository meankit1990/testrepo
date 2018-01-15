<?php
define("TABLE_USER", "signup");
define("COL_ID", "UID");
define("COL_USER_ID","username");
define("COL_USER_PASSWORD","pwd");
define("COL_FIRST_NAME","fname");
define("COL_LAST_NAME","lname");
define("COL_EMAIL","email");
define("COL_PREMIUM","premium");
define("COL_PREMIUM_PLUS","premiumplus");
define("COL_VIP","vipmember");
define("COL_EXPIRY_TIME","premiumexpirytime");
define("COL_LIFE_TIME","lifetimepremium");

define("TABLE_USER_ROLE","user_role");
define("COL_ROLE_NAME","role_name");

define("TABLE_USER_STATUS","user_status");
define("USER_ACTIVE",1);
define("USER_BLOCKED",2);
define("USER_PROFILE_INCOMPLETE",3);

?>
