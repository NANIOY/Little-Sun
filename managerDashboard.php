<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/Manager.php');
include_once (__DIR__ . '/classes/Report.php');
include_once (__DIR__ . '/classes/User.php');

requireManager();

$locationId = $_SESSION['user']['location_id'];
$workers = User::getAllWorkers($locationId);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportType = $_POST['reportType'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $userId = $_POST['user_id'] ?? null;

    switch ($reportType) {
        case 'hoursWorked':
            $reportData = Report::getHoursWorked($userId, $startDate, $endDate);
            break;
        case 'totalHoursWorked':
            $reportData = Report::getTotalHoursWorked($startDate, $endDate);
            break;
        case 'overtimeHours':
            $reportData = Report::getOvertimeHours($userId, $startDate, $endDate);
            break;
        case 'sickHours':
            $reportData = Report::getSickHours($userId, $startDate, $endDate);
            break;
        case 'timeOffRequests':
            $reportData = Report::getTimeOffRequests($startDate, $endDate);
            break;
        default:
            $reportData = [];
    }
}

$latestTimeOffRequests = Report::getLatestTimeOffRequests($locationId, 4);
$todaySchedule = Report::getTodaySchedule($locationId);
$clockedInWorkers = Report::getClockedInWorkers($locationId);

function getStatus($approvedCode)
{
    switch ($approvedCode) {
        case 0:
            return 'Pending';
        case 1:
            return 'Declined';
        case 2:
            return 'Approved';
        default:
            return 'Unknown';
    }
}

function formatDateRange($startDate, $endDate)
{
    $start = date("d/m", strtotime($startDate));
    $end = date("d/m", strtotime($endDate));
    return $start . ' - ' . $end;
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Manager Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <link rel="stylesheet" href="css/pagestyles/managerdashboard.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>
    <h4>Manager Dashboard</h4>

    <div class="dashboard-section">
        <h5 class="formContainer__title">Recent Time Off Requests</h5>
        <?php if (!empty($latestTimeOffRequests)): ?>
            <ul>
                <?php foreach ($latestTimeOffRequests as $request): ?>
                    <li>
                        <strong>Employee:</strong>
                        <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?><br>
                        <strong>Date Range:</strong> <?= formatDateRange($request['startDate'], $request['endDate']) ?><br>
                        <strong>Reason:</strong> <?= htmlspecialchars($request['reason']) ?><br>
                        <strong>Status:</strong> <?= getStatus($request['approved']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No recent time off requests.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-section">
        <h5 class="formContainer__title">Today's Schedule</h5>
        <?php if (!empty($todaySchedule)): ?>
            <ul>
                <?php foreach ($todaySchedule as $schedule): ?>
                    <li><?php echo $schedule['start_time']; ?> to <?php echo $schedule['end_time']; ?>:
                        <?php echo $schedule['first_name'] . ' ' . $schedule['last_name']; ?> -
                        <?php echo $schedule['task_title']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No scheduled tasks for today.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-section">
        <h5 class="formContainer__title">Clocked In Workers</h5>
        <?php if (!empty($clockedInWorkers)): ?>
            <ul>
                <?php foreach ($clockedInWorkers as $worker): ?>
                    <li><?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?> clocked in at
                        <?php echo $worker['clock_in_time']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No workers currently clocked in.</p>
        <?php endif; ?>
    </div>

    <div class="formContainer">
        <h5 class="formContainer__title">Generate Report</h5>
        <form method="POST" action="managerDashboard.php" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="reportType">Select Report Type:</label>
                <select name="reportType" id="reportType" class="formContainer__form__field__input">
                    <option value="hoursWorked">Hours Worked</option>
                    <option value="totalHoursWorked">Total Hours Worked</option>
                    <option value="overtimeHours">Overtime Hours</option>
                    <option value="sickHours">Sick Hours</option>
                    <option value="timeOffRequests">Time Off Requests</option>
                </select>
            </div>

            <div class="formContainer__form__field">
                <label for="user_id">Worker:</label>
                <select id="user_id" name="user_id" class="formContainer__form__field__input">
                    <?php foreach ($workers as $worker): ?>
                        <option value="<?php echo $worker['id']; ?>">
                            <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="formContainer__form__field">
                <label for="startDate">Start Date:</label>
                <input type="date" name="startDate" id="startDate" class="formContainer__form__field__input" required>
            </div>

            <div class="formContainer__form__field">
                <label for="endDate">End Date:</label>
                <input type="date" name="endDate" id="endDate" class="formContainer__form__field__input" required>
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Generate Report</button>
        </form>
    </div>

    <?php if (isset($reportData)): ?>
        <h2>Report Results</h2>
        <pre><?php print_r($reportData); ?></pre>
    <?php endif; ?>
</body>

</html>