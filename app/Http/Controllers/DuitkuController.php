<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Services\SecurePaymentService;
use Illuminate\Http\Request;
use Duitku\Config;
use Duitku\Pop;
use Exception;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    private $duitkuConfig;
    protected $securePaymentService;

    public function __construct(SecurePaymentService $securePaymentService)
    {
        $merchantCode = config('payment.duitku.merchant_code');
        $apiKey = config('payment.duitku.merchant_key');
        $isSandbox = config('payment.duitku.sandbox', false);

        $this->duitkuConfig = new Config($apiKey, $merchantCode);
        $this->duitkuConfig->setSandboxMode($isSandbox);
        $this->duitkuConfig->setSanitizedMode(false);
        $this->duitkuConfig->setDuitkuLogs(false);
        
        $this->securePaymentService = $securePaymentService;
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
        $expiryPeriod = 1440;

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
            Log::error('Duitku error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Terjadi kesalahan dalam memproses pembayaran');
        }
    }

    public function callback(Request $request)
    {
        Log::info('Duitku callback received', ['payload' => $request->all()]);

        try {
            $callback = Pop::callback($this->duitkuConfig);
            $notif = json_decode($callback);

            Log::info('Duitku callback processed', [
                'resultCode' => $notif->resultCode ?? null,
                'merchantOrderId' => $notif->merchantOrderId ?? null,
                'amount' => $notif->amount ?? null,
            ]);

            $billing = Billing::where('payment_reference', $notif->merchantOrderId)->first();

            if (!$billing) {
                return response()->json(['error' => 'Billing not found'], 404);
            }

            try {
                $payment = $this->securePaymentService->processDuitkuPaymentSecurely([
                    'merchantOrderId' => $notif->merchantOrderId,
                    'amount' => $notif->amount,
                    'reference' => $notif->reference,
                    'resultCode' => $notif->resultCode,
                    'paymentCode' => $notif->paymentCode,
                ]);

                return response()->json(['success' => true, 'payment' => $payment->id]);
            } catch (Exception $e) {
                Log::error('Payment processing failed: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 422);
            }
        } catch (Exception $e) {
            Log::error('Duitku callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function returnUrl()
    {
        return redirect()->route('dashboard')->with('message', 'Proses pembayaran selesai.');
    }
}
