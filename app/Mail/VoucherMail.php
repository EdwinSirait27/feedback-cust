<?php

// namespace App\Mail;

// use App\Models\Feedback;
// use App\Models\FeedbackSpin;
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;
// use Picqer\Barcode\BarcodeGeneratorPNG;

// class VoucherMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public string $barcodeBase64;

//     public function __construct(
//         public Feedback $feedback,
//         public FeedbackSpin $spin,
//     ) {
//         // Generate barcode 128 dari voucher_code, encode ke base64 untuk embed di email
//         $generator = new BarcodeGeneratorPNG();
//         $this->barcodeBase64 = base64_encode(
//             $generator->getBarcode(
//                 $spin->voucher_code,
//                 $generator::TYPE_CODE_128,
//                 3,   // width per bar
//                 60,  // height
//             )
//         );
//     }

//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: 'Selamat! Voucher Belanja Anda dari Bali in Bali',
//         );
//     }

//     public function content(): Content
//     {
//         return new Content(
//             markdown: 'emails.voucher',
//         );
//     }
// }
namespace App\Mail;

use App\Models\Feedback;
use App\Models\FeedbackSpin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;

class VoucherMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $barcodePath;

    public function __construct(
        public Feedback $feedback,
        public FeedbackSpin $spin,
    ) {
        $generator = new BarcodeGeneratorPNG();
        $barcodeContent = $generator->getBarcode(
            $spin->voucher_code,
            $generator::TYPE_CODE_128,
            3,
            60,
        );

        $filename = 'barcodes/' . $spin->voucher_code . '.png';
        Storage::disk('local')->put($filename, $barcodeContent);
        $this->barcodePath = Storage::disk('local')->path($filename);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Voucher Belanja Anda dari Bali in Bali',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.voucher',
            with: [
                'barcodePath' => $this->barcodePath,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->barcodePath)
                ->as('voucher-' . $this->spin->voucher_code . '.png')
                ->withMime('image/png'),
        ];
    }
}