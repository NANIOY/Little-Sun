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

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$allDaysThisMonth = generateDaysForMonth($currentYear, $currentMonth);

$user = new User();
$schedules = $user->fetchSchedule($user_id, "$currentYear-$currentMonth");

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
        <div class="workers__list">
            <?php
            $timeOffRequests = TimeOff::getAllForUser($_SESSION['user']['id']);
            if (!empty($timeOffRequests)): ?>
                <div class="workers__list__timeoff">
                    <?php foreach ($timeOffRequests as $request): ?>
                        <div class="workers__list__timeoff__request">
                            <span class="text-bold-normal">Date:
                            </span><?= date("Y-m-d H:i", strtotime($request['startDate'])) ?> to
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
            <div class="calendar__navigation">
                <div class="calendar__navigation__month">
                    <div class="calendar__navigation__buttons">
                        <button
                            onclick="navigateMonth(<?php echo ($currentMonth == 1) ? $currentYear - 1 : $currentYear; ?>, <?php echo ($currentMonth == 1) ? 12 : $currentMonth - 1; ?>)">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button
                            onclick="navigateMonth(<?php echo ($currentMonth == 12) ? $currentYear + 1 : $currentYear; ?>, <?php echo ($currentMonth == 12) ? 1 : $currentMonth + 1; ?>)">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    <h5><?php echo date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?></h5>
                </div>
                <div class="calendar__navigation__actions">
                    <button class="calendar__navigation__assign button--secondary"
                        onclick="navigateToAssignment('sick')" disabled>Assign sick days</button>
                    <button class="calendar__navigation__assign button--secondary"
                        onclick="navigateToAssignment('timeoff')" disabled>Request time off</button>
                </div>
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
                                    <span
                                        class="calendar__day__card__time text-reg-xs"><?php echo date('H:i', strtotime($schedule['start_time'])); ?></span>
                                </div>
                            <?php endif;
                        endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
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
    </script>
</body>

</html>