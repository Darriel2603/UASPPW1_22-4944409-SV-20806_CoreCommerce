<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {

    $valid = 1;
        
    if(empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_131."\\n";
    } else {
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= LANG_VALUE_134."\\n";
        } else {
            $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();                        
            if(!$total) {
                $valid = 0;
                $error_message .= LANG_VALUE_135."\\n";
            }
        }
    }
    if ($valid == 1) {
        $statement = $pdo->prepare("SELECT * FROM settings WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $forget_password_message = $row['forget_password_message'];
        }
    
        $token = md5(rand());
        $now = time();
        
        $statement = $pdo->prepare("UPDATE customer SET cust_token=?,cust_timestamp=? WHERE cust_email=?");
        $statement->execute(array($token, $now, strip_tags($_POST['cust_email'])));
    
        // Redirect to customer-password-forget.php
        header("Location: customer-password-forget.php?email=" . $_POST['cust_email'] . "&token=" . $token);
        exit;
    }
    
}
?>

<div class="page-banner">
    <div class="inner">
        <h1><?php echo LANG_VALUE_97; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                <?php
                    if($error_message != '') {
                        echo "<script>alert('".$error_message."')</script>";
                    }
                    if($success_message != '') {
                        echo "<script>alert('".$success_message."')</script>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email">
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_4; ?>" name="form1">
                                </div>
                                <a href="login.php" style="color:#e4144d;"><?php echo LANG_VALUE_12; ?></a>
                            </div>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>