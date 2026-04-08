<?php

require_once "../../app/config/db.php";
require_once "../../app/helpers/Session.php";

Session::start();

if (!Session::has("user_id")) {
    die("Unauthorized access.");
}

$database = new Database();
$conn = $database->connect();

$type = $_GET["type"] ?? "";

$query = "SELECT employee_code, full_name, email, phone, department, designation, gender, join_date, status, address 
          FROM employees 
          ORDER BY id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Failed to fetch employee data.");
}

/* =========================
   EXPORT CSV
========================= */
if ($type === "csv") {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=employees_list.csv");

    $output = fopen("php://output", "w");

    fputcsv($output, [
        "Employee Code",
        "Full Name",
        "Email",
        "Phone",
        "Department",
        "Designation",
        "Gender",
        "Join Date",
        "Status",
        "Address"
    ]);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row["employee_code"],
            $row["full_name"],
            $row["email"],
            $row["phone"],
            $row["department"],
            $row["designation"],
            $row["gender"],
            $row["join_date"],
            $row["status"],
            $row["address"]
        ]);
    }

    fclose($output);
    exit();
}

/* =========================
   EXPORT PDF
========================= */
if ($type === "pdf") {
    require_once "../../app/helpers/fpdf/fpdf.php";

    $pdf = new FPDF("L", "mm", "A4");
    $pdf->AddPage();

    $pdf->SetFont("Arial", "B", 16);
    $pdf->Cell(0, 10, "MBSL Employee Directory - Employees Report", 0, 1, "C");

    $pdf->Ln(4);

    $pdf->SetFont("Arial", "B", 10);

    $pdf->Cell(28, 10, "Code", 1);
    $pdf->Cell(40, 10, "Name", 1);
    $pdf->Cell(50, 10, "Email", 1);
    $pdf->Cell(28, 10, "Phone", 1);
    $pdf->Cell(28, 10, "Department", 1);
    $pdf->Cell(32, 10, "Designation", 1);
    $pdf->Cell(18, 10, "Gender", 1);
    $pdf->Cell(22, 10, "Join Date", 1);
    $pdf->Cell(22, 10, "Status", 1);
    $pdf->Ln();

    $pdf->SetFont("Arial", "", 9);

    mysqli_data_seek($result, 0);

    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(28, 10, $row["employee_code"], 1);
        $pdf->Cell(40, 10, substr($row["full_name"], 0, 22), 1);
        $pdf->Cell(50, 10, substr($row["email"], 0, 28), 1);
        $pdf->Cell(28, 10, substr($row["phone"], 0, 15), 1);
        $pdf->Cell(28, 10, substr($row["department"], 0, 15), 1);
        $pdf->Cell(32, 10, substr($row["designation"], 0, 18), 1);
        $pdf->Cell(18, 10, $row["gender"], 1);
        $pdf->Cell(22, 10, $row["join_date"], 1);
        $pdf->Cell(22, 10, $row["status"], 1);
        $pdf->Ln();
    }

    $pdf->Output("D", "employees_list.pdf");
    exit();
}

die("Invalid export type.");