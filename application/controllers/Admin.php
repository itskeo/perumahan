<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->load->model('ModelAdmin');
        $this->load->model('ModelPemesanan');
    }
    public function index()
    {

        $data['judul'] = 'Admin';
        $data['admin'] = $this->ModelAdmin->cekData(['email' => $this->session->userdata('email')]);
        $data['anggota'] = $this->ModelAdmin->getUserLimit()->result_array();
        $data['buku'] = $this->ModelPemesanan->getBuku()->result_array();
        $data['jumlah_pemesan'] = $this->db->count_all('buku');

        $this->load->view('template/Admin_header', $data);
        $this->load->view('template/Admin_sidebar', $data);
        $this->load->view('template/Admin_topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('template/Admin_footer');
    }
    public function ubahProfil()
    {
        $data['judul'] = 'Ubah Profil';
        $data['admin'] = $this->ModelAdmin->cekData(['email' => $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules(
            'nama',
            'Nama Lengkap',
            'required|trim',
            [
                'required' => 'Nama tidak Boleh Kosong'
            ]
        );
        if ($this->form_validation->run() == false) {
            $this->load->view('template/Admin_header', $data);
            $this->load->view('template/Admin_sidebar', $data);
            $this->load->view('template/Admin_topbar', $data);
            $this->load->view('user/', $data);
            $this->load->view('template/Admin_footer');
        } else {
            $nama = $this->input->post('nama', true);
            $email = $this->input->post('email', true);
            //jika ada gambar yang akan diupload
            $upload_image = $_FILES['image']['nama'];
            if ($upload_image) {
                $config['upload_path'] = './assets/img/profile/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '3000';
                $config['max_width'] = '1024';
                $config['max_height'] = '1000';
                $config['file_name'] = 'pro' . time();
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('image')) {
                    $gambar_lama = $data['admin']['image'];
                    if ($gambar_lama != 'default.jpg') {
                        unlink(FCPATH . 'assets/img/profile/' . $gambar_lama);
                    }
                    $gambar_baru = $this->upload->data('file_name');
                    $this->db->set('image', $gambar_baru);
                } else {
                }
            }
            $this->db->set('nama', $nama);
            $this->db->where('email', $email);
            $this->db->update('admin');
            $this->session->set_flashdata('pesan', '<div 
class="alert alert-success alert-message" role="alert">Profil 
Berhasil diubah </div>');
            redirect('user');
        }
    }
}
