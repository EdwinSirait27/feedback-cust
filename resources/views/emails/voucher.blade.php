<x-mail::message>
# Congratulation, {{ $feedback->full_name ?? 'Pelanggan Setia' }}!

Thank you for completing the Bali in Bali survey.
You've won a shopping voucher worth:
<x-mail::panel>
Rp {{ number_format($spin->voucher_amount, 0, ',', '.') }}
</x-mail::panel>
Voucher Code:
<div style="text-align:center; font-size:24px; font-weight:bold; letter-spacing:4px; padding:16px; background:#f5f5f5; border-radius:8px; margin:16px 0;">
{{ $spin->voucher_code }}
</div>

Scan the barcode at the cashier:

<div style="text-align:center; margin:16px 0;">
    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode {{ $spin->voucher_code }}" style="max-width:300px;">
</div>

Terms & Conditions:**
- Voucher valid for 1 day from receipt
- Cannot be combined with other promotions
- Valid for one transaction only

See you in Bali!

{{ config('app.name') }}
</x-mail::message>