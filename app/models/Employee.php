<?php

class Employee
{
    private $conn;
    private $table = "employees";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function exists($employee_code, $email, $excludeId = null)
    {
        if ($excludeId) {
            $query = "SELECT id FROM {$this->table} WHERE (employee_code = ? OR email = ?) AND id != ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $employee_code, $email, $excludeId);
        } else {
            $query = "SELECT id FROM {$this->table} WHERE employee_code = ? OR email = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $employee_code, $email);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        return mysqli_stmt_num_rows($stmt) > 0;
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table}
            (employee_code, full_name, email, phone, department, designation, gender, join_date, status, photo, address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssss",
            $data["employee_code"],
            $data["full_name"],
            $data["email"],
            $data["phone"],
            $data["department"],
            $data["designation"],
            $data["gender"],
            $data["join_date"],
            $data["status"],
            $data["photo"],
            $data["address"]
        );

        return mysqli_stmt_execute($stmt);
    }

    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET
            employee_code = ?, full_name = ?, email = ?, phone = ?, department = ?, designation = ?,
            gender = ?, join_date = ?, status = ?, photo = ?, address = ?
            WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssi",
            $data["employee_code"],
            $data["full_name"],
            $data["email"],
            $data["phone"],
            $data["department"],
            $data["designation"],
            $data["gender"],
            $data["join_date"],
            $data["status"],
            $data["photo"],
            $data["address"],
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function countAll($search = "", $department = "", $status = "")
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(employee_code LIKE ? OR full_name LIKE ? OR email LIKE ? OR designation LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ssss";
        }

        if (!empty($department)) {
            $where[] = "department = ?";
            $params[] = $department;
            $types .= "s";
        }

        if (!empty($status)) {
            $where[] = "status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $query = "SELECT COUNT(*) AS total FROM {$this->table} {$whereSql}";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return (int) $row["total"];
    }

    public function getAll($search = "", $department = "", $status = "", $sort = "id_desc", $limit = 5, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(employee_code LIKE ? OR full_name LIKE ? OR email LIKE ? OR designation LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ssss";
        }

        if (!empty($department)) {
            $where[] = "department = ?";
            $params[] = $department;
            $types .= "s";
        }

        if (!empty($status)) {
            $where[] = "status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $orderBy = "ORDER BY id DESC";
        if ($sort === "name_asc") $orderBy = "ORDER BY full_name ASC";
        elseif ($sort === "name_desc") $orderBy = "ORDER BY full_name DESC";
        elseif ($sort === "department_asc") $orderBy = "ORDER BY department ASC";
        elseif ($sort === "department_desc") $orderBy = "ORDER BY department DESC";
        elseif ($sort === "join_date_asc") $orderBy = "ORDER BY join_date ASC";
        elseif ($sort === "join_date_desc") $orderBy = "ORDER BY join_date DESC";
        elseif ($sort === "id_asc") $orderBy = "ORDER BY id ASC";

        $query = "SELECT * FROM {$this->table} {$whereSql} {$orderBy} LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!empty($params)) {
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = $row;
        }

        return $employees;
    }

    public function countByStatus($status)
    {
        $query = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $status);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return (int) $row["total"];
    }

    public function getDepartments()
    {
        $query = "SELECT DISTINCT department FROM {$this->table} ORDER BY department ASC";
        $result = mysqli_query($this->conn, $query);

        $departments = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $departments[] = $row["department"];
            }
        }

        return $departments;
    }
}