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
    return date('W', strtotime($date));
}

function navigateWeek($year, $month, $day, $direction)
{
    $date = strtotime("$year-$month-$day");
    if ($direction === 'prev') {
        $newDate = strtotime('-1 week', $date);
    } else {
        $newDate = strtotime('+1 week', $date);
    }
    $newYear = date('Y', $newDate);
    $newMonth = date('m', $newDate);
    $newDay = date('d', $newDate);
    header("Location: ?year=$newYear&month=$newMonth&day=$newDay&view=week");
    exit();
}

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentDay = isset($_GET['day']) ? $_GET['day'] : date('d');
$view = isset($_GET['view']) ? $_GET['view'] : 'month';

$days = ($view == 'week') ? generateDaysForWeek($currentYear, $currentMonth, $currentDay) : generateDaysForMonth($currentYear, $currentMonth);

$user = new User();
$schedules = [];
foreach ($days as $day) {
    $daySchedules = $user->fetchSchedule($user_id, $day['date']);
    $schedules = array_merge($schedules, $daySchedules);
}

$sickDays = User::getSickDays($user_id, $currentYear, $currentMonth);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="workers">
        <div class="calendar__navigation">
            <div class="calendar__navigation__view">
                <button class="button--tertiary <?php echo ($view == 'month') ? 'active' : ''; ?>"
                    onclick="switchView('month')">Month</button>
                <button class="button--tertiary <?php echo ($view == 'week') ? 'active' : ''; ?>"
                    onclick="switchView('week')">Week</button>
            </div>
            <div class="calendar__navigation__month">
                <?php if ($view == 'month'): ?>
                    <button
                        onclick="navigateMonth(<?php echo ($currentMonth == 1) ? $currentYear - 1 : $currentYear; ?>, <?php echo ($currentMonth == 1) ? 12 : $currentMonth - 1; ?>)">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <h5><?php echo date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?></h5>
                    <button
                        onclick="navigateMonth(<?php echo ($currentMonth == 12) ? $currentYear + 1 : $currentYear; ?>, <?php echo ($currentMonth == 12) ? 1 : $currentMonth + 1; ?>)">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                <?php else: ?>
                    <button
                        onclick="navigateWeek(<?php echo $currentYear; ?>, <?php echo $currentMonth; ?>, <?php echo $currentDay; ?>, 'prev')">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <h5><?php echo "Week " . getWeekNumber("$currentYear-$currentMonth-$currentDay") . " of " . date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?>
                    </h5>
                    <button
                        onclick="navigateWeek(<?php echo $currentYear; ?>, <?php echo $currentMonth; ?>, <?php echo $currentDay; ?>, 'next')">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
            </div>
            <div class="calendar__navigation__actions">
                <button class="calendar__navigation__assign button--secondary" onclick="navigateToAssignment('sick')"
                    disabled>Assign sick days</button>
                <button class="calendar__navigation__assign button--primary" onclick="navigateToAssignment('timeoff')"
                    disabled>Request time off</button>
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
                <?php
                $isSickDay = false;
                foreach ($sickDays as $sickDay) {
                    if ($sickDay['date'] === $day['date']) {
                        $isSickDay = true;
                        break;
                    }
                }
                ?>
                <div class="calendar__day<?php echo $day['currentMonth'] ? '' : ' calendar__day--other'; ?><?php echo $isSickDay ? ' calendar__day--sick' : ''; ?>"
                    data-date="<?php echo htmlspecialchars($day['date']); ?>"
                    onclick="toggleDateSelection('<?php echo htmlspecialchars($day['date']); ?>')">
                    <div class="date-label"><?php echo date('d', strtotime($day['date'])); ?></div>
                    <?php if ($isSickDay): ?>
                        <i class="fas fa-viruses calendar__day--sick__icon"></i>
                    <?php endif; ?>
                    <?php foreach ($schedules as $schedule):
                        if ($schedule['date'] === $day['date']): ?>
                            <div class="calendar__day__card text-reg-s"
                                style="background-color: <?php echo htmlspecialchars($schedule['color']); ?>"
                                data-task-id="<?php echo htmlspecialchars($schedule['task_id']); ?>">
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
                        <?php endif;
                    endforeach; ?>
                    <?php foreach ($timesOff as $timeOff):
                        if ($timeOff['startDate'] <= $day['date'] && $timeOff['endDate'] >= $day['date']):
                            $statusClass = ($timeOff['approved'] === 2) ? 'approved' : (($timeOff['approved'] === 1) ? 'not-approved' : 'pending'); ?>
                            <div class="calendar__day__timeoff <?php echo $statusClass; ?>">
                                <span><?php echo htmlspecialchars($timeOff['reason']); ?></span>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        let selectedDates = [];

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

        function navigateToAssignment(type) {
            if (selectedDates.length === 0) {
                alert('No dates selected.');
                return;
            }
            let url = '';
            if (type === 'sick') {
                url = 'workerAssignSick.php';
            } else if (type === 'timeoff') {
                url = 'requestTime.php';
            }
            window.location.href = `${url}?dates=` + selectedDates.join(',');
        }

        function navigateMonth(year, month) {
            window.location.href = '?year=' + year + '&month=' + month;
        }

        function getWeekNumber($date) {
            return date('W', strtotime($date));
        }

        function navigateWeek(year, month, day, direction) {
            const url = `?year=${year}&month=${month}&day=${day}&view=week&direction=${direction}`;
            window.location.href = url;
        }

        function switchView(view) {
            let url = `?year=<?php echo $currentYear; ?>&month=<?php echo $currentMonth; ?>&day=<?php echo $currentDay; ?>&view=` + view;
            window.location.href = url;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const view = urlParams.get('view');
            if (view === 'week') {
                document.querySelector('.calendar').classList.add('week-view');
            }
        });
    </script>
</body>

</html>