<?php
    
if(count($_ViewData) == 0) {
    echo "No address parameters present.";
} else {
    echo "<ul>";
    foreach($_ViewData as $k => $v){
        echo "<li>$k = $v</li>";
    }
    echo "</ul>";
}