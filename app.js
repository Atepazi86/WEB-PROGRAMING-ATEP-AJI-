function editInventaris(item) {
    const modal = new bootstrap.Modal(document.getElementById('modalInventaris'));
    const modalTitle = document.getElementById('modalTitle');
    const idInput = document.getElementById('inventaris_id');
    const namaBarangInput = document.getElementById('nama_barang');
    const stokInput = document.getElementById('stok');
    const satuanInput = document.getElementById('satuan');
    const keteranganInput = document.getElementById('keterangan');

    modalTitle.textContent = 'Edit Barang Inventaris';
    idInput.value = item.id;
    namaBarangInput.value = item.nama_barang;
    stokInput.value = item.stok;
    satuanInput.value = item.satuan;
    keteranganInput.value = item.keterangan;
    modal.show();
}

// Event listener untuk membersihkan modal inventaris saat ditutup
document.addEventListener('DOMContentLoaded', function() {
    const modalInventaris = document.getElementById('modalInventaris');
    if (modalInventaris) {
        modalInventaris.addEventListener('hidden.bs.modal', () => {
            const form = document.querySelector('#modalInventaris form');
            form.reset();
            document.getElementById('inventaris_id').value = '';
            document.getElementById('modalTitle').textContent = 'Tambah Barang Baru';
        });
    }

    const modalPelanggan = document.getElementById('modalPelanggan');
    if(modalPelanggan) {
        modalPelanggan.addEventListener('show.bs.modal', function (event) {
            // event.relatedTarget adalah elemen yang memicu modal (tombol)
            // Jika modal dipicu oleh tombol selain tombol 'Edit' (yang punya class .btn-warning), maka reset form
            if (!event.relatedTarget || !event.relatedTarget.classList.contains('btn-warning')) {
                document.getElementById('edit_id').value = '';
                document.getElementById('edit_nama').value = '';
                document.getElementById('edit_alamat').value = '';
                document.getElementById('edit_telepon').value = '';
                document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
            }
        });
    }

    const modalLayanan = document.getElementById('modalLayanan');
    if(modalLayanan) {
        modalLayanan.addEventListener('show.bs.modal', function (event) {
            if (!event.relatedTarget || !event.relatedTarget.classList.contains('btn-warning')) {
                document.getElementById('edit_id').value = '';
                document.getElementById('edit_nama_layanan').value = '';
                document.getElementById('edit_harga').value = '';
                document.getElementById('modalTitle').textContent = 'Tambah Layanan';
            }
        });
    }
});

function editPelanggan(id, nama, alamat, telepon) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_alamat').value = alamat;
    document.getElementById('edit_telepon').value = telepon;
    document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
    
    const modal = new bootstrap.Modal(document.getElementById('modalPelanggan'));
    modal.show();
}

function editLayanan(id, nama_layanan, harga) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama_layanan').value = nama_layanan;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('modalTitle').textContent = 'Edit Layanan';
    
    const modal = new bootstrap.Modal(document.getElementById('modalLayanan'));
    modal.show();
} 