// -----------------------------------------------
    // METHOD 6: Add new streak record
    // -----------------------------------------------
    public function addStreak($userID, $habitID) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_AddStreak(?, ?, ?)"
        );
        $stmt->bind_param("iis", $userID, $habitID, $today);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 7: Edit streak record
    // -----------------------------------------------
    public function editStreak(
        $streakID, $currentStreak,
        $longestStreak, $isActive
    ) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_EditStreak(?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiiis",
            $streakID,
            $currentStreak,
            $longestStreak,
            $isActive,
            $today
        );
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 8: Delete streak record
    // -----------------------------------------------
    public function deleteStreak($streakID) {
        $stmt = $this->conn->prepare(
            "CALL sp_DeleteStreak(?)"
        );
        $stmt->bind_param("i", $streakID);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 9: Find streak by ID
    // -----------------------------------------------
    public function findStreakById($streakID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FindStreakById(?)"
        );
        $stmt->bind_param("i", $streakID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 10: Filter streaks by user
    // -----------------------------------------------
    public function filterStreakByUser($userID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreakByUser(?)"
        );
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 11: Filter streaks by habit
    // -----------------------------------------------
    public function filterStreakByHabit($habitID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreakByHabit(?)"
        );
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 12: Validate inputs
    // -----------------------------------------------
    public function validateStreak(
        $userID, $habitID,
        $currentStreak, $longestStreak
    ) {
        $errors = [];

        if (empty($userID) || !is_numeric($userID)) {
            $errors[] = "UserID must be a valid number";
        }
        if (empty($habitID) || !is_numeric($habitID)) {
            $errors[] = "HabitID must be a valid number";
        }
        if ($currentStreak < 0) {
            $errors[] = "Current streak cannot be negative";
        }
        if ($longestStreak < 0) {
            $errors[] = "Longest streak cannot be negative";
        }
        if ($currentStreak > $longestStreak) {
            $errors[] = "Current streak cannot exceed longest streak";
        }

        return $errors;
    }