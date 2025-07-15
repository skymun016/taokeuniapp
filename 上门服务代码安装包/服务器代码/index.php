<?php
if (!is_file('./data/install.lock')) {
    header("location:public/install/install.php");
    exit;
} else {
    header('Location: admin/');
}
