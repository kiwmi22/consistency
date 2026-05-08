/**
 * filterByHabit() — filters logs by HabitID
 * Calls sp_FilterLogsByHabit — Sprint 3
 */
public function filterByHabit($habitID) {
    if (empty($habitID)) return [];
    $habitID = htmlspecialchars(strip_tags($habitID));
    $stmt = $this->db->prepare(
        "CALL sp_FilterLogsByHabit(?)"
    );
    $stmt->execute([$habitID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * filterByStatus() — filters logs by completion status
 * Calls sp_FilterLogsByStatus — Sprint 3
 */
public function filterByStatus($isCompleted) {
    $stmt = $this->db->prepare(
        "CALL sp_FilterLogsByStatus(?)"
    );
    $stmt->execute([$isCompleted]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * filterByUser() — filters logs by UserID for admin
 * Calls sp_FilterLogsByUser — Sprint 3
 */
public function filterByUser($userID) {
    if (empty($userID)) return [];
    $userID = htmlspecialchars(strip_tags($userID));
    $stmt = $this->db->prepare(
        "CALL sp_FilterLogsByUser(?)"
    );
    $stmt->execute([$userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}