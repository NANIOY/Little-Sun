<?php

include_once(__DIR__ . '/classes/Db.php');

// Set up database connection using the Db class
$db = Db::getInstance();

// Custom session handler class
class DBSessionHandler implements SessionHandlerInterface
{
    private $pdo;
    private $table;

    public function __construct(PDO $pdo, $table = 'sessions')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $stmt = $this->pdo->prepare("SELECT data FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['data'];
        }

        return '';
    }

    public function write($id, $data)
    {
        $timestamp = time();
        $stmt = $this->pdo->prepare("REPLACE INTO {$this->table} (id, data, timestamp) VALUES (:id, :data, :timestamp)");
        return $stmt->execute(['id' => $id, 'data' => $data, 'timestamp' => $timestamp]);
    }

    public function destroy($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function gc($maxLifetime)
    {
        $old = time() - $maxLifetime;
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE timestamp < :old");
        return $stmt->execute(['old' => $old]);
    }
}

// Set the custom session handler
$handler = new DBSessionHandler($db);
session_set_save_handler($handler, true);

// Start the session
session_start();

include_once(__DIR__ . '/classes/User.php');

echo 'test session 4';
error_log('Session path: ' . session_save_path());
error_log('Session ID: ' . session_id());
error_log('Session data at start: ' . print_r($_SESSION, true));

$users = User::getAll();

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    error_log('Form submitted: ' . print_r($_POST, true));

    $user = User::getByEmail($email, $password);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION["user"] = $user;

            error_log('User authenticated: ' . print_r($user, true));
            error_log('Session data after login: ' . print_r($_SESSION, true));

            if ($user['role'] === 'admin') {
                header('Location: managers.php');
                exit();
            } elseif ($user['role'] === 'manager') {
                header('Location: managerDashboard.php');
                exit();
            } elseif ($user['role'] === 'worker') {
                header('Location: workerDashboard.php');
                exit();
            }
        } else {
            error_log('Password verification failed for user: ' . $email);
            $error = true;
        }
    } else {
        error_log('No user found with email: ' . $email);
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LITTLESUN ☀️ | Login </title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <link rel="stylesheet" href="css/pagestyles/login.css">
</head>

<body>
    <main>
        <div class="LittleSunTitleShiftplanner">
            <h2>Little <strong style="color:#F7BD01;">Sun</strong> Shiftplanner</h2>
            <p>Welcome to Little Sun Shiftplanner, the ultimate platform for shift planners in Zambia! At Little Sun
                Shiftplanner, we empower workers to take control of their schedules by defining their roles and
                selecting preferred work locations. Our user-friendly interface allows workers to plan their
                availability for shifts and even schedule well-deserved vacations with ease.</p>
        </div>
        <div class="formContainer">
            <h4 class="formContainer__title">Welcome</h4>
            <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
                <?php if (isset($error)): ?>
                    <div class="form__error">
                        <p>
                            Sorry, we can't log you in with that email address and password. Can you try again?
                        </p>
                    </div>
                <?php endif; ?>
                <div class="formContainer__form__field">
                    <label for="email" class="text-reg-s">Email:</label>
                    <input type="email" id="email" name="email"
                        class="formContainer__form__field__input text-reg-normal" required>
                </div>
                <div class="formContainer__form__field">
                    <label for="password" class="text-reg-s">Password:</label>
                    <input type="password" id="password" name="password"
                        class="formContainer__form__field__input text-reg-normal" required>
                </div>
                <div class="formContainer__form__field">
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="formContainer__form__button button--primary">Log In</button>
            </form>
        </div>
    </main>
</body>

</html>
