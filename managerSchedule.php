<?php
session_start();
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

function getWeekNumber($date)
{
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
                        value="<?php echo $task['id']; ?>" class="filter-task" data-task-id="<?php echo $task['id']; ?>">
                    <label for="task-<?php echo $task['id']; ?>"><?php echo $task['title']; ?></label>
                </div>
            <?php endforeach; ?>
            <h4 class="filter__header--two">Workers</h4>
            <?php foreach ($workers as $worker): ?>
                <div>
                    <input type="checkbox" id="worker-<?php echo $worker['id']; ?>" name="workers"
                        value="<?php echo $worker['id']; ?>" class="filter-worker"
                        data-worker-id="<?php echo $worker['id']; ?>">
                    <label
                        for="worker-<?php echo $worker['id']; ?>"><?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?></label>
                </div>
            <?php endforeach; ?>
            <div class="filter__buttons">
                <button class="button--secondary" onclick="applyFilters()">Apply Filters</button>
                <button class="button--tertiary" onclick="removeFilters()">Remove Filters</button>
            </div>
        </div>

        <div class="tooltip text-reg-xs" id="tooltip"></div>

        <div class="workers">

            <div class="calendar__navigation">
                <div class="calendar__navigation__view">
                    <button class="button--tertiary <?php echo ($view == 'month') ? 'active' : ''; ?>"
                        onclick="switchView('month')">Month</button>
                    <button class="button--tertiary <?php echo ($view == 'week') ? 'active' : ''; ?>"
                        onclick="switchView('week')">Week</button>
                </div>
                <div class="calendar__navigation__month">
                    <button onclick="<?php if ($view == 'week') {
                        echo "navigateWeek($currentYear, $currentMonth, $currentDay, 'prev')";
                    } else {
                        echo "navigateMonth($currentYear, $currentMonth - 1)";
                    } ?>">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <h5><?php echo ($view == 'week') ? "Week " . getWeekNumber("$currentYear-$currentMonth-$currentDay") . " of " . date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')) : date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?>
                    </h5>
                    <button onclick="<?php if ($view == 'week') {
                        echo "navigateWeek($currentYear, $currentMonth, $currentDay, 'next')";
                    } else {
                        echo "navigateMonth($currentYear, $currentMonth + 1)";
                    } ?>">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
                <div class="calendar__navigation__actions">
                    <?php if ($view == 'week'): ?>
                        <button class="calendar__navigation__copy button--secondary" id="copyWeekButton"
                            onclick="copyWeekTasks()">Copy week
                            tasks</button>
                        <button class="calendar__navigation__copy button--secondary" id="pasteWeekButton"
                            onclick="pasteWeekTasks()" disabled>Paste
                            week tasks</button>
                    <?php endif; ?>
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

                        usort($schedules, function ($a, $b) {
                            return strtotime($a['start_time']) - strtotime($b['start_time']);
                        });

                        foreach ($schedules as $schedule): ?>
                            <div class="calendar__day__card text-reg-s <?php echo $schedule['sick_date'] ? 'calendar__day__card--sick' : ''; ?>"
                                style="background-color: <?php echo htmlspecialchars($schedule['color']); ?>"
                                data-task-id="<?php echo htmlspecialchars($schedule['task_id']); ?>"
                                data-worker-id="<?php echo htmlspecialchars($schedule['user_id']); ?>"
                                onclick="event.stopPropagation(); window.location.href='editSchedule.php?schedule_id=<?php echo htmlspecialchars($schedule['id']); ?>'">
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
                                <?php if ($schedule['sick_date']): ?>
                                    <div class="calendar__day__card--sick__indicator">Sick</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            let selectedDates = [];
            const tooltip = document.getElementById('tooltip');

            function toggleDateSelection(date) {
                const index = selectedDates.indexOf(date);
                if (index === -1) {
                    selectedDates.push(date);
                } else {
                    selectedDates.splice(index, 1);
                }

                document.querySelector(`.calendar__day[data-date="${date}"]`).classList.toggle('selected');
                updateAssignButtonState();
            }

            function updateAssignButtonState() {
                const assignButtons = document.querySelectorAll('.calendar__navigation__assign');
                assignButtons.forEach(button => {
                    if (selectedDates.length === 0) {
                        button.classList.remove('enabled');
                        button.disabled = true;
                    } else {
                        button.classList.add('enabled');
                        button.disabled = false;
                    }
                });
            }

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
                let url = `?year=<?php echo $currentYear; ?>&month=<?php echo $currentMonth; ?>&day=<?php echo $currentDay; ?>&view=` + view;
                window.location.href = url;
            }

            function showTooltip(text) {
                tooltip.innerHTML = text;
                tooltip.style.display = 'block';
            }

            function hideTooltip() {
                tooltip.style.display = 'none';
            }

            function moveTooltip(event) {
                tooltip.style.left = event.pageX + 10 + 'px';
                tooltip.style.top = event.pageY + 10 + 'px';
            }

            function copyWeekTasks() {
                const sourceWeek = {
                    year: <?php echo $currentYear; ?>,
                    month: <?php echo $currentMonth; ?>,
                    day: <?php echo $currentDay; ?>
                };
                localStorage.setItem('sourceWeek', JSON.stringify(sourceWeek));
                alert('Source week copied. Now go to the destination week and click "Paste Week Tasks".');
                document.getElementById('pasteWeekButton').disabled = false;
            }

            function pasteWeekTasks() {
                const sourceWeek = JSON.parse(localStorage.getItem('sourceWeek'));

                if (!sourceWeek) {
                    alert('Please copy a source week first by clicking "Copy week tasks".');
                    return;
                }

                const destinationWeek = {
                    year: <?php echo $currentYear; ?>,
                    month: <?php echo $currentMonth; ?>,
                    day: <?php echo $currentDay; ?>
                };

                const data = {
                    sourceWeek: sourceWeek,
                    destinationWeek: destinationWeek
                };

                fetch('copyWeekTasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem('sourceWeek');
                            location.reload();
                        } else {
                            alert('Error copying tasks: ' + data.message);
                        }
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                const view = urlParams.get('view');
                if (view === 'week') {
                    document.querySelector('.calendar').classList.add('week-view');
                }

                const sourceWeek = JSON.parse(localStorage.getItem('sourceWeek'));
                if (sourceWeek) {
                    document.getElementById('pasteWeekButton').disabled = false;
                }

                document.querySelectorAll('.calendar__day').forEach(day => {
                    day.addEventListener('mouseover', (event) => {
                        showTooltip('click to add task');
                        moveTooltip(event);
                    });
                    day.addEventListener('mousemove', moveTooltip);
                    day.addEventListener('mouseout', hideTooltip);
                });

                document.querySelectorAll('.calendar__day__card').forEach(card => {
                    card.addEventListener('mouseover', (event) => {
                        event.stopPropagation();
                        showTooltip('click to edit task');
                        moveTooltip(event);
                    });
                    card.addEventListener('mousemove', moveTooltip);
                    card.addEventListener('mouseout', hideTooltip);
                });
            });

            function applyFilters() {
                const selectedTasks = Array.from(document.querySelectorAll('.filter-task:checked')).map(cb => cb.dataset.taskId);
                const selectedWorkers = Array.from(document.querySelectorAll('.filter-worker:checked')).map(cb => cb.dataset.workerId);

                document.querySelectorAll('.calendar__day__card').forEach(card => {
                    const taskId = card.dataset.taskId;
                    const workerId = card.dataset.workerId;

                    const taskMatch = selectedTasks.length === 0 || selectedTasks.includes(taskId);
                    const workerMatch = selectedWorkers.length === 0 || selectedWorkers.includes(workerId);

                    if (taskMatch && workerMatch) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            function removeFilters() {
                document.querySelectorAll('.filter-task, .filter-worker').forEach(cb => cb.checked = false);
                document.querySelectorAll('.calendar__day__card').forEach(card => card.style.display = '');
            }
        </script>
</body>

</html>