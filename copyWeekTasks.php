<?php
include_once(__DIR__ . '/includes/auth.inc.php');
include_once(__DIR__ . '/classes/ScheduleManager.php');
requireManager();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['sourceWeek']) || !isset($data['destinationWeek'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit();
}

$sourceWeek = $data['sourceWeek'];
$destinationWeek = $data['destinationWeek'];

function getWeekDates($year, $month, $day) {
    $startOfWeek = strtotime("last monday", strtotime("$year-$month-$day"));
    $weekDates = [];

    for ($i = 0; $i < 7; $i++) {
        $currentDay = strtotime("+$i days", $startOfWeek);
        $weekDates[] = date('Y-m-d', $currentDay);
    }

    return $weekDates;
}

$sourceDates = getWeekDates($sourceWeek['year'], $sourceWeek['month'], $sourceWeek['day']);
$destinationDates = getWeekDates($destinationWeek['year'], $destinationWeek['month'], $destinationWeek['day']);

$scheduleManager = new ScheduleManager();

foreach ($sourceDates as $index => $sourceDate) {
    $sourceSchedules = $scheduleManager->getSchedulesByDate($sourceDate);

    foreach ($sourceSchedules as $schedule) {
        $newSchedule = [
            'user_id' => $schedule['user_id'],
            'task_id' => $schedule['task_id'],
            'start_time' => $schedule['start_time'],
            'end_time' => $schedule['end_time'],
            'date' => $destinationDates[$index],
            'location_id' => $schedule['location_id']
        ];
        $scheduleManager->assignSchedule(
            $newSchedule['user_id'],
            $newSchedule['task_id'],
            $newSchedule['start_time'],
            $newSchedule['end_time'],
            $newSchedule['date'],
            $newSchedule['location_id']
        );
    }
}

echo json_encode(['success' => true, 'message' => 'Tasks copied successfully']);
