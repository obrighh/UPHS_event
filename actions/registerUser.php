<?php
    include 'actions/conn.php';

    if(isset($_POST['reg_submit'])){

        $l_name = $_POST['l_name'];
        $f_name = $_POST['f_name'];
        $m_name = $_POST['m_name'];
        $sch_id = $_POST['sch_id'];
        $regUsername = $_POST['regUsername'];
        $regPassword = $_POST['regPassword'];
        $num = 3;

        $hashPassword = password_hash($regPassword, PASSWORD_DEFAULT);

        $sql = "INSERT INTO accounts (username, password, f_name, m_name, l_name, sch_id, ut_id) 
                VALUES (?,?,?,?,?,?,?)
                ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $regUsername, $hashPassword, $f_name, $m_name, $l_name, $sch_id, $num);

        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo 
        '
            <script> alert("Account created successfully!"); </script>
        ';
    }
?>