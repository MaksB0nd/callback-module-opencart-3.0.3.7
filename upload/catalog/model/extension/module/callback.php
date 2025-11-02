<?php

class ModelExtensionModuleCallback extends Model {
    public function addCallback(array $data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "callback_request` SET name = '" . $this->db->escape($data['name']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', client_comment = '" . $this->db->escape($data['client_comment']) . "', status = 'new', date_added = NOW()");

        return (int)$this->db->getLastId();
    }
}