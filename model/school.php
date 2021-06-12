<?php

require_once 'common/DBHandler.php';

class school{
    use DBHandler;
    /*
     * System Users Role Summary
     * */
    public function schoolUsersRoleSummary(){
        return $this->records("SELECT COUNT(role_id) as totalUsers, name, 
                        SUM(role_id) AS total, AVG(role_id) as average FROM role_user JOIN roles 
                        WHERE role_user.role_id=roles.id GROUP BY role_id");
    }

    public function totalUsersPerSchool(){
        $this->records("SELECT school_name, COUNT(*) AS totalUsers FROM schools JOIN users WHERE users.school_id = schools.id  GROUP BY users.school_id");
    }
}

$school = new school();
echo json_encode($school->schoolStatistics());