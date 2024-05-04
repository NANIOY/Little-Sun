<?php
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();


if (!empty($_POST)) {
    try {
        $task = new Task();
        $task->setTitle($_POST['title']);
        $task->setColor($_POST['color']);
        $task->save();

        header('Location: tasks.php');
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Add Task</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <style>
       
    </style>
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Add Task</h4>
        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="title" class="text-reg-s">Type:</label>
                <input type="text" id="title" name="title" class="formContainer__form__field__input text-reg-normal"
                    required>
            </div>

            <div class="formContainer__form__field">
                <label class="text-reg-s">Color:</label>
                <div class="color">
                    <?php
                    $colors = ['#FF6976', '#F069FF', '#7A68FE', '#69B4FF', '#69F0AE', '#69FFB7', '#E9FF69', '#FFDA69', '#900017', '#620078', '#050094', '#0061A6', '#008587', '#007A35', '#727A00', '#795000'];
                    foreach ($colors as $color) {
                        echo "<input type='radio' id='color{$color}' name='color' value='{$color}' class='color__input'>
                              <label for='color{$color}' class='color__choice' style='background-color: {$color};'></label>";
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Add Task</button>
        </form>
    </div>
</body>

</html>