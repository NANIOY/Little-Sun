<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Calendar.php');
include_once (__DIR__ . '/classes/Task.php');

requireWorker();
$user_id = $_SESSION['user']['id'];
$worker = User::getById($user_id);

$timesOff = TimeOff::getAllForUser($user_id);

/*$schedules = User::fetchSchedule($locationId, $date);*/


function generateDaysForMonth($year, $month)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = date('N', strtotime("$year-$month-01"));
    $days = [];

    $daysFromPrevMonth = $firstDayOfMonth - 1;
    $prevMonth = $month - 1;
    $prevYear = $year;
    if ($prevMonth == 0) {
        $prevMonth = 12;
        $prevYear--;
    }

    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

    for ($i = $daysInPrevMonth - $daysFromPrevMonth + 1; $i <= $daysInPrevMonth; $i++) {
        $days[] = [
            'date' => sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $i),
            'currentMonth' => false,
        ];
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $days[] = [
            'date' => sprintf('%04d-%02d-%02d', $year, $month, $day),
            'currentMonth' => true,
        ];
    }

    $lastDayOfMonth = date('N', strtotime("$year-$month-$daysInMonth"));

    $daysToEndOfMonth = 7 - $lastDayOfMonth;
    $nextMonth = $month + 1;
    $nextYear = $year;
    if ($nextMonth == 13) {
        $nextMonth = 1;
        $nextYear++;
    }

    for ($i = 1; $i <= $daysToEndOfMonth; $i++) {
        $days[] = [
            'date' => sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $i),
            'currentMonth' => false,
        ];
    }

    return $days;
}

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$allDaysThisMonth = generateDaysForMonth($currentYear, $currentMonth);



/*$schedules = $worker->fetchSchedule($locationId, "$currentYear-$currentMonth");*/


?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | <?= htmlspecialchars($worker['first_name']) ?>'s Schedule</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workers.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
    <link rel="stylesheet" href="css/pagestyles/calendar.css">

</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>My Schedule</h3>
            <button onclick="window.location.href='requestTime.php'" class="button--primary">Request time off</button>
        </div>

        <div class="workers__list">
            <?php
            $timeOffRequests = TimeOff::getAllForUser($_SESSION['user']['id']);
            if (!empty($timeOffRequests)): ?>
                <div class="workers__list__timeoff">
                    <?php foreach ($timeOffRequests as $request): ?>
                        <div class="workers__list__timeoff__request">
                            <span class="text-bold-normal">Date: </span><?= date("Y-m-d H:i", strtotime ($request['startDate'])) ?> to 
                            <?= date("Y-m-d H:i", strtotime($request['endDate'])) ?><br>
                            <span class="text-bold-normal">Reason: </span><?= htmlspecialchars($request['reason']) ?><br>
                            <span class="text-bold-normal">Status:
                            </span><?= isset($request['approved']) && $request['approved'] ? 'Approved' : 'Pending' ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <p>No time off requests found.</p>
            <?php endif; ?>
        </div>

        <div class="workers">
            <div class="workers__header">
                <h3>Schedule</h3>
            </div>
            <div class="calendar__navigation">
                <button class="button--primary"
                    onclick="navigateMonth(<?php echo ($currentMonth == 1) ? $currentYear - 1 : $currentYear; ?>, <?php echo ($currentMonth == 1) ? 12 : $currentMonth - 1; ?>)">Prev</button>
                <h5><?php echo date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?></h5>
                <button class="button--primary"
                    onclick="navigateMonth(<?php echo ($currentMonth == 12) ? $currentYear + 1 : $currentYear; ?>, <?php echo ($currentMonth == 12) ? 1 : $currentMonth + 1; ?>)">Next</button>
            </div>
            <div class="calendar text-reg-normal">
                <div class="text-bold-normal">Mon</div>
                <div class="text-bold-normal">Tue</div>
                <div class="text-bold-normal">Wed</div>
                <div class="text-bold-normal">Thu</div>
                <div class="text-bold-normal">Fri</div>
                <div class="text-bold-normal">Sat</div>
                <div class="text-bold-normal">Sun</div>
                <?php foreach ($allDaysThisMonth as $day): ?>
                    <div class="calendar__day<?php echo $day['currentMonth'] ? '' : ' calendar__day--other'; ?>"
                        onclick="navigateToAssignment('<?php echo htmlspecialchars($day['date']); ?>')">
                        <div class="date-label"><?php echo date('d', strtotime($day['date'])); ?></div>
                        <?php
                        /*$schedules = $worker->fetchSchedule($locationId, $day['date']);*/
                        foreach ($schedules as $schedule): ?>
                            <div class="calendar__day__card text-reg-s"
                                style="background-color: <?php echo htmlspecialchars($schedule['color']); ?>"
                                data-task-id="<?php echo htmlspecialchars($schedule['task_id']); ?>"
                                data-worker-id="<?php echo htmlspecialchars($schedule['user_id']); ?>">
                                <strong><?php echo htmlspecialchars($schedule['task_title']); ?></strong><br>
                                <span><?php echo htmlspecialchars($schedule['first_name']) . ' ' . htmlspecialchars($schedule['last_name']); ?></span><br>
                                <small><?php echo date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <script>
            function navigateToAssignment(date) {
                window.location.href = 'managerAssign.php?date=' + date;
            }

            function navigateMonth(year, month) {
                window.location.href = '?year=' + year + '&month=' + month;
            }
        </script>
</body>

</html>