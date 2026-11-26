/* ============================================================
   Perilaku halaman: kirim form lewat fetch, indikator sandi,
   dan tautan reset dari mode demo lokal.
   ============================================================ */
(() => {
    'use strict';

    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrf = meta ? meta.getAttribute('content') : '';

    async function kirim(url, data) {
        const respons = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': csrf,
            },
            body: JSON.stringify(data),
        });

        try {
            return await respons.json();
        } catch (galat) {
            return { ok: false, message: 'Respons server tidak dapat dibaca.' };
        }
    }

    function tampilkanAlert(form, pesan, variasi) {
        const kotak = form.querySelector('[data-form-alert]');
        if (!kotak) {
            return null;
        }

        kotak.hidden = pesan === '';
        kotak.textContent = pesan;
        kotak.classList.remove('alert--error', 'alert--success', 'alert--info');
        kotak.classList.add('alert--' + variasi);

        return kotak;
    }

    document.querySelectorAll('form[data-ajax]').forEach((form) => {
        const tombolKirim = form.querySelector('[type="submit"]');

        form.addEventListener('submit', async (peristiwa) => {
            peristiwa.preventDefault();

            if (form.dataset.sibuk === '1') {
                return;
            }

            form.dataset.sibuk = '1';
            if (tombolKirim) {
                tombolKirim.disabled = true;
            }

            form.querySelectorAll('[aria-invalid="true"]').forEach((el) => {
                el.removeAttribute('aria-invalid');
            });
            tampilkanAlert(form, '', 'info');

            const data = Object.fromEntries(new FormData(form).entries());

            try {
                const hasil = await kirim(form.dataset.ajax || form.action, data);

                if (!hasil.ok) {
                    tampilkanAlert(form, hasil.message || 'Terjadi kesalahan.', 'error');

                    if (hasil.errors) {
                        Object.keys(hasil.errors).forEach((nama) => {
                            const isian = form.elements.namedItem(nama);
                            if (isian && isian.setAttribute) {
                                isian.setAttribute('aria-invalid', 'true');
                            }
                        });
                    }
                    return;
                }

                if (hasil.data && hasil.data.redirect) {
                    window.location.href = hasil.data.redirect;
                    return;
                }

                const kotak = tampilkanAlert(form, hasil.message || 'Berhasil.', 'success');

                if (kotak && hasil.data && hasil.data.reset_url) {
                    const tautan = document.createElement('a');
                    tautan.href = hasil.data.reset_url;
                    tautan.textContent = 'Buka tautan reset';
                    kotak.append(' ');
                    kotak.appendChild(tautan);
                }

                if (form.dataset.reset !== undefined) {
                    form.reset();
                }
            } catch (galat) {
                tampilkanAlert(form, 'Tidak dapat menghubungi server. Coba lagi.', 'error');
            } finally {
                form.dataset.sibuk = '';
                if (tombolKirim) {
                    tombolKirim.disabled = false;
                }
            }
        });
    });

    /* ---------- Kekuatan kata sandi ---------- */
    const labelTingkat = ['Sangat lemah', 'Lemah', 'Cukup', 'Baik', 'Kuat'];

    function hitungKekuatan(nilai) {
        if (nilai === '') {
            return -1;
        }

        let skor = 0;
        if (nilai.length >= 8) {
            skor += 1;
        }
        if (nilai.length >= 12) {
            skor += 1;
        }
        if (/[a-z]/.test(nilai) && /[A-Z]/.test(nilai)) {
            skor += 1;
        }
        if (/\d/.test(nilai)) {
            skor += 1;
        }
        if (/[^A-Za-z0-9]/.test(nilai)) {
            skor += 1;
        }

        return Math.min(skor, 4);
    }

    document.querySelectorAll('[data-meter]').forEach((meter) => {
        const nama = meter.dataset.untuk;
        const isian = document.querySelector('input[name="' + nama + '"]');
        const teks = meter.querySelector('[data-meter-text]');

        if (!isian || !teks) {
            return;
        }

        const perbarui = () => {
            const nilai = isian.value;
            const tingkat = hitungKekuatan(nilai);

            meter.dataset.level = String(Math.max(tingkat, 0));
            meter.dataset.terisi = nilai === '' ? '0' : '1';
            teks.textContent = tingkat < 0 ? 'Belum diisi' : labelTingkat[tingkat];
        };

        isian.addEventListener('input', perbarui);
        perbarui();
    });
})();
