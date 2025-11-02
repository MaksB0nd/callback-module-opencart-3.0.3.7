<?php

class ControllerExtensionModuleCallback extends Controller {
    private $error = [];

    protected function renderLicenseForm() {
        $this->load->language('extension/module/callback');

        $data = [];
        
        $data['heading_title_license'] = $this->language->get('heading_title_license');

        $data['entry_license_key'] = $this->language->get('entry_license_key');
        $data['entry_domain_name'] = $this->language->get('entry_domain_name');
        $data['help_domain_name'] = $this->language->get('help_domain_name');
        $data['button_activate'] = $this->language->get('button_activate');

        $data['error_license'] = $this->error['warning'] ?? '';

        $data['module_callback_license_key'] = $this->config->get('module_callback_license_key');
        $data['module_callback_domain_name'] = $this->config->get('module_callback_domain_name');

        $data['link'] = $this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/callback/license', $data));
    }

    public function index() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');

        $this->document->setTitle($this->language->get('heading_title_requests'));

        $this->getList();
    }

    public function add() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');

        $this->document->setTitle($this->language->get('heading_title_requests'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $this->model_extension_module_callback->addCallback($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_add');

            $this->response->redirect($this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');

        $this->document->setTitle($this->language->get('heading_title_requests'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_callback->editCallback($this->request->get['callback_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_edit');

            $this->response->redirect($this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');

        $this->document->setTitle($this->language->get('heading_title_requests'));

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $callback_id) {
                $this->model_extension_module_callback->deleteCallback($callback_id);
            }

            $this->session->data['success'] = $this->language->get('text_success_delete');
            
            $this->response->redirect($this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getList();
    }

    protected function getList() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');

        $current_lang = $this->config->get('config_admin_language');

        $status_keys = [
            'new',
            'processed'
        ];

        $config = (array)$this->config->get('module_callback_statuses');
    
        $data['status_options'] = [];

        foreach ($status_keys as $key) {
            $text = $config[$key][$current_lang] ?? $this->language->get('status_' . $key);

            $color = $config[$key]['color'] ?? '#e6e6e6';

            $data['status_options'][] = [
                'value' => $key,
                'text'  => $text,
                'color' => $color
            ];
        }

        if (!empty($config['custom']) && is_array($config['custom'])) {
            foreach ($config['custom'] as $i => $block) {
                $key  = 'custom_' . $i;
                $text = $block[$current_lang] ?? '';
                $color = $block['color'] ?? '#ffffff';

                if (trim($text)) {
                    $data['status_options'][] = [
                        'value' => $key,
                        'text'  => $text,
                        'color' => $color
                    ];
                }
            }
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
            $url .= '&filter_name=' . urlencode(html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_telephone'])) {
            $filter_telephone = $this->request->get['filter_telephone'];
            $url .= '&filter_telephone=' . urlencode(html_entity_decode($filter_telephone, ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_telephone = '';
        }

        if (isset($this->request->get['filter_client_comment'])) {
            $filter_client_comment = $this->request->get['filter_client_comment'];
            $url .= '&filter_client_comment=' . urlencode(html_entity_decode($filter_client_comment, ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_client_comment = '';
        }

        if (isset($this->request->get['filter_admin_comment'])) {
            $filter_admin_comment = $this->request->get['filter_admin_comment'];
            $url .= '&filter_admin_comment=' . urlencode(html_entity_decode($filter_admin_comment, ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_admin_comment = '';
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
            $url .= '&filter_date_start=' . $filter_date_start;
        } else {
            $filter_date_start = '';
        }

        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
            $url .= '&filter_date_end=' . $filter_date_end;
        } else {
            $filter_date_end = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
            $url .= '&filter_status=' . urlencode($filter_status);
        } else {
            $filter_status = '';
        }

        $data['filter_name'] = $filter_name;
        $data['filter_telephone'] = $filter_telephone;
        $data['filter_client_comment'] = $filter_client_comment;
        $data['filter_admin_comment'] = $filter_admin_comment;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_status'] = $filter_status;

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = 15;

        $filter_data = [
            'filter_name' => $filter_name,
            'filter_telephone' => $filter_telephone,
            'filter_client_comment' => $filter_client_comment,
            'filter_admin_comment' => $filter_admin_comment,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_status' => $filter_status,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ];

        $results = $this->model_extension_module_callback->getCallbacks($filter_data);

        $data['callbacks'] = [];
        
        foreach ($results as $r) {
            $status_text = $r['status'];

            $status_color = '#e6e6e6';

            foreach ($data['status_options'] as $opt) {
                if ($opt['value'] === $r['status']) {
                    $status_text  = $opt['text'];
                    $status_color = $opt['color'];
                    break;
                }
            }

            $data['callbacks'][] = [
                'callback_id' => $r['callback_id'],
                'name' => $r['name'],
                'telephone' => $r['telephone'],
                'client_comment' => $r['client_comment'],
                'date_added' => $r['date_added'],
                'date_edit' => $r['date_edit'],
                'admin_comment' => $r['admin_comment'],
                'status_text' => $status_text,
                'status_color'=> $status_color,
                'edit' => $this->url->link('extension/module/callback/edit', 'user_token='.$this->session->data['user_token'].'&callback_id='.$r['callback_id'] . $url, true),
                'selected' => isset($this->request->post['selected']) && in_array($r['callback_id'],$this->request->post['selected'])
            ];
        }

        $data['status_options'] = $data['status_options'];

        $callback_total = $this->model_extension_module_callback->getTotalCallbacks($filter_data);

        $pagination = new Pagination();
        $pagination->total = $callback_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), $callback_total ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($callback_total - $limit)) ? $callback_total : ((($page - 1) * $limit) + $limit), $callback_total, ceil($callback_total / $limit));

        $data['add'] = $this->url->link('extension/module/callback/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['delete'] = $this->url->link('extension/module/callback/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['setting'] = $this->url->link('extension/module/callback/setting','user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['user_token'] = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('extension/module/callback/callback_list',$data));
    }

    protected function getForm() {
        $this->load->language('extension/module/callback');

        $this->load->model('extension/module/callback');
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');

        $this->document->setTitle($this->language->get('heading_title_requests'));

        $current_lang = $this->config->get('config_admin_language');

        $status_keys = [
            'new',
            'processed'
        ];

        $config = (array)$this->config->get('module_callback_statuses');

        $data['status_options'] = [];

        foreach ($status_keys as $key) {
            $text = $config[$key][$current_lang] ?? $this->language->get('status_' . $key);

            $data['status_options'][] = [
                'value' => $key,
                'text'  => $text
            ];
        }

        if (!empty($config['custom']) && is_array($config['custom'])) {
            foreach ($config['custom'] as $i => $block) {
                $key  = 'custom_' . $i;
                $text = $block[$current_lang] ?? '';

                if (trim($text)) {
                    $data['status_options'][] = [
                        'value' => $key,
                        'text'  => $text
                    ];
                }
            }
        }

        $url = '';

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('heading_title_requests'),
                'href' => $this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'] . $url, true)
            ]
        ];

        if ($this->request->get['route'] === 'extension/module/callback/add') {
            $data['action'] = $this->url->link('extension/module/callback/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } elseif (isset($this->request->get['callback_id'])) {
            $data['action'] = $this->url->link('extension/module/callback/edit', 'user_token=' . $this->session->data['user_token'] . '&callback_id=' . (int)$this->request->get['callback_id'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/callback/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['callback_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
            $callback_info = $this->model_extension_module_callback->getCallback($this->request->get['callback_id']);
        } else {
            $callback_info = [];
        }

        $data['callback_id'] = $this->request->get['callback_id'] ?? '';
        $data['name'] = $this->request->post['name'] ?? ($callback_info['name'] ?? '');
        $data['telephone'] = $this->request->post['telephone'] ?? ($callback_info['telephone'] ?? '');
        $data['client_comment'] = $this->request->post['client_comment'] ?? ($callback_info['client_comment'] ?? '');
        $data['status'] = $this->request->post['status'] ?? ($callback_info['status'] ?? 'new');
        $data['admin_comment'] = $this->request->post['admin_comment']  ?? ($callback_info['admin_comment'] ?? '');
        $data['date_added'] = $callback_info['date_added'] ?? '';
        $data['date_edit'] = $callback_info['date_edit']  ?? '';

        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/callback/callback_form', $data));
    }

    public function setting() {
        $this->load->language('extension/module/callback');

        $this->load->model('setting/setting');
        $this->load->model('localisation/language');

        $this->document->setTitle($this->language->get('heading_title_setting'));

        $data['languages'] = $this->model_localisation_language->getLanguages();
        $current_lang = $this->config->get('config_admin_language');

        $status_keys = [
            'new',
            'processed'
        ];

        $config = (array)$this->config->get('module_callback_statuses');

        $data['default_statuses'] = [];
        $data['default_status_labels'] = [];
        $data['default_status_colors'] = [];

        foreach ($status_keys as $key) {
            $data['default_statuses'][$key] = [];

            foreach ($data['languages'] as $lang) {
                $code = $lang['code'];

                $data['default_statuses'][$key][$code] = $config[$key][$code] ?? $this->language->get('status_' . $key);
            }

            $data['default_status_labels'][$key] = $data['default_statuses'][$key][$current_lang] ?? reset($data['default_statuses'][$key]);

            $data['default_status_colors'][$key] = $config[$key]['color'] ?? '#eeeeee';
        }

        $data['custom_statuses'] = [];

        if (!empty($config['custom']) && is_array($config['custom'])) {
            foreach ($config['custom'] as $block) {
                $row = ['color' => $block['color'] ?? '#ffffff'];

                foreach ($data['languages'] as $lang) {
                    $code = $lang['code'];
                    $row[$code] = $block[$code] ?? '';
                }

                if (array_filter($row)) {
                    $data['custom_statuses'][] = $row;
                }
            }
        }

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validate()) {
            $posted = $this->request->post['statuses'] ?? [];
            $save = [];

            foreach ($status_keys as $key) {
                foreach ($data['languages'] as $lang) {
                    $code = $lang['code'];
                    $save[$key][$code] = $posted[$key][$code]
                        ?? $this->language->get('status_' . $key);
                }
                $save[$key]['color'] = $posted[$key]['color'] ?? $data['default_status_colors'][$key];
            }

            $save['custom'] = [];
            if (!empty($posted['custom']) && is_array($posted['custom'])) {
                foreach ($posted['custom'] as $block) {
                    $row = ['color' => $block['color'] ?? ''];
                    foreach ($data['languages'] as $lang) {
                        $code = $lang['code'];
                        $row[$code] = $block[$code] ?? '';
                    }
                    if (array_filter($row)) {
                        $save['custom'][] = $row;
                    }
                }
            }

            $this->model_setting_setting->editSetting('module_callback', [
                'module_callback_status' => $this->request->post['module_callback_status'] ?? 1,
                'module_callback_statuses' => $save
            ]);

            $this->session->data['success'] = $this->language->get('text_success_setting');
            $this->response->redirect($this->url->link('extension/module/callback/setting', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['module_callback_status'] = $this->config->get('module_callback_status');
        $data['action'] = $this->url->link('extension/module/callback/setting', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['requests_url'] = $this->url->link('extension/module/callback', 'user_token=' . $this->session->data['user_token'], true);
        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['success'] = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/callback/callback_setting', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/callback')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/callback')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/callback')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/module/callback');
        $this->model_extension_module_callback->install();
    }

    public function uninstall() {
        $this->load->model('extension/module/callback');
        $this->model_extension_module_callback->uninstall();
    }
}