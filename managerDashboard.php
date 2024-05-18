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

    if ($userId == 'all') {
        $userId = null;
    }

    switch ($reportType) {
        case 'hoursWorked':
            $reportData = Report::getHoursWorked($userId, $startDate, $endDate);
            break;
        case 'totalHoursWorked':
            $reportData = Report::getTotalHoursWorked($startDate, $endDate, $userId);
            break;
        case 'overtimeHours':
            $reportData = Report::getOvertimeHours($userId, $startDate, $endDate);
            break;
        case 'sickDays':
            $reportData = Report::getSickDays($userId, $startDate, $endDate);
            break;
        case 'timeOffRequests':
            $reportData = Report::getTimeOffRequests($startDate, $endDate);
            break;
        default:
            $reportData = [];
    }
}

$latestTimeOffRequests = Report::getLatestTimeOffRequests($locationId, 4);
$todaySchedule = (new Manager())->fetchSchedules($locationId, date('Y-m-d'));
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

    <div class="dashboard">

        <div class="dashboard__section">
            <h5 class="formContainer__title">Today's Schedule</h5>
            <?php if (!empty($todaySchedule)): ?>
                <div class="calendar">
                    <?php foreach ($todaySchedule as $schedule): ?>
                        <div class="calendar__day__card <?php echo $schedule['sick_date'] ? 'calendar__day__card--sick' : ''; ?>"
                            style="background-color: <?php echo htmlspecialchars($schedule['color']); ?>;">
                            <img src="<?php echo htmlspecialchars($schedule['profile_img']); ?>" alt="Profile Image"
                                class="calendar__day__card__img">
                            <span
                                class="calendar__day__card__task"><?php echo htmlspecialchars($schedule['task_title']); ?></span>
                            <span class="calendar__day__card__time">
                                <?php echo date('H:i', strtotime($schedule['start_time'])); ?> -
                                <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                            </span>
                            <?php if ($schedule['sick_date']): ?>
                                <div class="calendar__day__card--sick__indicator">Sick</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No scheduled tasks for today.</p>
            <?php endif; ?>
        </div>

        <div class="formContainer dashboard__section">
            <h5 class="formContainer__title">Generate Report</h5>
            <form method="POST" action="managerDashboard.php" class="formContainer__form">
                <div class="formContainer__form__field">
                    <label for="reportType">Select Report Type:</label>
                    <select name="reportType" id="reportType" class="formContainer__form__field__input">
                        <option value="hoursWorked">Hours Worked</option>
                        <option value="totalHoursWorked">Total Hours Worked</option>
                        <option value="overtimeHours">Overtime Hours</option>
                        <option value="sickDays">Sick Days</option>
                        <option value="timeOffRequests">Time Off Requests</option>
                    </select>
                </div>

                <div class="formContainer__form__field">
                    <label for="user_id">Worker:</label>
                    <select id="user_id" name="user_id" class="formContainer__form__field__input">
                        <option value="all">All Workers</option>
                        <?php foreach ($workers as $worker): ?>
                            <option value="<?php echo $worker['id']; ?>">
                                <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="formContainer__form__field">
                    <label for="startDate">Start Date:</label>
                    <input type="date" name="startDate" id="startDate" class="formContainer__form__field__input"
                        required>
                </div>

                <div class="formContainer__form__field">
                    <label for="endDate">End Date:</label>
                    <input type="date" name="endDate" id="endDate" class="formContainer__form__field__input" required>
                </div>

                <button type="submit" class="formContainer__form__button button--primary">Generate Report</button>
            </form>
        </div>

        <div class="dashboard__section">
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
        <div class="dashboard__section">
            <h5 class="formContainer__title">Report Results</h5>
            <?php if (!empty($reportData)): ?>
                <?php if ($reportType == 'hoursWorked' || $reportType == 'overtimeHours'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Worker</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Hours Worked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $entry): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($entry['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']); ?></td>
                                    <td><?php echo date('H:i', strtotime($entry['clock_in_time'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($entry['clock_out_time'])); ?></td>
                                    <td><?php echo number_format($entry['hours_worked'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($reportType == 'totalHoursWorked'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Worker</th>
                                <th>Total Hours Worked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']); ?></td>
                                    <td><?php echo number_format($entry['total_hours_worked'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($reportType == 'sickDays'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Worker</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $entry): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($entry['sick_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['reason']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($reportType == 'timeOffRequests'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Worker</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($entry['startDate'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($entry['endDate'])); ?></td>
                                    <td><?php echo htmlspecialchars($entry['reason']); ?></td>
                                    <td><?php echo getStatus($entry['approved']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php else: ?>
                <p>No data available for the selected report type and date range.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>