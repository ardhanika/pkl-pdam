<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * |==============================================================|
 * | Please DO NOT modify this information :                      |
 * |--------------------------------------------------------------|
 * | Author          : Susantokun
 * | Email           : admin@susantokun.com
 * | Filename        : Auth.php
 * | Instagram       : @susantokun
 * | Blog            : http://www.susantokun.com
 * | Info            : http://info.susantokun.com
 * | Demo            : http://demo.susantokun.com
 * | Youtube         : http://youtube.com/susantokun
 * | File Created    : Thursday, 12th March 2020 10:34:33 am
 * | Last Modified   : Thursday, 12th March 2020 10:57:22 am
 * |==============================================================|
 */

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Auth_model');
    }

    public function profile()
    {
        $data = konfigurasi('Profile', 'Kelola Profile');
        $this->template->load('layouts/template', 'authentication/profile', $data);
    }
    public function update_pengajuan()
    {
        $id = $this->session->userdata('pengguna_id');
        $appointment = array('status_pendaftaran' => 2);
        $this->db->where('id', $id);
        $this->db->update('status_pendaftaran', $appointment);
        $this->template->load('layouts/template', 'member/index');
    }

    public function updateProfile()
    {
        // $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[15]');
        // $this->form_validation->set_rules('first_name', 'Nama Depan', 'trim|required|min_length[2]|max_length[15]');
        // $this->form_validation->set_rules('last_name', 'Nama Belakang', 'trim|required|min_length[2]|max_length[15]');
        // $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[8]|max_length[50]');
        // $this->form_validation->set_rules('phone', 'Telp', 'trim|required|min_length[11]|max_length[12]');

        $id = $this->session->userdata('pengguna_id');
        $data = array(
            // 'username' => $this->input->post('username'),
            'nama' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'handphone' => $this->input->post('handphone'),
            'asal' => $this->input->post('asal'),
            'photo' => $this->input->post('photo'),
        );
        // if ($this->form_validation->run() == true) {
        if (!empty($_FILES['photo']['name'])) {
            $upload = $this->_do_upload();

            //delete file
            $user = $this->Auth_model->get_by_id($this->session->userdata('id'));
            if (file_exists('assets/uploads/images/foto_profil/' . $user->photo) && $user->photo) {
                unlink('assets/uploads/images/foto_profil/' . $user->photo);
            }

            $data['photo'] = $upload;
        }
        $result = $this->Auth_model->update($data, $id);
        if ($result > 0) {
            $this->updateProfil();
            $this->session->set_flashdata('msg', show_succ_msg('Data Profil Berhasil diubah'));
            redirect('auth/profile');
        } else {
            $this->session->set_flashdata('msg', show_err_msg('Data Profile Gagal diubah'));
            redirect('auth/profile');
        }
        // } else {
        //     $this->session->set_flashdata('msg', show_err_msg(validation_errors()));
        //     redirect('auth/profile');
        // }
    }

    public function updatePassword()
    {
        $this->form_validation->set_rules('passLama', 'Password Lama', 'trim|required|min_length[5]|max_length[25]');
        $this->form_validation->set_rules('passBaru', 'Password Baru', 'trim|required|min_length[5]|max_length[25]');
        $this->form_validation->set_rules('passKonf', 'Password Konfirmasi', 'trim|required|min_length[5]|max_length[25]');

        $id = $this->session->userdata('pengguna_id');
        if ($this->form_validation->run() == true) {
            if (password_verify($this->input->post('passLama'), $this->session->userdata('password'))) {
                if ($this->input->post('passBaru') != $this->input->post('passKonf')) {
                    $this->session->set_flashdata('msg', show_err_msg('Password Baru dan Konfirmasi Password harus sama'));
                    redirect('auth/login');
                } else {
                    $data = ['password' => get_hash($this->input->post('passBaru'))];
                    $result = $this->Auth_model->update($data, $id);
                    if ($result > 0) {
                        $this->updateProfil();
                        $this->session->set_flashdata('msg', show_succ_msg('Password Berhasil diubah'));
                        redirect('auth/login');
                    } else {
                        $this->session->set_flashdata('msg', show_err_msg('Password Gagal diubah'));
                        redirect('auth/login');
                    }
                }
            } else {
                $this->session->set_flashdata('msg', show_err_msg('Password Salah'));
                redirect('auth/login');
            }
        } else {
            $this->session->set_flashdata('msg', show_err_msg(validation_errors()));
            redirect('auth/login');
        }
    }

    public function reset_password()
    {
        $this->form_validation->set_rules('passBaru', 'Password', 'trim|required|min_length[5]|matches[passKonf]');
        $this->form_validation->set_rules('passKonf', 'Password', 'trim|required|min_length[5]|matches[passBaru]');

        // $id = $this->session->userdata('pengguna_id');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Reset Password';
            $this->template->load('authentication/layouts/template', 'authentication/reset_password', $data);
        } else {
            // $data = ['password' => get_hash($this->input->post('passBaru'))];
            // $result = $this->Auth_model->update($data, $id);
            // if ($result > 0) {
            //     $this->updateProfil();

            $password = password_hash($this->input->post('passBaru'), PASSWORD_DEFAULT);
            $code = $this->input->post('code');
            
            // print_r($this->input->post('passBaru'));
            // die();
            // $password = get_hash($this->input->post('password'));
            // $code = 3888;
                             

            
            $this->db->set('password', $password);
            $this->db->where('code', $code);
            $this->db->update('pengguna');

            // $this->session->unset_userdata('email');
            $data = ['password' => get_hash($this->input->post('passBaru'))];
            $this->session->set_flashdata('alert', '<p class="box-msg">
        <div class="info-box alert-success">
        <div class="info-box-icon">
        <i class="fa fa-check-circle"></i>
        </div>
        <div class="info-box-content" style="font-size:14">
        <b style="font-size: 20px">SUKSES</b><br>Reset Password Berhasil! Silahkan login kembali</div>
        </div>
        </p>
      ');
            redirect('auth/login');
            // } else {
            //     $this->session->set_flashdata('msg', show_err_msg('Password Gagal diubah'));
            //     redirect('auth/login');
            // }
        }
    }


    private function _do_upload()
    {
        $config['upload_path']          = 'assets/uploads/images/foto_profil/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100; //set max size allowed in Kilobyte
        $config['max_width']            = 1000; // set max width image allowed
        $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000);
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('photo')) {
            $this->session->set_flashdata('msg', $this->upload->display_errors('', ''));
            redirect('auth/profile');
        }
        return $this->upload->data('file_name');
    }

    public function register()
    {
        $data = konfigurasi('Register');
        $this->template->load('authentication/layouts/template', 'authentication/register', $data);
    }

    public function check_register()
    {
        $data = konfigurasi('Register');
        $this->Auth_model->reg();
        $this->session->set_flashdata('alert', '<p class="box-msg">
              <div class="info-box alert-success">
              <div class="info-box-icon">
              <i class="fa fa-check-circle"></i>
              </div>
              <div class="info-box-content" style="font-size:14">
              <b style="font-size: 20px">SUKSES</b><br>Pendaftaran berhasil, silakan login.</div>
              </div>
              </p>
            ');
        redirect('auth/login', 'refresh', $data);
    }

    public function check_account()
    {
        //validasi login
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');

        //ambil data dari database untuk validasi login
        $query = $this->Auth_model->check_account($email, $password);

        if ($query === 1) {
            $this->session->set_flashdata('alert', '<p class="box-msg">
        			<div class="info-box alert-danger">
        			<div class="info-box-icon">
        			<i class="fa fa-warning"></i>
        			</div>
        			<div class="info-box-content" style="font-size:14">
        			<b style="font-size: 20px">GAGAL</b><br>Email yang Anda masukkan tidak terdaftar.</div>
        			</div>
        			</p>
            ');
        } elseif ($query === 2) {
            $this->session->set_flashdata(
                'alert',
                '<p class="box-msg">
              <div class="info-box alert-info">
              <div class="info-box-icon">
              <i class="fa fa-info-circle"></i>
              </div>
              <div class="info-box-content" style="font-size:14">
              <b style="font-size: 20px">GAGAL</b><br>Akun yang Anda masukkan tidak aktif, silakan hubungi Administrator.</div>
              </div>
              </p>'
            );
        } elseif ($query === 3) {
            $this->session->set_flashdata('alert', '<p class="box-msg">
        			<div class="info-box alert-danger">
        			<div class="info-box-icon">
        			<i class="fa fa-warning"></i>
        			</div>
        			<div class="info-box-content" style="font-size:14">
        			<b style="font-size: 20px">GAGAL</b><br>Password yang Anda masukkan salah.</div>
        			</div>
        			</p>
              ');
        } else {
            //membuat session dengan nama userData yang artinya nanti data ini bisa di ambil sesuai dengan data yang login
            $userdata = array(
                'is_login'    => true,
                'pengguna_id'          => $query->pengguna_id,
                'password'    => $query->password,
                'role_id'     => $query->role_id,
                'name'    => $query->nama,
                //   'first_name'  => $query->first_name,
                //   'last_name'   => $query->last_name,
                'email'       => $query->email,
                'handphone'       => $query->handphone,
                'photo'       => $query->photo,
                'last_login'  => $query->last_login,
                'status_pendaftaran'  => $query->status_pendaftaran,
                'pengguna_id'  => $query->pengguna_id,

                //   'created_at'  => $query->created_at,
                //   'updated_at'  => $query->updated_at,
            );
            $this->session->set_userdata($userdata);
            return true;
        }
    }
    public function login()
    {
        $data = konfigurasi('Login');
        //melakukan pengalihan halaman sesuai dengan levelnya
        if ($this->session->userdata('role_id') == "1") {
            redirect('admin/home');
        }
        if ($this->session->userdata('role_id') == "2") {
            redirect('member/home');
        }

        //proses login dan validasi nya
        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[22]');
            $error = $this->check_account();

            if ($this->form_validation->run() && $error === true) {
                $data = $this->Auth_model->check_account($this->input->post('email'), $this->input->post('password'));

                //jika bernilai TRUE maka alihkan halaman sesuai dengan level nya
                if ($data->role_id == '1') {
                    redirect('admin/home');
                } elseif ($data->role_id == '2') {
                    redirect('member/home');
                }
            } else {
                $this->template->load('authentication/layouts/template', 'authentication/login', $data);
            }
        } else {
            $this->template->load('authentication/layouts/template', 'authentication/login', $data);
        }
    }
    public function logout()
    {
        date_default_timezone_set('ASIA/JAKARTA');
        $date = array('last_login' => date('Y-m-d H:i:s'));
        $id = $this->session->userdata('id');
        $this->Auth_model->logout($date, $id);
        $user_data = $this->session->userdata();
        foreach ($user_data as $key => $value) {
            if ($key != '__ci_last_regenerate' && $key != '__ci_vars')
                $this->session->unset_userdata($key);
        }
        $this->session->set_flashdata('alert', '<p class="box-msg">
              <div class="info-box alert-success">
              <div class="info-box-icon">
              <i class="fa fa-check-circle"></i>
              </div>
              <div class="info-box-content" style="font-size:14">
              <b style="font-size: 20px">SUKSES</b><br>Log Out Berhasil</div>
              </div>
              </p>
			');
        redirect('auth/login');
    }



    public function forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Lupa Password';
            $this->template->load('authentication/layouts/template', 'authentication/forgot_password', $data);
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('pengguna', ['email' => $email])->row_array();

            if ($user) {
                $token = rand(9999, 1111);
                $data = [
                    'code' => $token,
                ];

                // print_r($this->input->post('email'));
                // die();

                $this->db->where('email', $this->input->post('email'));
                $this->db->update('pengguna', $data);
                $this->db->affected_rows();

                $this->sendEmail($token);
                redirect('auth/form_code');
            } else {
                $this->session->set_flashdata('alert', '<p class="box-msg">
                <div class="info-box alert-danger">
                <div class="info-box-icon">
                <i class="fa fa-check-circle"></i>
                </div>
                <div class="info-box-content" style="font-size:14">
                <b style="font-size: 20px">GAGAL</b><br>Email tidak terdaftar!</div>
                </div>
                </p>
              ');
                redirect('auth/forgot_password');
            }
        }
    }
    public function sendEmail()
    {
        $token = rand(9999, 1111);
        $data = [
            'code' => $token,
        ];

        $this->db->where('email', $this->input->post('email'));
        $this->db->update('pengguna', $data);
        $this->db->affected_rows();

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'iniakunbuattes@gmail.com',
            'smtp_pass' => 'iniakunbuattes123',
            'mailtype'  => 'html',
            'charset'   => 'iso-8859-1'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('iniakunbuattes@gmail.com', 'Admin PDAM Tugu Tirta');
        // $this->email->to('jayamahendra490@gmail.com');
        $this->email->to($this->input->post('email'));
        $this->email->subject('Lupa Password');
        $this->email->message('Anda akan melakukan reset password, masukkan code ' . $token . ' pada form');
        $result = $this->email->send();

        redirect('auth/form_code');
    }


    public function form_code()
    {
        $this->form_validation->set_rules('code', 'Code', 'required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Form Code';
            $this->template->load('authentication/layouts/template', 'authentication/form_code', $data);
        } else {
            $code = $this->input->post('code');
            $user = $this->db->get_where('pengguna', ['code' => $code])->row_array();

            if ($user) {
                $this->session->set_flashdata('inikode', $code);
                redirect('auth/reset_password');
            } else {
                $this->session->set_flashdata('alert', '<p class="box-msg">
                <div class="info-box alert-danger">
                <div class="info-box-icon">
                <i class="fa fa-check-circle"></i>
                </div>
                <div class="info-box-content" style="font-size:14">
                <b style="font-size: 20px">GAGAL</b><br>Code yang anda masukkan salah!</div>
                </div>
                </p>
              ');
                redirect('auth/form_code');
            }
        }
    }
}
