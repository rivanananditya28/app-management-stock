YG perlu direvisi :
1. penamaan field table											
2. atribut id tidak boleh negatif(unsigned)							
3. field ID pakai tipe data biginteger								
4. field tgl_transaksi diubah menjadi datetime							
5. field kode_barang dan id_lokasi pada tabel transaksi_history dapat didrop saja	
6. data unix dalam tabel stok_barang (tgl_masuk, id_barang & id_lokasi) ->Termasuk input form tambah transaksi							
7. jika ada error data yang telah diinput jangan sampai hilang atau tereset	
8. Transaksi keluar harus memperhatikan tanggal terakhir transaksi masuk
9. Qty tidak boleh 0
10. validasi input Transaksi tanggal_masuk harus sama dengan tanggal hari ini
11. query menghitung saldo per tanggal yang dicari
12. Implementasi FIFO transaksi barang keluar untuk saldo =0
