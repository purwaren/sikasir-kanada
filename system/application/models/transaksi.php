<?php
/**
* Model for transaksi penjualan
*/
class Transaksi extends Model 
{
    /**
    *Model constructor
    */
    function Transaksi()
    {
        parent::Model();
    }
    /**
    *Menyimpan transaksi
    */
    function add_transaksi($data)
    {
        $query = $this->db->get_where('transaksi_penjualan',array(
        	'id_transaksi'=>$data['id_transaksi'],
        	//'kassa'=>$data['kassa']
        ));
        if($query->num_rows() > 0)
        {
            return FALSE;
        }
        else
        {
            $this->db->insert('transaksi_penjualan',$data);
            return TRUE;
        }
    }
    /**
    * Get transaksi from specific range of date
    */
    function get_transaksi($from,$to)
    {
        $this->db->select('tp.*,itp.id_barang,sum(itp.qty) as qty,itp.diskon as disc_item')->from('transaksi_penjualan tp');
        $this->db->join('item_transaksi_penjualan itp', 'tp.id_transaksi = itp.id_transaksi','left')->where(array('tp.tanggal >='=>$from, 'tp.tanggal <='=>$to,'itp.id_barang !='=> 'NULL'));
        return $this->db->group_by('itp.id_transaksi, itp.id_barang')->get();
    }
    /**
    * hapus transaksi berdasar id transaksinya
    */
    function remove($id_transaksi)
    {
        return $this->db->where('id_transaksi',$id_transaksi)->delete('transaksi_penjualan');
    }
    /**
    *Ambil data kasir dalam transaksi
    */
    function get_kasir($tanggal)
    {
        $query = 'select * from transaksi_penjualan  where tanggal="'.$tanggal.'" group by id_kasir';
        return $this->db->query($query);
    }
    /**
    *Ambil data terakhir transaksi
    */
    function last_transaksi($id_transaksi)
    {
        $query = 'select time(from_unixtime(substr(id_transaksi,1,10))) as jam,tp.* from transaksi_penjualan tp where tp.id_transaksi="'.$id_transaksi.'"';
        return $this->db->query($query);
    }
    /**
    *Ambil transaksi plus item2nya yang terjadi dalam satu hari, urutkan berdasarkan jam transaksi
    */
    function trans_a_day($date)
    {
        $query = 'select time(from_unixtime(substr(transaksi_penjualan.id_transaksi,1,10))) as jam_transaksi, transaksi_penjualan.*,id_barang,sum(qty) as qty,item_transaksi_penjualan.diskon as diskon_item, harga_jual 
                from transaksi_penjualan left join item_transaksi_penjualan on transaksi_penjualan.id_transaksi = item_transaksi_penjualan.id_transaksi 
                where tanggal ="'.$date.'"
                group by jam_transaksi,id_barang
                order by jam_transaksi asc';
        return $this->db->query($query);
    }
    /**
    *Ambil transaksi dalam satu hari berdasarkan kode BON
    */
    function trans_based_bon($tanggal)
    {
        $query = 'select * from (select tp.id_transaksi,tp.diskon, time( from_unixtime( substr(tp.id_transaksi,1,10)) ) AS jam_transaksi, total, count(distinct id_barang) as jml_item from transaksi_penjualan as tp left join item_transaksi_penjualan as itp
                            on tp.id_transaksi = itp.id_transaksi where tanggal="'.$tanggal.'" group by tp.id_transaksi) as tab1
                left join (select itp.id_transaksi,b.harga,sum(itp.diskon/100*b.harga*itp.qty) as rupiah_diskon 
                            from item_transaksi_penjualan itp left join barang b on  itp.id_barang = b.id_barang left join transaksi_penjualan tp on itp.id_transaksi = tp.id_transaksi 
                            where tp.tanggal="'.$tanggal.'" group by itp.id_transaksi) as tab2
                on tab1.id_transaksi=tab2.id_transaksi';
        return $this->db->query($query);
    }
    /**
    * Ambil nominal total penjualan dalam satu hari   
    */
    function total_sales_a_day($date)
    {
        $query = 'select sum(total) as temp_sales from transaksi_penjualan where tanggal="'.$date.'" and kassa = '.$this->session->userdata('no_kassa');
        return $this->db->query($query);
    }
    /**
     * Total diskon dalam satu hari
     */
    function total_disc_a_day($date)
    {
    	$sql = 'select sum((t2.total*diskon/100)+diskon_item) as total_diskon from transaksi_penjualan t1 
		left join 
		(select id_transaksi, sum(dt.qty*harga*(1-dt.diskon/100)) as total,sum(dt.diskon/100*harga) as diskon_item from item_transaksi_penjualan dt left join barang b on dt.id_barang=b.id_barang group by id_transaksi) t2
		on t1.id_transaksi = t2.id_transaksi where t1.tanggal="'.$date.'"';
    	return $this->db->query($sql);
    }
    /**
    * ambil total item terjual per hari dalam satu bulan
    */
    function total_qty_sales($month,$year)
    {
        $query = 'select day(tp.tanggal) as tgl, sum(qty) as total from transaksi_penjualan tp 
        left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi
        where month(tp.tanggal) = "'.$month.'" and year(tp.tanggal)="'.$year.'" group by tp.tanggal order by tgl asc';
        return $this->db->query($query);
    }
    /**
    * ambil total diskon per hari dalam satu bulan
    */
    function total_disc_daily($month,$year)
    {
        $query = 'select tgl,ceil((sum(diskon_item)+sum(diskon_all))/100)*100 as total_diskon from (select day(tanggal)as tgl,sum(itp.diskon/100*b.harga*itp.qty) as diskon_item, ceil(((tp.total*(tp.diskon/100))/(1-tp.diskon/100))/100)*100 as diskon_all
                    from item_transaksi_penjualan itp left join barang b on  itp.id_barang = b.id_barang left join transaksi_penjualan tp on itp.id_transaksi = tp.id_transaksi 
                    where month(tanggal)="'.$month.'" and year(tanggal)="'.$year.'" group by itp.id_transaksi)as disc group by tgl';
        return $this->db->query($query);
    }
    /**
    *ambil penjualan sementara per kode kelompok barang
    */
    function total_qty_sales_by_cat($date)
    {
        $query = 'select barang.kelompok_barang, sum(penjualan.qty) as total_jual 
                    from 
                    (select itp.* from transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi where tanggal="'.$date.'" and kassa = '.$this->session->userdata('no_kassa').')
                    as penjualan 
                  left join barang on penjualan.id_barang = barang.id_barang 
                  group by barang.kelompok_barang';
        return $this->db->query($query);
    }
    /**
    *Akumulasi penjualan hrian, per kode label
    *opsi 1 -> per kode label
    *opsi 2 -> per kelompok barang
    */
    function acc_sales_a_day($date,$opsi)
    {
        if($opsi==1)
        {
            $query = 'select b.*, trans.jml_terjual from (select id_barang,sum(qty) as jml_terjual 
                    from transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi
                    where tanggal = "'.$date.'" group by id_barang)
                    as trans left join barang b on trans.id_barang = b.id_barang';
        }
        else if($opsi==2)
        {
            $query = 'select b.kelompok_barang, sum(trans.jml_terjual) as acc_terjual from (select id_barang,sum(qty) as jml_terjual 
                    from transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi 
                    where tanggal = "'.$date.'" group by id_barang)as trans left join barang b on trans.id_barang = b.id_barang group by b.kelompok_barang';
        }
        return $this->db->query($query);
    }
    /**
    *Akumulasi penjualan bulanan
    */
    function acc_sales_a_month($kb,$month,$year)
    {
        $query = 'select barang.kelompok_barang,day(trans_month.tanggal) as tgl, sum(trans_month.qty)as jumlah from 
            (select itp.id_barang,itp.qty,tp.tanggal from transaksi_penjualan tp 
            left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi where month(tanggal)="'.$month.'" and year(tanggal)="'.$year.'") as trans_month 
            left join barang on trans_month.id_barang=barang.id_barang where barang.kelompok_barang="'.$kb.'" group by trans_month.tanggal, barang.kelompok_barang ';
        return $this->db->query($query);
    }
    /**
    *Ambil bulan terjadinya transaksi
    */
    function month_of_trans()
    {
        $query = 'select month(tanggal) as bulan from transaksi_penjualan group by bulan';
        return $this->db->query($query);
    }
    /**
    *Ambil tahun terjadinya transaksi
    */
    function year_of_trans()
    {
        $query = 'select year(tanggal) as tahun from transaksi_penjualan group by tahun';
        return $this->db->query($query);
    }
    function search_sales($opsi,$data)
    {
        if($opsi == 1)
        {
            if(empty($data['kb_high']))
            {
                $query = 'select penjualan.*, barang.nama, barang.kelompok_barang, barang.mutasi_keluar, barang.harga from 
                            (select tp.id_transaksi,tp.tanggal,tp.total, itp.id_barang, sum(itp.qty) as jumlah_terjual from 
                            transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi 
                            where tp.tanggal = "'.$data['tanggal'].'" group by itp.id_barang) as penjualan 
                            left join barang on penjualan.id_barang = barang.id_barang 
                            where barang.kelompok_barang = "'.$data['kb_low'].'" order by barang.kelompok_barang';
            }
            else
            {
                $query = 'select penjualan.*, barang.nama, barang.kelompok_barang, barang.mutasi_keluar, barang.harga from 
                            (select tp.id_transaksi,tp.tanggal,tp.total, itp.id_barang, sum(itp.qty) as jumlah_terjual from 
                            transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi 
                            where tp.tanggal = "'.$data['tanggal'].'" group by itp.id_barang) as penjualan 
                            left join barang on penjualan.id_barang = barang.id_barang 
                            where barang.kelompok_barang >= "'.$data['kb_low'].'" and barang.kelompok_barang <= "'.$data['kb_high'].'" order by barang.kelompok_barang';
            }
        }
        else if($opsi == 2)
        {
            if(empty($data['ib_low']))
            {
                $query = 'select penjualan.*, barang.nama, barang.kelompok_barang, barang.mutasi_keluar, barang.harga from 
                            (select tp.id_transaksi,tp.tanggal,tp.total, itp.id_barang, sum(itp.qty) as jumlah_terjual from 
                            transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi 
                            where tp.tanggal = "'.$data['tanggal'].'" group by itp.id_barang) as penjualan 
                            left join barang on penjualan.id_barang = barang.id_barang 
                            where penjualan.id_barang = "'.$data['ib_low'].'" order by barang.kelompok_barang';
            }
            else
            {
                $query = 'select penjualan.*, barang.nama, barang.kelompok_barang, barang.mutasi_keluar, barang.harga from 
                            (select tp.id_transaksi,tp.tanggal,tp.total, itp.id_barang, sum(itp.qty) as jumlah_terjual from 
                            transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi = itp.id_transaksi 
                            where tp.tanggal = "'.$data['tanggal'].'" group by itp.id_barang) as penjualan 
                            left join barang on penjualan.id_barang = barang.id_barang 
                            where penjualan.id_barang >= "'.$data['ib_low'].'" and penjualan.id_barang <= "'.$data['ib_high'].'" order by barang.kelompok_barang';
            }
        }
        return $this->db->query($query);
    }
    /**
    *Mengambil data omset perhari dalam satu bulan untuk dibuat grafik
    */
    function get_omset($bulan,$tahun)
    {
        $query = 'select day(tp.tanggal) as tgl,sum(tp.total) as omset from transaksi_penjualan tp where month(tp.tanggal)="'.$bulan.'" and year(tp.tanggal) ="'.$tahun.'" group by tanggal';
        return $this->db->query($query);
    }
    /**    
    *Ambil data penjualan berdasarkan kode barang
    */
    function sale_history_ib($id_barang)
    {
        $query = 'select id_barang, tanggal, sum(qty) as qty_sale from transaksi_penjualan tp left join item_transaksi_penjualan itp on tp.id_transaksi=itp.id_transaksi 
                where id_barang="'.$id_barang.'" group by tanggal order by tanggal';
        return $this->db->query($query);
    }
    /**
    *ambil data omset perkaryawan
    */
    function get_omset_karyawan($nik,$bulan,$tahun)
    {
        $query = 'select id_pramuniaga, day(tanggal) as tgl, sum(total) as omset from transaksi_penjualan 
            where id_pramuniaga ="'.$nik.'" and month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" group by id_pramuniaga, tanggal';  
        return $this->db->query($query);
    }
    /*
    *fungsi ambil omset, total item perjual, total customer yang dilayani sekaligus
    */
    function get_sales_karyawan($nik,$bulan,$tahun)
    {
        $query = 'select tab1.*,tab2.total_item from (
                        select day(tanggal) as tgl, sum(total) as omset, count(id_transaksi) as total_customer 
                        from transaksi_penjualan where id_pramuniaga ="'.$nik.'" and month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" group by tanggal) as tab1 
                left join 
                        (select day(tanggal) as tgl, sum(qty) as total_item from transaksi_penjualan tp 
                        left join item_transaksi_penjualan itp on tp.id_transaksi=itp.id_transaksi 
                        where id_pramuniaga = "'.$nik.'" and month(tanggal)="'.$bulan.'" and year(tanggal) = "'.$tahun.'" group by tanggal) as tab2 
                on tab1.tgl=tab2.tgl';
        return $this->db->query($query);
    }
    /**
    *Ambil omset rata2 per hari dalam satu bulan
    * rumus rata2 = omset / jmlh pramuniaga yang masuk
    */
    function get_avg_omset($bulan,$tahun)
    {
        $query = 'select omset1.tgl, omset1.total_customer/omset2.total_pramu as rata2_customer,omset1.omset_total/omset2.total_pramu as rata2_omset,omset2.total_qty/omset2.total_pramu as rata2_qty 
                from  (select day(tanggal) as tgl, count(id_transaksi) as total_customer, sum(total) as omset_total 
                        from transaksi_penjualan where month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" group by tgl) as omset1 
                left join 
                        (select day(tanggal) as tgl, sum(qty) as total_qty,count(distinct id_pramuniaga)as total_pramu 
                        from transaksi_penjualan tp 
                            left join 
                        item_transaksi_penjualan itp 
                            on tp.id_transaksi = itp.id_transaksi where month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" group by tgl) as omset2 
                on omset1.tgl=omset2.tgl';
        return $this->db->query($query);
    }
    /**
    *Ambil jumlah item yang berhasil dijual oleh karyawan
    */
    function item_sales_by_karyawan($nik,$bulan,$tahun)
    {
        $query = 'select tanggal, sum(qty) as total_item from transaksi_penjualan tp left join item_transaksi_penjualan itp 
                on tp.id_transaksi=itp.id_transaksi where id_pramuniaga = "'.$nik.'" and month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" 
                group by tanggal';
        return $this->db->query($query);
    }
    /**
    *Ambil jumlah customer yang berhasil dilayani karyawan
    */
    function customer_serve_by_karyawan($nik,$bulan,$tahun)
    {
        $query = 'select tanggal, count(id_transaksi) as total_customer from transaksi_penjualan 
                where id_pramuniaga="'.$nik.'" and month(tanggal)="'.$bulan.'" and year(tanggal)="'.$tahun.'" 
                group by tanggal';
        return $this->db->query($query);
    }
    /**
    * Check apakah sudah ada transaksi dengan id tsb
    */
    function trans_exist($id_transaksi)
    {
        $query = $this->db->get_where('transaksi_penjualan',array('id_transaksi'=>$id_transaksi));
        if($query->num_rows() > 1)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}
//End of Transaksi.php
//Location: system/application/models