<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Duitku\Config;
use Duitku\Pop;
use Exception;

class DuitkuController extends Controller
{
    private $duitkuConfig;

    public function __construct()
    {
        $merchantCode = env('DUITKU_MERCHANT_CODE');
        $apiKey = env('DUITKU_API_KEY');
        $isProduction = env('APP_ENV') === 'production';

        $this->duitkuConfig = new Config($apiKey, $merchantCode);
        // $this->duitkuConfig->setSandboxMode(!$isProduction);
        $this->duitkuConfig->setSandboxMode(true);
        $this->duitkuConfig->setSanitizedMode(false);
        $this->duitkuConfig->setDuitkuLogs(false);
    }

    public function createInvoice($billingId, Request $request = null)
    {
        $billing = Billing::findOrFail($billingId);

        if ($request && $request->has('force')) {
            $billing->update(['payment_url' => null, 'payment_reference' => null]);
        }

        if ($billing->payment_url) {
            return redirect($billing->payment_url);
        }

        $paymentAmount      = $billing->final_amount;
        $email              = $billing->student->guardian?->user?->email ?? 'no-email@example.com';
        $phoneNumber        = $billing->student->guardian?->whatsapp ?? '081234567890';
        $productDetails     = "Pembayaran " . $billing->title;
        $merchantOrderId    = $billing->id . '-' . time();
        $customerVaName     = $billing->student->guardian?->full_name ?? $billing->student->full_name;
        $callbackUrl        = route('duitku.callback');
        $returnUrl          = route('duitku.return');
        $expiryPeriod       = 1440; // 24 hours

        $customerDetail = array(
            'firstName'         => $customerVaName,
            'lastName'          => "",
            'email'             => $email,
            'phoneNumber'       => $phoneNumber,
        );

        $item1 = array(
            'name'      => $productDetails,
            'price'     => (int) $paymentAmount,
            'quantity'  => 1
        );

        $itemDetails = array($item1);

        $params = array(
            'paymentAmount'     => (int)$paymentAmount,
            'merchantOrderId'   => $merchantOrderId,
            'productDetails'    => $productDetails,
            'customerVaName'    => $customerVaName,
            'email'             => $email,
            'phoneNumber'       => $phoneNumber,
            'itemDetails'       => $itemDetails,
            'customerDetail'    => $customerDetail,
            'callbackUrl'       => $callbackUrl,
            'returnUrl'         => $returnUrl,
            'expiryPeriod'      => $expiryPeriod
        );

        try {
            $responseDuitkuPop = Pop::createInvoice($params, $this->duitkuConfig);
            $response = json_decode($responseDuitkuPop);

            if (isset($response->paymentUrl)) {
                $billing->update([
                    'payment_url' => $response->paymentUrl,
                    'payment_reference' => $response->reference,
                ]);

                return redirect($response->paymentUrl);
            }

            return back()->with('error', 'Gagal membuat invoice pembayaran: ' . ($response->statusMessage ?? 'Unknown error'));

        } catch (Exception $e) {
            return back()->with('error', 'Error dari Duitku: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            $callback = Pop::callback($this->duitkuConfig);
            $notif = json_decode($callback);

            if ($notif->resultCode == "00") {
                $merchantOrderId = $notif->merchantOrderId;
                $billingId = explode('-', $merchantOrderId)[0];

                $billing = Billing::find($billingId);
                if ($billing && $billing->status !== 'PAID') {
                    $billing->update(['status' => 'PAID']);

                    \App\Models\Payment::create([
                        'billing_id' => $billing->id,
                        'payment_method' => $notif->paymentCode ?? 'DUITKU',
                        'amount' => $notif->amount,
                        'paid_at' => now(),
                    ]);
                }
            }
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function returnUrl()
    {
        return redirect()->route('dashboard')->with('message', 'Proses pembayaran selesai.');
    }
}
