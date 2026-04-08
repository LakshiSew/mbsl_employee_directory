<?php

class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmail($email)
    {
        $query = "SELECT id, full_name, email, password, role, profile_picture, created_at
                  FROM {$this->table}
                  WHERE email = ?
                  LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function emailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            $query = "SELECT id FROM {$this->table} WHERE email = ? AND id != ? LIMIT 1";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $email, $excludeId);
        } else {
            $query = "SELECT id FROM {$this->table} WHERE email = ? LIMIT 1";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        return mysqli_stmt_num_rows($stmt) > 0;
    }

    public function create($full_name, $email, $password, $role, $profile_picture = null)
    {
        $query = "INSERT INTO {$this->table} (full_name, email, password, role, profile_picture)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $password, $role, $profile_picture);

        return mysqli_stmt_execute($stmt);
    }

    public function getById($id)
    {
        $query = "SELECT id, full_name, email, role, profile_picture, created_at
                  FROM {$this->table}
                  WHERE id = ?
                  LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function update($id, $full_name, $email, $role, $profile_picture = null, $password = null)
    {
        if ($password !== null) {
            $query = "UPDATE {$this->table}
                      SET full_name = ?, email = ?, role = ?, password = ?, profile_picture = ?
                      WHERE id = ?";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $full_name, $email, $role, $password, $profile_picture, $id);
        } else {
            $query = "UPDATE {$this->table}
                      SET full_name = ?, email = ?, role = ?, profile_picture = ?
                      WHERE id = ?";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $role, $profile_picture, $id);
        }

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    public function countAll($search = "", $role = "")
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(full_name LIKE ? OR email LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ss";
        }

        if (!empty($role)) {
            $where[] = "role = ?";
            $params[] = $role;
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

    public function getAll($search = "", $role = "", $sort = "id_desc", $limit = 5, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(full_name LIKE ? OR email LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ss";
        }

        if (!empty($role)) {
            $where[] = "role = ?";
            $params[] = $role;
            $types .= "s";
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $orderBy = "ORDER BY id DESC";

        if ($sort === "name_asc") $orderBy = "ORDER BY full_name ASC";
        elseif ($sort === "name_desc") $orderBy = "ORDER BY full_name DESC";
        elseif ($sort === "email_asc") $orderBy = "ORDER BY email ASC";
        elseif ($sort === "email_desc") $orderBy = "ORDER BY email DESC";
        elseif ($sort === "role_asc") $orderBy = "ORDER BY role ASC";
        elseif ($sort === "role_desc") $orderBy = "ORDER BY role DESC";
        elseif ($sort === "id_asc") $orderBy = "ORDER BY id ASC";

        $query = "SELECT id, full_name, email, role, profile_picture, created_at
                  FROM {$this->table}
                  {$whereSql}
                  {$orderBy}
                  LIMIT ? OFFSET ?";

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

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        return $users;
    }

    public function countByRole($role)
    {
        $query = "SELECT COUNT(*) AS total FROM {$this->table} WHERE role = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return (int) $row["total"];
    }

    public function updateProfile($id, $full_name, $email, $profile_picture = null, $password = null)
{
    if ($password !== null) {
        $query = "UPDATE {$this->table}
                  SET full_name = ?, email = ?, password = ?, profile_picture = ?
                  WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $password, $profile_picture, $id);
    } else {
        $query = "UPDATE {$this->table}
                  SET full_name = ?, email = ?, profile_picture = ?
                  WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $full_name, $email, $profile_picture, $id);
    }

    return mysqli_stmt_execute($stmt);
}

public function getProfileById($id)
{
    $query = "SELECT id, full_name, email, role, profile_picture, created_at
              FROM {$this->table}
              WHERE id = ?
              LIMIT 1";

    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}
}