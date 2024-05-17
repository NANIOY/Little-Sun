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

function generateDaysForWeek($year, $month, $day)
{
    $startOfWeek = strtotime("last monday", strtotime("$year-$month-$day"));
    $days = [];

    for ($i = 0; $i < 7; $i++) {
        $currentDay = strtotime("+$i days", $startOfWeek);
        $days[] = [
            'date' => date('Y-m-d', $currentDay),
            'currentMonth' => (date('m', $currentDay) == $month)
        ];
    }

    return $days;
}

function getWeekNumber($date) {
    $timestamp = strtotime($date);
    $firstDayOfMonth = date('Y-m-01', $timestamp);
    $weekNumber = intval(date('W', $timestamp)) - intval(date('W', strtotime($firstDayOfMonth))) + 1;
    return $weekNumber;
}

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentDay = isset($_GET['day']) ? $_GET['day'] : date('d');
$view = isset($_GET['view']) ? $_GET['view'] : 'month';

$days = ($view == 'week') ? generateDaysForWeek($currentYear, $currentMonth, $currentDay) : generateDaysForMonth($currentYear, $currentMonth);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            <div class="filter__buttons">
                <button class="button--secondary" onclick="applyFilters()">Apply Filters</button>
                <button class="button--tertiary" onclick="removeFilters()">Remove Filters</button>
            </div>
        </div>
        <div class="workers">
            <div class="calendar__navigation">
                <div class="calendar__navigation__view">
                    <button class="button--tertiary <?php echo ($view == 'month') ? 'active' : ''; ?>"
                        onclick="switchView('month')">Month</button>
                    <button class="button--tertiary <?php echo ($view == 'week') ? 'active' : ''; ?>"
                        onclick="switchView('week')">Week</button>
                </div>
                <div class="calendar__navigation__month">
                    <button
                        onclick="<?php if ($view == 'week') {
                            echo "navigateWeek($currentYear, $currentMonth, $currentDay, 'prev')";
                        } else {
                            echo "navigateMonth($currentYear, $currentMonth - 1)";
                        } ?>">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <h5><?php echo ($view == 'week') ? "Week " . getWeekNumber("$currentYear-$currentMonth-$currentDay") . " of " . date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')) : date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?>
                    </h5>
                    <button
                        onclick="<?php if ($view == 'week') {
                            echo "navigateWeek($currentYear, $currentMonth, $currentDay, 'next')";
                        } else {
                            echo "navigateMonth($currentYear, $currentMonth + 1)";
                        } ?>">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="calendar text-reg-normal <?php echo ($view == 'week') ? 'week-view' : ''; ?>">
                <div class="text-bold-normal">Mon</div>
                <div class="text-bold-normal">Tue</div>
                <div class="text-bold-normal">Wed</div>
                <div class="text-bold-normal">Thu</div>
                <div class="text-bold-normal">Fri</div>
                <div class="text-bold-normal">Sat</div>
                <div class="text-bold-normal">Sun</div>
                <?php foreach ($days as $day): ?>
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
                                <img src="<?php echo htmlspecialchars($schedule['profile_img']); ?>" alt="Profile Image"
                                    class="calendar__day__card__img">
                                <span
                                    class="calendar__day__card__task"><?php echo htmlspecialchars($schedule['task_title']); ?></span>
                                <?php if ($view == 'week'): ?>
                                    <span class="calendar__day__card__time text-reg-xs">
                                        <?php echo date('H:i', strtotime($schedule['start_time'])); ?> -
                                        <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                                    </span>
                                <?php elseif ($view == 'month'): ?>
                                    <span class="calendar__day__card__time text-reg-xs">
                                        <?php echo date('H:i', strtotime($schedule['start_time'])); ?>
                                    </span>
                                <?php endif; ?>
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

            function navigateWeek(year, month, day, direction) {
                let date = new Date(year, month - 1, day);
                if (direction === 'prev') {
                    date.setDate(date.getDate() - 7);
                } else {
                    date.setDate(date.getDate() + 7);
                }
                let newYear = date.getFullYear();
                let newMonth = date.getMonth() + 1;
                let newDay = date.getDate();
                window.location.href = `?year=${newYear}&month=${newMonth}&day=${newDay}&view=week`;
            }

            function switchView(view) {
                let url = `?year=<?php echo $currentYear; ?>&month=<?php echo $currentMonth; ?>&view=` + view;
                window.location.href = url;
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

            function removeFilters() {
                document.querySelectorAll('input[type="checkbox"][name="tasks"], input[type="checkbox"][name="workers"]').forEach(checkbox => {
                    checkbox.checked = false;
                });

                document.querySelectorAll('.calendar__day__card').forEach(card => {
                    card.style.display = '';
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                const view = urlParams.get('view');
                if (view === 'week') {
                    document.querySelector('.calendar').classList.add('week-view');
                }
            });
        </script>
    </div>
</body>

</html>