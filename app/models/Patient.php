<?php

require_once '../app/core/Database.php';

class Patient
{
    private $conn;

    public function __construct()
    {
        $db = new Database();

        $this->conn = $db->connect();
    }

    // Create Patient
    public function create($data)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO patients
            (
                name, age, gender, phone, address, user_id)
            VALUES
            (  ?,  ?,  ?,  ?,  ?,  ?  )'
        );

        $stmt->bind_param(
            'sisssi',
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $data['user_id']
        );

        return $stmt->execute();
    }

    // Get Patients By User
    public function getByUser($userId)
    {
        $stmt = $this->conn->prepare(
            'SELECT *
            FROM patients
            WHERE user_id = ?
            ORDER BY id DESC'
        );

        $stmt->bind_param('i', $userId);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update Patient
    public function updatePatient($id, $data)
    {
        $stmt = $this->conn->prepare(
            'UPDATE patients
            SET
                name = ?,
                age = ?,
                gender = ?,
                phone = ?,
                address = ?
            WHERE id = ?'
        );

        $stmt->bind_param(
            'sisssi',
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $id
        );

        return $stmt->execute();
    }

    public function getPatientByUser($id, $userId)
    {
        $stmt = $this->conn->prepare(
            'SELECT *
            FROM patients
            WHERE id = ?
            AND user_id = ?'
        );

        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // Delete Patient
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            'DELETE FROM patients
            WHERE id = ?'
        );

        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }
}