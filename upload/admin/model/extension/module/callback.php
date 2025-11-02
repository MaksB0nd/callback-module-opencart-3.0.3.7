<?php

class ModelExtensionModuleCallback extends Model {
    public function getCallbacks($data = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "callback_request WHERE 1";
        
        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (!empty($data['filter_telephone'])) {
            $sql .= " AND telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }

        if (!empty($data['filter_client_comment'])) {
            $sql .= " AND client_comment LIKE '%" . $this->db->escape($data['filter_client_comment']) . "%'";
        }

        if (!empty($data['filter_admin_comment'])) {
            $sql .= " AND admin_comment LIKE '%" . $this->db->escape($data['filter_admin_comment']) . "%'";
        }

        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        if ($data['filter_status'] !== '' && $data['filter_status'] !== null) {
            $sql .= " AND status = '" . $this->db->escape($data['filter_status']) . "'";
        }

        $sql .= " ORDER BY callback_id DESC";

        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalCallbacks($data = []) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "callback_request WHERE 1";
        
        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_telephone'])) {
            $sql .= " AND telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }

        if (!empty($data['filter_client_comment'])) {
            $sql .= " AND client_comment LIKE '%" . $this->db->escape($data['filter_client_comment']) . "%'";
        }

        if (!empty($data['filter_admin_comment'])) {
            $sql .= " AND admin_comment LIKE '%" . $this->db->escape($data['filter_admin_comment']) . "%'";
        }

        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
        }
        
        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        if ($data['filter_status'] !== '' && $data['filter_status'] !== null) {
            $sql .= " AND status = '" . $this->db->escape($data['filter_status']) . "'";
        }

        $result = $this->db->query($sql);
        return $result->row['total'];
    }

    public function getCallback($callback_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "callback_request WHERE callback_id = '" . (int)$callback_id . "'";
        return $this->db->query($sql)->row;
    }

    public function addCallback(array $data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "callback_request` SET 
            name           = '" . $this->db->escape($data['name']) . "',
            telephone      = '" . $this->db->escape($data['telephone']) . "',
            client_comment = '" . $this->db->escape($data['client_comment']) . "',
            status         = '" . $this->db->escape($data['status']) . "',
            admin_comment  = '" . $this->db->escape($data['admin_comment']) . "',
            date_added     = NOW()");
    }

    public function editCallback($callback_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "callback_request SET 
            name            = '" . $this->db->escape($data['name']) . "', 
            telephone       = '" . $this->db->escape($data['telephone']) . "', 
            status          = '" . $this->db->escape($data['status']) . "', 
            admin_comment   = '" . $this->db->escape($data['admin_comment']) . "', 
            client_comment  = '" . $this->db->escape($data['client_comment']) . "', 
            date_edit       = NOW() WHERE callback_id = '" . (int)$callback_id . "'");
    }

    public function deleteCallback($callback_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "callback_request WHERE callback_id = '" . (int)$callback_id . "'");
    }

    public function install() {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "callback_request` (" .
                "`callback_id`      INT(11) NOT NULL AUTO_INCREMENT," .
                "`name`             VARCHAR(64)      NOT NULL," .
                "`telephone`        VARCHAR(32)      NOT NULL," .
                "`client_comment`   VARCHAR(256)     DEFAULT NULL," .
                "`status`           VARCHAR(32)      NOT NULL DEFAULT 'new'," .
                "`admin_comment`    TEXT             DEFAULT NULL," .
                "`date_added`       DATETIME         NOT NULL," .
                "`date_edit`        DATETIME         DEFAULT NULL," .
                "PRIMARY KEY (`callback_id`)" .
            ") ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "callback_request`");
    }
}