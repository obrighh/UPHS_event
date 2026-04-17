<?php
    require 'conn.php';

    if(isset($_POST['add_organization'])){
        $org_name = $_POST['org_name'];

        $sql =
        "INSERT INTO organizations (org_name)
        VALUES (?)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $org_name);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo
        '
        <script> window.location = "../users/dean/html/organizations.php "</script>
        ';
        
    }
?>