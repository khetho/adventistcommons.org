<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class User extends CI_Controller
{
    public $data = [];

    public $skills = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library([ "ion_auth", "form_validation", "twig" ]);
        $this->load->helper([ "url", "language" ]);
        $this->form_validation->set_error_delimiters($this->config->item("error_start_delimiter", "ion_auth"), $this->config->item("error_end_delimiter", "ion_auth"));
        $this->lang->load("auth");
        $user = $this->ion_auth->user()->row();
        if ($user) {
            $this->user = $user;
            $this->twig->addGlobal("user", $user);
        }


        $skills = $this->db->select("name")
            ->from("skills")
            ->get();

        foreach ($skills->result() as $row) {
            array_push($this->skills, $row->name);
        }
    }

    public $breadcrumbs = [
        [
            "label" => "Users",
            "url" => "/user/list",
        ],
    ];

    public function list()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }

        $this->data["users"] = $this->ion_auth->users()->result();
        foreach ($this->data["users"] as $k => $user) {
            $this->data["users"][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
        }
        $users = $this->data['users'];
        foreach ($users as $key => $user) {
            $users[$key] = (array) $users[$key];
            $users[$key]['md5email'] = md5(strtolower(trim($user->email)));
        }

        $data = [
            "users" => $users,
        ];

        $this->breadcrumbs[] = [ "label" => "List" ];
        $this->twig->addGlobal("title", "Users");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/auth/list", $data);
    }

    public function edit($user_id = null)
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }

        $this->load->model("project_model");

        $user = $this->ion_auth->user($user_id)->row();
        $data = [
            "edit_user" => $user,
            "skills" => array_merge($this->ion_auth->get_user_skills($user_id), $this->skills),
            "permission_groups" => $this->ion_auth->groups()->result_array(),
            "user_group_id" => $this->ion_auth->get_users_groups($user_id)->row()->id,
            "membership" => $this->project_model->getMembershipByUserId($user_id),
            "is_admin_edit" => true,
        ];

        $this->breadcrumbs[] = [
            "label" => "Edit",
        ];

        $this->breadcrumbs[] = [
            "label" => $data["edit_user"]->first_name . " " . $data["edit_user"]->last_name,
        ];

        $this->twig->addGlobal("title", "Edit User");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/account", $data);
    }

    public function search($project_id, $query)
    {
        $this->load->model("project_model");
        $query = urldecode($query);
        if (! $this->project_model->isManager($this->ion_auth->user()->row()->id, $project_id) && ! $this->ion_auth->is_admin()) {
            show_404();
        }
        
        $users = $this->db->select("*")
            ->from("users")
            ->like("concat(first_name, ' ', last_name)", $query)
            ->or_like("email", $query)
            ->limit(10)
            ->get()
            ->result_array();
        if (! $users && filter_var($query, FILTER_VALIDATE_EMAIL)) {
            $users[] = [
                "heading" => $query,
                "email" => $query,
                "invite_only" => true,
            ];
        }
        $users = array_map(function ($user) {
            if (! isset($user["invite_only"])) {
                $user["heading"] = $user["first_name"] . " " . $user["last_name"];
            }
            $user["avatar"] = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($user["email"]))) . "?s=72&d=mp";
            return $user;
        }, $users);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($users));
    }

    /**
     * Log the user in
     */
    public function login()
    {
        if ($this->ion_auth->logged_in()) {
            redirect('/projects', 'refresh');
        }

        $this->data['title'] = $this->lang->line('login_heading');

        // validate form input
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

        if ($this->form_validation->run() === true) {
            // check to see if the user is logging in
            // check for "remember me"
            $remember = (bool)$this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                //if the login is successful
                //redirect them back to the home page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('/projects', 'refresh');
            } else {
                // if the login was un-successful
                // redirect them back to the login page
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        } else {
            // the user is not logging in so display the login page
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = [
                'name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            ];

            $this->data['password'] = [
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
            ];
            $this->twig->addGlobal("title", "Login");
            $this->twig->display("twigs/auth/login", $this->data);
        }
    }

    public function logout()
    {
        $this->ion_auth->logout();
        redirect('/', 'refresh');
    }

    public function forgot_password()
    {
        $this->form_validation->set_rules("identity", $this->lang->line("forgot_password_validation_email_label"), "required|valid_email");

        if ($this->form_validation->run() === false) {
            $this->twig->addGlobal("title", "Forgot password");
            $this->twig->addGlobal("type", $this->config->item("identity", "ion_auth"));
            $this->twig->addGlobal("message", validation_errors());
            $this->twig->display("twigs/auth/forgot_password", $this->data);
        } else {
            $user = $this->ion_auth->where("email", $this->input->post("identity"))->users()->row();

            if (empty($user)) {
                $this->ion_auth->set_error("forgot_password_email_not_found");
                $this->session->set_flashdata("message", $this->ion_auth->errors());
                redirect("/forgot_password", "refresh");
            }

            $data = $this->ion_auth->forgotten_password($user->email);

            if ($data) {
                $template_data = [
                    "user" => $user->first_name,
                    "link" => base_url() . "user/reset_password/" . $data["forgotten_password_code"],
                ];

                $this->twig->addGlobal("heading", "Password reset instructions");
                $this->twig->addGlobal("base_url", base_url());
                $content = $this->twig->render("twigs/email/forgot_password", $template_data);
                echo $content;
                die;
                $this->email->from("info@adventistcommons.org", "Adventist Commons");
                $this->email->to($user->email);
                $this->email->message($content);
                $this->email->subject("Password reset instructions");
                $this->email->send();

                $this->session->set_flashdata("message", $this->ion_auth->messages());
                redirect("/login", "refresh");
            } else {
                $this->session->set_flashdata("message", $this->ion_auth->errors());
                redirect("/forgot_password", "refresh");
            }
        }
    }

    public function reset_password($code = null)
    {
        if (! $code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            $this->form_validation->set_rules("new", $this->lang->line("reset_password_validation_new_password_label"), "required|min_length[" . $this->config->item("min_password_length", "ion_auth") . "]|matches[new_confirm]");
            $this->form_validation->set_rules("new_confirm", $this->lang->line("reset_password_validation_new_password_confirm_label"), "required");

            if ($this->form_validation->run() === false) {
                $this->data["message"] = (validation_errors()) ? validation_errors() : $this->session->flashdata("message");
                $data["user_id"] = $user->id;
                $this->data["csrf"] = $this->_get_csrf_nonce();
                $this->data["code"] = $code;
                $this->template->set("title", "Reset password");
                $this->template->load("utility_template", "auth/reset_password", $this->data);
            } else {
                $identity = $user->{ $this->config->item("identity", "ion_auth") };
                if ($this->_valid_csrf_nonce() === false || $user->id != $this->input->post("user_id")) {
                    $this->ion_auth->clear_forgotten_password_code($identity);
                    show_error($this->lang->line("error_csrf"));
                } else {
                    $change = $this->ion_auth->reset_password($identity, $this->input->post("new"));
                    if ($change) {
                        $this->session->set_flashdata("message", $this->ion_auth->messages());
                        redirect("/login", "refresh");
                    } else {
                        $this->session->set_flashdata("message", $this->ion_auth->errors());
                        redirect("/user/reset_password/" . $code, "refresh");
                    }
                }
            }
        } else {
            $this->session->set_flashdata("message", $this->ion_auth->errors());
            redirect("/forgot_password", "refresh");
        }
    }

    public function activate($id, $code = false)
    {
        $activation = false;

        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } elseif ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            // redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            // redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("/forgot_password", 'refresh');
        }
    }

    public function deactivate($id = null)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            // redirect them to the home page because they must be an administrator to view this
            show_error('You must be an administrator to view this page.');
        }

        $id = (int)$id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

        if ($this->form_validation->run() === false) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === false || $id != $this->input->post('id')) {
                    show_error($this->lang->line('error_csrf'));
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            // redirect them back to the auth page
            redirect('auth', 'refresh');
        }
    }

    public function register()
    {
        $this->data['title'] = $this->lang->line('create_user_heading');

        if ($this->ion_auth->logged_in()) {
            redirect('/', 'refresh');
        }

        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['identity_column'] = $identity_column;

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
        if ($identity_column !== 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
        } else {
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() === true) {
            $email = strtolower($this->input->post('email'));
            $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
            $password = $this->input->post('password');

            $additional_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'product_notify' => $this->input->post('product_notify') ?? false,
            ];
        }
        if ($this->form_validation->run() === true && $this->ion_auth->register($identity, $password, $email, $additional_data)) {
            if ($this->ion_auth->login($identity, $password)) {
                $user = $this->ion_auth->user()->row();
                
                $invites = $this->db->select("*")
                    ->from("project_members")
                    ->where("invite_email", $user->email)
                    ->get()
                    ->result_array();
                
                foreach ($invites as $invite) {
                    $invite_data = [
                        "user_id" => $user->id,
                        "invite_email" => null,
                        "type" => $invite["type"],
                    ];
                    $this->db->where("id", $invite["id"]);
                    $this->db->update("project_members", $invite_data);
                }
                redirect("/user/register_profile", "refresh");
            }
        } else {
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data["post"] = $this->input->post();
            $this->twig->addGlobal("title", "Register");
            $this->twig->display("twigs/auth/register", $this->data);
        }
    }

    public function register_profile()
    {
        if (! $this->ion_auth->logged_in()) {
            show_404();
        }

        $data = [
            "edit_user" => $this->user,
            "skills" => $this->skills,
        ];

			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'product_notify' => $this->input->post('product_notify') ?? false,
			];
		}
		if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $additional_data))
		{
			if ($this->ion_auth->login($identity, $password))
			{
				$user = $this->ion_auth->user()->row();
				
				$invites = $this->db->select( "*" )
					->from( "project_members" )
					->where( "invite_email", $user->email )
					->get()
					->result_array();
				
				foreach( $invites as $invite ) {
					$invite_data = [
						"user_id" => $user->id,
						"invite_email" => null,
						"type" => $invite["type"],
					];
					$this->db->where( "id", $invite["id"] );
					$this->db->update( "project_members", $invite_data );
				}
				redirect( "/user/register_profile", "refresh" );
			}
		}
		else
		{
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data["post"] = $this->input->post();
			$this->twig->addGlobal("title", "Register");
			$this->twig->display("twigs/auth/register",$this->data);
		}
	}

    public function register_profile_save()
    {
        $this->output->set_content_type("application/json");

        $this->form_validation->set_rules("mother_language_id", "Mother language", "required");

        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
        } else {
            $post_data = $this->input->post();
            $user_id = $this->ion_auth->user()->row()->id;
            $data = [
                "bio" => $post_data["bio"],
                "location" => $post_data["location"],
                "mother_language_id" => $post_data["mother_language_id"],
                "skills" => $post_data["skills"] ?? [],
                "languages" => $post_data["languages"] ?? [],
                "pro_translator" => $post_data[ "pro_translator" ] ?? false,
            ];
            $this->ion_auth->update_user_custom($user_id, $data);

            $this->output->set_output(json_encode([ "redirect" => "/projects" ]));
        }
    }

    public function account()
    {
        $data = [
            "skills" => $this->skills,
            "edit_user" => $this->user,
        ];
        $breadcrumbs = [
            [
                "label" => "Account",
                "url" => "/account",
            ],
            [
                "label" => "Settings",
            ]
        ];
        $this->twig->addGlobal("title", "Account Settings");
        $this->twig->addGlobal("breadcrumbs", $breadcrumbs);
        $this->twig->display("twigs/account", $data);
    }

    public function save_account()
    {
        $this->output->set_content_type("application/json");

        $this->form_validation->set_rules("email", "Email", "required|valid_email");
        $this->form_validation->set_rules("first_name", "First name", "required");
        $this->form_validation->set_rules("last_name", "Last name", "required");
        $this->form_validation->set_rules("mother_language_id", "Mother language", "required");

        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
        } else {
            $data = $this->input->post();
            $user_id = $this->ion_auth->is_admin() ? $data["id"] : $this->ion_auth->user()->row()->id;
            $this->ion_auth->update_user_custom($user_id, $data);
            $this->output->set_output(json_encode([ "success" => "Account info updated" ]));
        }
    }

    public function save_password()
    {
        $this->output->set_content_type("application/json");

        if (! $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules("current_password", "Current password", "required");
        }
        $this->form_validation->set_rules("new_password", "New password", "required|min_length[" . $this->config->item("min_password_length", "ion_auth") . "]|matches[confirm_password]");
        $this->form_validation->set_rules("confirm_password", "Confirm password", "required");

        $user = $user_id = $this->ion_auth->is_admin() ? $this->ion_auth->user($this->input->post("id"))->row() : $this->ion_auth->user()->row();

        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
        } else {
            if ($this->ion_auth->is_admin()) {
                $change = $this->ion_auth->_set_password_db($user->email, $this->input->post("new_password"));
            } else {
                $change = $this->ion_auth->change_password($user->email, $this->input->post("current_password"), $this->input->post("new_password"));
            }

            if (! $change) {
                $this->output->set_output(json_encode([ "error" => $this->ion_auth->errors() ]));
            } else {
                $this->output->set_output(json_encode([ "success" => "Password updated successfully" ]));
            }
        }
    }

    public function save_permissions()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }

        $this->output->set_content_type("application/json");

        $this->form_validation->set_rules("group_id", "Permission level", "required");
        $this->form_validation->set_rules("user_id", "User ID", "required");

        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
        } else {
            $data = $this->input->post();

            $this->db->where("user_id", $data["user_id"])
                ->delete("users_groups");

            $this->db->insert("users_groups", $data);
            $id = $this->db->insert_id();
            $this->output->set_output(json_encode([ "success" => "Permissions updated successfully" ]));
        }
    }
    
    public function delete()
    {
        if (! $this->ion_auth->logged_in()) {
            show_404();
        }
        
        $this->output->set_content_type("application/json");
        $data = [
            "first_name" => "Inactive",
            "last_name" => "user",
            "ip_address" => null,
            "username" => null,
            "password" => null,
            "email" => null,
            "last_login" => null,
            "active" => false,
            "location" => null,
            "bio" => null,
            "mother_language_id" => null,
            "skills" => null,
            "product_notify" => null,
            "pro_translator" => null,
        ];
        
        $this->db->where("id", $this->ion_auth->user()->row()->id);
        $this->db->update("users", $data);
        $this->ion_auth->logout();
        $this->session->set_flashdata("message", "Your account has been successfully deleted");
        $this->output->set_output(json_encode([ "redirect" => "/" ]));
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return [$key => $value];
    }

    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return true;
        }
        return false;
    }

    public function _render_page($view, $data = null, $returnhtml = false)//I think this makes more sense
    {
        $viewdata = (empty($data)) ? $this->data : $data;

        $view_html = $this->load->view($view, $viewdata, $returnhtml);

        // This will return html on 3rd argument being true
        if ($returnhtml) {
            return $view_html;
        }
    }
}
