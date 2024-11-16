<?php

function get_token(){
    $id = (int)$_SESSION['id'];
    $conn = Model::getModel();
    $sql="SELECT token FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows !== 1){
        echo "erreur";
    }else{
        $res = $result->fetch_assoc();
        return $res['token'];
    }


}



?>