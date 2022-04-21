<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidSignatureException;
use App\Models\User;
use Elliptic\EC;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use kornrunner\Keccak;

class VerifySignatureAndAuthenticateUserController
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'address' => 'required',
            'signature' => 'required',
        ]);

        $address = $this->verifySignature(
            session()->get('nonce'),
            $request->input('signature'),
            $request->input('address'),
        );

        $user = User::query()
            ->where('address', '=', $address)
            ->first();

        if (! $user) {
            $user = User::create([
                'address' => $address,
            ]);
        }

        Auth::login($user);

        return $user;
    }

    /** Based on code taken from: https://github.com/simplito/elliptic-php */
    private function verifySignature($message, $signature, $address): string
    {
        $recoveryId = ord(hex2bin(substr($signature, 130, 2))) - 27;

        if ($recoveryId != ($recoveryId & 1)) {
            throw new InvalidSignatureException();
        }

        $messageLength = strlen($message);
        $hash = Keccak::hash("\x19Ethereum Signed Message:\n{$messageLength}{$message}", 256);
        $publicKey = (new EC('secp256k1'))->recoverPubKey($hash, [
            "r" => substr($signature, 2, 64),
            "s" => substr($signature, 66, 64),
        ], $recoveryId);

        $recoveredAddress = strtolower("0x".substr(Keccak::hash(substr(hex2bin($publicKey->encode("hex")), 1), 256), 24));

        if (strtolower($address) !== $recoveredAddress) {
            throw new InvalidSignatureException();
        }

        return $recoveredAddress;
    }
}
