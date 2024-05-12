<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/Manager.php');
include_once (__DIR__ . '/classes/Calendar.php');
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/classes/User.php');
requireManager();

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

$manager = new Manager();
$manager->setId($_SESSION['user']['id']);
$manager->setHubLocation($_SESSION['user']['location_id']);

$locationId = $manager->getHubLocation();

$schedules = $manager->fetchSchedules($locationId, "$currentYear-$currentMonth");

$tasks = Task::getAll();
$workers = User::getAllWorkers($locationId);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Schedule</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
    <link rel="stylesheet" href="css/pagestyles/calendar.css">
</head>

<body>
    <div class="container">
        <?php include_once ("./includes/managerNav.inc.php"); ?>
        <div class="filter">
            <h4 class="filter__header">Tasks</h4>
            <?php foreach ($tasks as $task): ?>
                <div>
                    <input type="checkbox" id="task-<?php echo $task['id']; ?>" name="tasks"
                        value="<?php echo $task['id']; ?>">
                    <label for="task-<?php echo $task['id']; ?>"><?php echo $task['title']; ?></label>
                </div>
            <?php endforeach; ?>
            <h4 class="filter__header--two">Workers</h4>
            <?php foreach ($workers as $worker): ?>
                <div>
                    <input type="checkbox" id="worker-<?php echo $worker['id']; ?>" name="workers"
                        value="<?php echo $worker['id']; ?>">
                    <label
                        for="worker-<?php echo $worker['id']; ?>"><?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?></label>
                </div>
            <?php endforeach; ?>
            <button class="button--secondary" onclick="applyFilters()">Apply Filters</button>
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
                        $schedules = $manager->fetchSchedules($locationId, $day['date']);
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
        <script>
            function navigateToAssignment(date) {
                window.location.href = 'managerAssign.php?date=' + date;
            }

            function navigateMonth(year, month) {
                window.location.href = '?year=' + year + '&month=' + month;
            }

            function applyFilters() {
                var tasksChecked = Array.from(document.querySelectorAll('[name="tasks"]:checked')).map(input => input.value);
                var workersChecked = Array.from(document.querySelectorAll('[name="workers"]:checked')).map(input => input.value);

                document.querySelectorAll('.calendar__day__card').forEach(card => {
                    var taskMatch = tasksChecked.length === 0 || tasksChecked.includes(card.getAttribute('data-task-id'));
                    var workerMatch = workersChecked.length === 0 || (card.getAttribute('data-worker-id') && workersChecked.includes(card.getAttribute('data-worker-id')));

                    if (taskMatch && workerMatch) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
        </script>
    </div>
</body>

</html>