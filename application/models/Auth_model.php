<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * |==============================================================|
 * | Please DO NOT modify this information :                      |
 * |--------------------------------------------------------------|
 * | Author          : Susantokun
 * | Email           : admin@susantokun.com
 * | Filename        : Auth_model.php
 * | Instagram       : @susantokun
 * | Blog            : http://www.susantokun.com
 * | Info            : http://info.susantokun.com
 * | Demo            : http://demo.susantokun.com
 * | Youtube         : http://youtube.com/susantokun
 * | File Created    : Thursday, 12th March 2020 10:34:33 am
 * | Last Modified   : Thursday, 12th March 2020 10:58:39 am
 * |==============================================================|
 */

class Auth_model extends CI_Model
{
    public $table       = 'pengguna';
    public $id          = 'pengguna.pengguna_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function update($data, $id)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
	}
	
	public function get_by_id()
    {
        $id = $this->session->userdata('pengguna_id');
        $this->db->select('
            pengguna.*, roles.role_id AS role_id, roles.keterangan,
        ');
        $this->db->join('roles', 'pengguna.role_id = roles.role_id');
        $this->db->from($this->table);
        $this->db->where($this->id, $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function reg()
    {
      date_default_timezone_set('ASIA/JAKARTA');
      $data = array(
        'email' => $this->input->post('email'),
        'nama' => $this->input->post('name'),
        'asal' => $this->input->post('asal'),
        'handphone' => $this->input->post('handphone'),
        'role_id' => '2',
        'tanggal_daftar' => date('Y-m-d H:i:s'),
        'status_pendaftaran' => '1',
        'password' => get_hash($this->input->post('password'))
      );
      return $this->db->insert($this->table, $data);
    }

    public function login($email, $password)
    {
        $query = $this->db->get_where($this->table, array('email'=>$email, 'password'=>$password));
        return $query->row_array();
    }

    public function check_account($email)
    {
        //cari email lalu lakukan validasi
        $this->db->where('email', $email);
        $query = $this->db->get($this->table)->row();

        //jika bernilai 1 maka user tidak ditemukan
        if (!$query) {
            return 1;
        }
        // //jika bernilai 2 maka user tidak aktif
        // if ($query->activated == 0) {
        //     return 2;
        // }
        //jika bernilai 3 maka password salah
        if (!hash_verified($this->input->post('password'), $query->password)) {
            return 3;
        }

        return $query;
    }

    public function logout($date, $id)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $date);
    }

    
}
