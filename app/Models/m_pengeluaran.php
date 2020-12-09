<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class m_pengeluaran extends Model {
 
    protected $table = 'transaksi_kas';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_transaksi','rincian_transaksi', 'jumlah','harga','total', 'struk', 'jenis_transaksi']; //atribut tabel
    protected $primaryKey = 'id_transaksi';

    public function get_idotomatis()
    {
        $q = $this->db->query("SELECT MAX(RIGHT(id_transaksi,4)) AS kd_max FROM transaksi_kas ");
        $kd = "";
        if ($q) {
            foreach ($q->getresult() as $k) {
                $tmp = ((int)$k->kd_max) + 1;
                $kd = sprintf("%04s", $tmp);
            }
        } else {
            $kd = "0001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return date('dmY') . $kd;
    }
} 