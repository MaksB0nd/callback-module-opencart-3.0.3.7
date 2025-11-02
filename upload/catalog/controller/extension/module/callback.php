<?php

class ControllerExtensionModuleCallback extends Controller {
    public function send() {
        $this->load->language('extension/module/callback');

        $json = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $name = trim($this->request->post['name'] ?? '');
            $telephone = trim($this->request->post['telephone'] ?? '');
            $comment = trim($this->request->post['comment'] ?? '');
            
            if (mb_strlen($name) < 2 || mb_strlen($name) > 32) {
                $json['error'] = $this->language->get('error_name');
            }
            
            elseif (!preg_match('/^[0-9]{3,}$/', $telephone)) {
                $json['error'] = $this->language->get('error_telephone');
            }
            
            elseif (mb_strlen($comment) > 256) {
                $json['error'] = $this->language->get('error_comment');
            }
            else {
                $this->load->model('extension/module/callback');

                $callback_id = $this->model_extension_module_callback->addCallback([
                    'name' => $name,
                    'telephone' => $telephone,
                    'client_comment' => $comment
                ]);

                $json['success'] = $this->language->get('text_success_front');
                $json['callback_id'] = $callback_id;
            }
        } else {
            $json['error'] = $this->language->get('error_method');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}