<?php
session_start(); // දැනට තියෙන session එක හඳුනාගන්න

// 1. සියලුම session variables ඉවත් කිරීම
session_unset();

// 2. session එක සම්පූර්ණයෙන්ම විනාශ කිරීම
session_destroy();

// 3. යූසර්ව නැවත login.php වෙත යොමු කිරීම
header("Location: index.php");
exit();
?>