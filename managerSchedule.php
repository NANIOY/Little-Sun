<?php
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
        $days[] = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $i);
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $days[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
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
        $days[] = sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $i);
    }

    return $days;
}

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

$allDaysThisMonth = generateDaysForMonth($currentYear, $currentMonth);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
    <link rel="stylesheet" href="css/pagestyles/calendar.css">
</head>

<body>
    <div class="container">
        <?php include_once ("./includes/managerNav.inc.php"); ?>

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
            <div class="calendar">
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
                <div>Sun</div>
                <?php foreach ($allDaysThisMonth as $day): ?>
                    <div class="day" onclick="navigateToAssignment('<?php echo $day; ?>')">
                        <?php echo date('d', strtotime($day)); ?>
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
        </script>
    </div>
</body>

</html>