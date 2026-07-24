<x-mail::message>
# Selamat, {{ $feedback->full_name ?? 'Pelanggan Setia' }}!

Terima kasih sudah mengisi survey **Bali in Bali**.  
Kamu berhasil memenangkan voucher belanja senilai:

<x-mail::panel>
## Rp {{ number_format($spin->voucher_amount, 0, ',', '.') }}
</x-mail::panel>

**Kode Voucher Kamu:**

<div style="text-align:center; font-size:24px; font-weight:bold; letter-spacing:4px; padding:16px; background:#f5f5f5; border-radius:8px; margin:16px 0;">
{{ $spin->voucher_code }}
</div>

**Scan barcode di kasir:**

<div style="text-align:center; margin:16px 0;">
    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode {{ $spin->voucher_code }}" style="max-width:300px;">
</div>

**Syarat & Ketentuan:**
- Voucher berlaku 30 hari sejak diterima
- Tidak dapat digabungkan dengan promo lain
- Hanya berlaku untuk 1x transaksi

Sampai jumpa di Bali in Bali!

{{ config('app.name') }}
</x-mail::message>