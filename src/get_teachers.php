<?php
$teachers = [
    "advisor" => ["name" => "อ.สมศรี ใจดี", "id" => "T001"],
    "subjectTeachers" => [
        ["name" => "อ.สมชาย", "subject" => "คณิตศาสตร์", "id" => "T002"],
        ["name" => "อ.สมหญิง", "subject" => "ภาษาไทย", "id" => "T003"]
    ]
];

echo json_encode($teachers);
?>
